<?php
// 1. Inclui o bootstrap a partir da raiz do projeto.
require_once __DIR__ . '/../bootstrap.php';

$result = $conn->query('SELECT * FROM produtos ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos Cadastrados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Produtos Cadastrados</h2>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'sucesso'): ?>
        <div class="alert alert-success">Produto cadastrado com sucesso!</div>
    <?php endif; ?>
    <a href="cadastrar.php" class="btn btn-success mb-3">Novo Produto</a>
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if ($row['imagem']): ?>
                        <!-- 2. Caminho da imagem corrigido para ser relativo à raiz -->
                        <img src="../assets/img/<?php echo $row['imagem']; ?>" class="card-img-top" alt="Imagem do Produto">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/300x200?text=Sem+Imagem" class="card-img-top" alt="Sem Imagem">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['nome']); ?></h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($row['descricao'])); ?></p>
                        <p class="card-text fw-bold">R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></p>
                        <div class="mt-3">
                            <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
