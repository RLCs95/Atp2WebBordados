<?php
require_once 'bootstrap.php';

// 1. Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// 2. Verifica se o usuário é o 'dono'
$nivel_usuario = $_SESSION['nivel'] ?? 'funcionario';
if ($nivel_usuario !== 'dono') {
    $_SESSION['msg_erro'] = "Acesso negado. Você não tem permissão para gerenciar usuários.";
    header('Location: dashboard.php');
    exit;
}

// --- LÓGICA CRUD (CREATE, READ, UPDATE, DELETE) ---
$acao = $_GET['acao'] ?? '';
$id = $_GET['id'] ?? 0;
$msg = '';

// Adicionar Usuário
if ($acao === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $nivel = $_POST['nivel'];

    if (empty($nome) || empty($email) || empty($senha) || !in_array($nivel, ['gerente', 'funcionario'])) {
        $msg = 'Erro: Todos os campos são obrigatórios e o nível deve ser válido.';
    } else {
        $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
        $stmt = $conn->prepare('INSERT INTO usuarios (nome, email, senha, nivel) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $nome, $email, $senhaHash, $nivel);
        if ($stmt->execute()) $msg = 'Usuário cadastrado com sucesso!';
        else $msg = 'Erro ao cadastrar o usuário. O e-mail já pode existir.';
    }
}

// Editar Usuário
if ($acao === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $nivel = $_POST['nivel'];

    if (empty($nome) || empty($email) || !in_array($nivel, ['gerente', 'funcionario'])) {
        $msg = 'Erro: Nome, e-mail e nível são obrigatórios.';
    } else {
        if (!empty($senha)) {
            $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
            $stmt = $conn->prepare('UPDATE usuarios SET nome=?, email=?, senha=?, nivel=? WHERE id=? AND nivel != "dono"');
            $stmt->bind_param('ssssi', $nome, $email, $senhaHash, $nivel, $id);
        } else {
            $stmt = $conn->prepare('UPDATE usuarios SET nome=?, email=?, nivel=? WHERE id=? AND nivel != "dono"');
            $stmt->bind_param('sssi', $nome, $email, $nivel, $id);
        }
        if ($stmt->execute()) $msg = 'Usuário atualizado com sucesso!';
        else $msg = 'Erro ao atualizar o usuário.';
    }
}

// Excluir Usuário
if ($acao === 'del' && $id) {
    if ($id == $_SESSION['user_id']) {
        $msg = 'Erro: Você não pode excluir sua própria conta de administrador.';
    } else {
        $stmt = $conn->prepare('DELETE FROM usuarios WHERE id=? AND nivel != "dono"');
        $stmt->bind_param('i', $id);
        if ($stmt->execute() && $stmt->affected_rows > 0) $msg = 'Usuário excluído com sucesso!';
        else $msg = 'Erro ao excluir o usuário ou usuário não encontrado.';
    }
}

// --- BUSCA DE DADOS PARA EXIBIÇÃO ---
$usuarios_result = $conn->query("SELECT id, nome, email, nivel FROM usuarios WHERE id != {$_SESSION['user_id']} ORDER BY nome ASC");

$usuarioEdit = null;
if ($acao === 'edit' && $id) {
    $stmt = $conn->prepare("SELECT id, nome, email, nivel FROM usuarios WHERE id = ? AND nivel != 'dono'");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $usuarioEdit = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Bordado Industrial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<?php include 'partials/navbar.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Gerenciar Usuários</h2>
        <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar ao Dashboard</a>
    </div>

    <?php if ($msg): ?><div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <h4><?php echo $usuarioEdit ? 'Editar Usuário' : 'Novo Usuário'; ?></h4>
            <form method="POST" action="?acao=<?php echo $usuarioEdit ? 'edit&id=' . $usuarioEdit['id'] : 'add'; ?>">
                <div class="mb-2"><label for="nome" class="form-label">Nome</label><input type="text" class="form-control" id="nome" name="nome" required value="<?php echo htmlspecialchars($usuarioEdit['nome'] ?? ''); ?>"></div>
                <div class="mb-2"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($usuarioEdit['email'] ?? ''); ?>"></div>
                <div class="mb-2"><label for="senha" class="form-label">Senha</label><input type="password" class="form-control" id="senha" name="senha" <?php echo $usuarioEdit ? '' : 'required'; ?>><?php if ($usuarioEdit): ?><small class="form-text text-muted">Deixe em branco para não alterar.</small><?php endif; ?></div>
                <div class="mb-2">
                    <label for="nivel" class="form-label">Nível de Acesso</label>
                    <select class="form-select" id="nivel" name="nivel" required>
                        <option value="funcionario" <?php echo (isset($usuarioEdit['nivel']) && $usuarioEdit['nivel'] == 'funcionario') ? 'selected' : ''; ?>>Funcionário</option>
                        <option value="gerente" <?php echo (isset($usuarioEdit['nivel']) && $usuarioEdit['nivel'] == 'gerente') ? 'selected' : ''; ?>>Gerente</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
                <?php if ($usuarioEdit): ?><a href="usuarios.php" class="btn btn-secondary ms-2">Cancelar Edição</a><?php endif; ?>
            </form>
        </div>
        <div class="col-md-8">
            <h4>Lista de Usuários</h4>
            <table class="table table-striped table-hover">
                <thead><tr><th>ID</th><th>Nome</th><th>Email</th><th>Nível</th><th>Ações</th></tr></thead>
                <tbody>
                <?php while ($u = $usuarios_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['nome']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><span class="badge bg-secondary"><?php echo ucfirst(htmlspecialchars($u['nivel'])); ?></span></td>
                        <td>
                            <a href="?acao=edit&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                            <a href="?acao=del&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')"><i class="bi bi-trash"></i></a>
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