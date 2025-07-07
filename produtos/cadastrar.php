<?php
// Inclui o bootstrap a partir da raiz do projeto.
require_once __DIR__ . '/../bootstrap.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Cadastro de Produto</h2>    
    <form action="salvar.php" method="POST" enctype="multipart/form-data" id="formProduto" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" required></textarea>
        </div>
        <div class="mb-3">
            <label for="preco" class="form-label">Preço</label>
            <input type="number" step="0.01" class="form-control" id="preco" name="preco" required>
        </div>
        <?php if (isset($_SESSION['nivel']) && ($_SESSION['nivel'] === 'gerente' || $_SESSION['nivel'] === 'dono')): ?>
        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem</label>
            <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
        </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Cadastrar</button>
        <a href="listar.php" class="btn btn-secondary ms-2">Ver Produtos</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Validação simples do formulário
const form = document.getElementById('formProduto');
form.addEventListener('submit', function(e) {
    const nome = document.getElementById('nome').value.trim();
    const preco = document.getElementById('preco').value;
    if (nome.length < 3) {
        alert('O nome deve ter pelo menos 3 caracteres.');
        e.preventDefault();
    }
    if (preco <= 0) {
        alert('O preço deve ser maior que zero.');
        e.preventDefault();
    }
});
</script>
</body>
</html>
