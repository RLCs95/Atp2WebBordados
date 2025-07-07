<?php
// Garante que a variável de nível de usuário exista, mesmo que seja como 'funcionario' por padrão.
$nivel_usuario = $_SESSION['nivel'] ?? 'funcionario';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Bordado Industrial</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="clientes.php">Clientes</a></li>
                <li class="nav-item"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
                <?php if ($nivel_usuario === 'dono' || $nivel_usuario === 'gerente'): ?>
                    <li class="nav-item"><a class="nav-link" href="listar.php">Produtos</a></li>
                <?php endif; ?>
                <?php if ($nivel_usuario === 'dono'): ?>
                    <li class="nav-item"><a class="nav-link" href="usuarios.php">Usuários</a></li>
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