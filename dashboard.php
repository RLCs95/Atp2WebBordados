<?php
session_start();
// 1. Verificação de sessão e correção do caminho de redirecionamento
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php'); // Caminho corrigido
    exit;
}
// 2. Obtenção do nível de acesso para controle de permissões
$nivel_usuario = $_SESSION['nivel'] ?? 'funcionario';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bordado Industrial</title>
    <!-- O link para estilo.css foi removido pois o arquivo não foi fornecido. Se existir, pode ser adicionado novamente. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> <!-- 3. Adicionando ícones para o menu -->
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Bordado Industrial</a> <!-- 4. Link do dashboard corrigido -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="clientes.php">Clientes</a></li>
                <li class="nav-item"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
                <?php if ($nivel_usuario === 'dono' || $nivel_usuario === 'gerente'): ?>
                    <!-- 5. Link para produtos visível apenas para gerente ou dono -->
                    <li class="nav-item"><a class="nav-link" href="listar.php">Produtos</a></li>
                <?php endif; ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['usuario']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <div class="p-4 mb-4 bg-white rounded-3 shadow-sm">
        <div class="container-fluid py-3">
            <h1 class="display-5 fw-bold">Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h1>
            <p class="col-md-8 fs-4">Use os cartões abaixo para navegar rapidamente pelas seções do sistema.</p>
        </div>
    </div>

    <!-- 6. Menu principal com cartões visuais -->
    <div class="row align-items-md-stretch">
        <div class="col-md-6 mb-4">
            <div class="h-100 p-5 text-white bg-dark rounded-3 shadow">
                <h2><i class="bi bi-people-fill"></i> Gerenciar Clientes</h2>
                <p>Adicione, edite e visualize a lista de clientes da sua empresa.</p>
                <a href="clientes.php" class="btn btn-outline-light" type="button">Acessar Clientes</a>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="h-100 p-5 bg-body-secondary border rounded-3 shadow">
                <h2><i class="bi bi-box-seam-fill"></i> Gerenciar Pedidos</h2>
                <p>Acompanhe os pedidos, verifique status e gerencie novas solicitações.</p>
                <a href="pedidos.php" class="btn btn-outline-secondary" type="button">Acessar Pedidos</a>
            </div>
        </div>
    </div>

    <?php if ($nivel_usuario === 'dono' || $nivel_usuario === 'gerente'): ?>
        <!-- 7. Cartão para gerenciar produtos visível apenas para gerente ou dono -->
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="p-5 bg-primary text-white rounded-3 shadow">
                    <h2><i class="bi bi-tags-fill"></i> Gerenciar Produtos</h2>
                    <p>Cadastre novos produtos, defina preços e gerencie o catálogo disponível para os clientes.</p>
                    <a href="listar.php" class="btn btn-outline-light" type="button">Acessar Produtos</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($nivel_usuario === 'dono'): ?>
        <!-- 8. Cartão para gerenciar usuários visível apenas para o dono -->
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="p-5 bg-info text-dark rounded-3 shadow">
                    <h2><i class="bi bi-person-plus-fill"></i> Gerenciar Usuários</h2>
                    <p>Crie, edite e remova contas de usuários do sistema. Defina os níveis de acesso (gerente ou funcionário).</p>
                    <a href="usuarios.php" class="btn btn-outline-dark" type="button">Acessar Usuários</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
