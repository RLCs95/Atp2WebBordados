<?php
// Iniciar a sessão para podermos usar mensagens flash (opcional, mas bom para feedback)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- 1. Conexão com o Banco de Dados ---
// Usa a conexão MySQLi do arquivo de configuração na mesma pasta.
require_once 'database.php';

// --- 2. Verificar se um usuário 'dono' já existe ---
$result = $conn->query("SELECT COUNT(*) FROM usuarios WHERE nivel = 'dono'");
$donoExists = $result->fetch_row()[0] > 0;
$result->close();

// --- 3. Lógica de Redirecionamento e Processamento do Formulário ---
$mensagem_erro = '';

// Se um 'dono' já existe, redireciona imediatamente.
if ($donoExists) {
    header('Location: ../../login.php?status=setup_completo');
    exit;
}

// Se o método for POST, significa que o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificação de segurança: se um 'dono' já existe, não permitir a criação
    if ($donoExists) {
        header('Location: ../../login.php?status=setup_completo');
        exit;
    }

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        $mensagem_erro = "Todos os campos são obrigatórios.";
    } else {
        // Hash da senha - NUNCA armazene senhas em texto puro
        $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

        // Inserir no banco de dados usando MySQLi com prepared statements
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, nivel) VALUES (?, ?, ?, 'dono')");
        $stmt->bind_param('sss', $nome, $email, $senhaHash);
        $stmt->execute();

        // Redirecionar para a página de login com uma mensagem de sucesso
        header('Location: ../../login.php?status=dono_criado');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração Inicial - Criar Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>
<body class="tema-admin d-flex align-items-center justify-content-center vh-100">

    <main class="container" style="max-width: 500px;">
        <div class="card">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h2 class="card-title">Configuração Inicial</h2>
                    <p class="text-muted">Crie a conta principal do administrador do sistema.</p>
                </div>

                <?php if (!empty($mensagem_erro)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($mensagem_erro) ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Criar Administrador</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
</html>