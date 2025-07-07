<?php
// 1. Inclui o arquivo de inicialização central.
require_once 'bootstrap.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php'); // 2. Caminho de redirecionamento corrigido.
    exit;
}

// CRUD de clientes
$acao = $_GET['acao'] ?? '';
$id = $_GET['id'] ?? '';
$msg = '';

// Adicionar cliente
if ($acao === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $stmt = $conn->prepare('INSERT INTO clientes (nome, email, telefone) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $nome, $email, $telefone);
    $stmt->execute();
    $msg = 'Cliente cadastrado!';
}
// Editar cliente
if ($acao === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $stmt = $conn->prepare('UPDATE clientes SET nome=?, email=?, telefone=? WHERE id=?');
    $stmt->bind_param('sssi', $nome, $email, $telefone, $id);
    $stmt->execute();
    $msg = 'Cliente atualizado!';
}
// Excluir cliente
if ($acao === 'del' && $id) {
    $stmt = $conn->prepare('DELETE FROM clientes WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $msg = 'Cliente excluído!';
}
// Buscar clientes
$clientes = $conn->query('SELECT * FROM clientes ORDER BY id DESC');
// Se for editar, busca o cliente
$clienteEdit = null;
if ($acao === 'edit' && $id) {
    // 3. CORREÇÃO DE SEGURANÇA: Usando prepared statements para evitar SQL Injection.
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $clienteEdit = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Bordado Industrial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> <!-- Adicionado para usar ícones -->
</head>
<body class="bg-light">
<?php include 'partials/navbar.php'; ?>
<div class="container mt-5">
    <!-- Cabeçalho da página com título e botão de voltar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Clientes</h2>
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar ao Dashboard
        </a>
    </div>
    <?php if ($msg): ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-4">
            <h4><?php echo $clienteEdit ? 'Editar Cliente' : 'Novo Cliente'; ?></h4>
            <form method="POST" action="?acao=<?php echo $clienteEdit ? 'edit&id=' . $clienteEdit['id'] : 'add'; ?>">
                <div class="mb-2">
                    <label class="form-label">Nome</label>
                    <input type="text" class="form-control" name="nome" required value="<?php echo $clienteEdit['nome'] ?? ''; ?>">
                </div>
                <div class="mb-2">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required value="<?php echo $clienteEdit['email'] ?? ''; ?>">
                </div>
                <div class="mb-2">
                    <label class="form-label">Telefone</label>
                    <input type="text" class="form-control" name="telefone" value="<?php echo $clienteEdit['telefone'] ?? ''; ?>">
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
                <?php if ($clienteEdit): ?>
                    <a href="clientes.php" class="btn btn-secondary ms-2">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="col-md-8">
            <h4>Lista de Clientes</h4>
            <table class="table table-striped">
                <thead><tr><th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Ações</th></tr></thead>
                <tbody>
                <?php while ($c = $clientes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $c['id']; ?></td>
                        <td><?php echo htmlspecialchars($c['nome']); ?></td>
                        <td><?php echo htmlspecialchars($c['email']); ?></td>
                        <td><?php echo htmlspecialchars($c['telefone']); ?></td>
                        <td>
                            <a href="?acao=edit&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="?acao=del&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir este cliente?')">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
