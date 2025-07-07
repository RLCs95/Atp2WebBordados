<?php
// 1. Inclui o arquivo de inicialização e verifica a sessão do usuário.
require_once 'bootstrap.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// --- PREPARAÇÃO DE DADOS ---
// Busca todos os clientes para preencher o menu <select> no formulário.
$clientes_result = $conn->query("SELECT id, nome FROM clientes ORDER BY nome ASC");
$clientes_options = [];
while ($cliente = $clientes_result->fetch_assoc()) {
    $clientes_options[] = $cliente;
}

// --- LÓGICA CRUD (CREATE, READ, UPDATE, DELETE) ---
$acao = $_GET['acao'] ?? '';
$id = $_GET['id'] ?? 0;
$msg = '';

// Adicionar Pedido
if ($acao === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $descricao = $_POST['descricao'];
    $data_pedido = $_POST['data_pedido'];
    $valor = $_POST['valor'];

    $stmt = $conn->prepare('INSERT INTO pedidos (cliente_id, descricao, data_pedido, valor) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('issd', $cliente_id, $descricao, $data_pedido, $valor);
    if ($stmt->execute()) {
        $msg = 'Pedido cadastrado com sucesso!';
    } else {
        $msg = 'Erro ao cadastrar o pedido.';
    }
}

// Editar Pedido
if ($acao === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
    $cliente_id = $_POST['cliente_id'];
    $descricao = $_POST['descricao'];
    $data_pedido = $_POST['data_pedido'];
    $valor = $_POST['valor'];

    $stmt = $conn->prepare('UPDATE pedidos SET cliente_id=?, descricao=?, data_pedido=?, valor=? WHERE id=?');
    $stmt->bind_param('issdi', $cliente_id, $descricao, $data_pedido, $valor, $id);
    if ($stmt->execute()) {
        $msg = 'Pedido atualizado com sucesso!';
    } else {
        $msg = 'Erro ao atualizar o pedido.';
    }
}

// Excluir Pedido
if ($acao === 'del' && $id) {
    $stmt = $conn->prepare('DELETE FROM pedidos WHERE id=?');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $msg = 'Pedido excluído com sucesso!';
    } else {
        $msg = 'Erro ao excluir o pedido. Verifique se ele não está sendo usado.';
    }
}

// --- BUSCA DE DADOS PARA EXIBIÇÃO ---
// Busca todos os pedidos, juntando com a tabela de clientes para obter o nome.
$pedidos_result = $conn->query("
    SELECT p.id, p.descricao, p.data_pedido, p.valor, c.nome as cliente_nome
    FROM pedidos p
    JOIN clientes c ON p.cliente_id = c.id
    ORDER BY p.id DESC
");

// Se a ação for 'edit', busca os dados do pedido específico para preencher o formulário.
$pedidoEdit = null;
if ($acao === 'edit' && $id) {
    $stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $pedidoEdit = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - Bordado Industrial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<?php include 'partials/navbar.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Gerenciar Pedidos</h2>
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar ao Dashboard
        </a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <h4><?php echo $pedidoEdit ? 'Editar Pedido' : 'Novo Pedido'; ?></h4>
            <form method="POST" action="?acao=<?php echo $pedidoEdit ? 'edit&id=' . $pedidoEdit['id'] : 'add'; ?>">
                <div class="mb-2">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select class="form-select" id="cliente_id" name="cliente_id" required>
                        <option value="">Selecione um cliente</option>
                        <?php foreach ($clientes_options as $cliente): ?>
                            <option value="<?php echo $cliente['id']; ?>" <?php echo (isset($pedidoEdit['cliente_id']) && $pedidoEdit['cliente_id'] == $cliente['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cliente['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" required><?php echo htmlspecialchars($pedidoEdit['descricao'] ?? ''); ?></textarea>
                </div>
                <div class="mb-2">
                    <label for="data_pedido" class="form-label">Data do Pedido</label>
                    <input type="date" class="form-control" id="data_pedido" name="data_pedido" required value="<?php echo htmlspecialchars($pedidoEdit['data_pedido'] ?? date('Y-m-d')); ?>">
                </div>
                <div class="mb-2">
                    <label for="valor" class="form-label">Valor (R$)</label>
                    <input type="number" step="0.01" class="form-control" id="valor" name="valor" required value="<?php echo htmlspecialchars($pedidoEdit['valor'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
                <?php if ($pedidoEdit): ?>
                    <a href="pedidos.php" class="btn btn-secondary ms-2">Cancelar Edição</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="col-md-8">
            <h4>Lista de Pedidos</h4>
            <table class="table table-striped table-hover">
                <thead><tr><th>ID</th><th>Cliente</th><th>Descrição</th><th>Data</th><th>Valor</th><th>Ações</th></tr></thead>
                <tbody>
                <?php while ($p = $pedidos_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td><?php echo htmlspecialchars($p['cliente_nome']); ?></td>
                        <td><?php echo htmlspecialchars($p['descricao']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($p['data_pedido'])); ?></td>
                        <td>R$ <?php echo number_format($p['valor'], 2, ',', '.'); ?></td>
                        <td>
                            <a href="?acao=edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                            <a href="?acao=del&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este pedido?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>