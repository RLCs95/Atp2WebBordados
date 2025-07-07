<?php
// 1. Inclui o arquivo de inicialização central.
require_once 'bootstrap.php';

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // 3. Consulta usando MySQLi (padrão do projeto)
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // 4. Verificação de senha SEGURA usando password_verify
    // Isso requer que as senhas no banco sejam salvas com password_hash()
    if ($user && password_verify($senha, $user['senha'])) {
        // 5. Define as variáveis de sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['usuario'] = $user['nome'];
        $_SESSION['nivel'] = $user['nivel'];

        header("Location: dashboard.php");
        exit();
    } else {
        $erro = "Usuário ou senha inválidos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bordado Industrial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card p-4 shadow-sm">
                <h3 class="mb-3 text-center">Login</h3>
                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger"><?php echo $erro; ?></div>
                <?php endif; ?>
                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>