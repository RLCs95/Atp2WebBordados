<?php
require_once 'bootstrap.php';

// 1. Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// 2. Verifica o nível de permissão (apenas 'dono' e 'gerente' podem acessar)
$nivel_usuario = $_SESSION['nivel'] ?? 'funcionario';
if ($nivel_usuario !== 'dono' && $nivel_usuario !== 'gerente') {
    // Redireciona para o dashboard com uma mensagem de erro
    $_SESSION['msg_erro'] = "Você não tem permissão para acessar esta página.";
    header('Location: dashboard.php');
    exit;
}

// --- LÓGICA CRUD (CREATE, READ, UPDATE, DELETE) ---
$acao = $_GET['acao'] ?? '';
$id = $_GET['id'] ?? 0;
$msg = '';

// Adicionar Produto
if ($acao === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $categoria = $_POST['categoria'];

    $stmt = $conn->prepare('INSERT INTO produtos (nome, descricao, preco, categoria) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssds', $nome, $descricao, $preco, $categoria);
    if ($stmt->execute()) $msg = 'Produto cadastrado com sucesso!';
    else $msg = 'Erro ao cadastrar o produto.';
}

// Editar Produto
if ($acao === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $categoria = $_POST['categoria'];

    $stmt = $conn->prepare('UPDATE produtos SET nome=?, descricao=?, preco=?, categoria=? WHERE id=?');
    $stmt->bind_param('ssdsi', $nome, $descricao, $preco, $categoria, $id);
    if ($stmt->execute()) $msg = 'Produto atualizado com sucesso!';
    else $msg = 'Erro ao atualizar o produto.';
}

// Excluir Produto
if ($acao === 'del' && $id) {
    $stmt = $conn->prepare('DELETE FROM produtos WHERE id=?');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) $msg = 'Produto excluído com sucesso!';
    else $msg = 'Erro ao excluir o produto.';
}

// --- BUSCA DE DADOS PARA EXIBIÇÃO ---
$produtos_result = $conn->query("SELECT * FROM produtos ORDER BY id DESC");

$produtoEdit = null;
if ($acao === 'edit' && $id) {
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $produtoEdit = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Bordado Industrial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<?php include 'partials/navbar.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Gerenciar Produtos</h2>
        <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar ao Dashboard</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <h4><?php echo $produtoEdit ? 'Editar Produto' : 'Novo Produto'; ?></h4>
            <form method="POST" action="?acao=<?php echo $produtoEdit ? 'edit&id=' . $produtoEdit['id'] : 'add'; ?>">
                <div class="mb-2">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" required value="<?php echo htmlspecialchars($produtoEdit['nome'] ?? ''); ?>">
                </div>
                <div class="mb-2">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" required><?php echo htmlspecialchars($produtoEdit['descricao'] ?? ''); ?></textarea>
                </div>
                <div class="mb-2">
                    <label for="preco" class="form-label">Preço (R$)</label>
                    <input type="number" step="0.01" class="form-control" id="preco" name="preco" required value="<?php echo htmlspecialchars($produtoEdit['preco'] ?? ''); ?>">
                </div>
                <div class="mb-2">
                    <label for="categoria" class="form-label">Categoria</label>
                    <input type="text" class="form-control" id="categoria" name="categoria" required value="<?php echo htmlspecialchars($produtoEdit['categoria'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
                <?php if ($produtoEdit): ?>
                    <a href="listar.php" class="btn btn-secondary ms-2">Cancelar Edição</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="col-md-8">
            <h4>Lista de Produtos</h4>
            <table class="table table-striped table-hover">
                <thead><tr><th>ID</th><th>Nome</th><th>Preço</th><th>Categoria</th><th>Ações</th></tr></thead>
                <tbody>
                <?php while ($p = $produtos_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td><?php echo htmlspecialchars($p['nome']); ?></td>
                        <td>R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($p['categoria']); ?></td>
                        <td>
                            <a href="?acao=edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                            <a href="?acao=del&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')"><i class="bi bi-trash"></i></a>
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