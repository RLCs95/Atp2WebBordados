<?php
session_start();
require_once '../../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: listar.php');
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare('SELECT * FROM produtos WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$produto = $result->fetch_assoc();

if (!$produto) {
    header('Location: listar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $imagem = $produto['imagem'];

    // Só gerente ou dono pode alterar imagem
    if (isset($_SESSION['nivel']) && ($_SESSION['nivel'] === 'gerente' || $_SESSION['nivel'] === 'dono')) {
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $nomeImg = uniqid('prod_') . '.' . $ext;
            $destino = '../../assets/img/' . $nomeImg;
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                $imagem = $nomeImg;
            }
        }
    }

    $stmt = $conn->prepare('UPDATE produtos SET nome=?, descricao=?, preco=?, imagem=? WHERE id=?');
    $stmt->bind_param('ssdsi', $nome, $descricao, $preco, $imagem, $id);
    if ($stmt->execute()) {
        header('Location: listar.php?msg=editado');
        exit;
    } else {
        $erro = 'Erro ao atualizar produto!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Editar Produto</h2>
    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php endif; ?>
    <form action="" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" required value="<?php echo htmlspecialchars($produto['nome']); ?>">
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" required><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="preco" class="form-label">Preço</label>
            <input type="number" step="0.01" class="form-control" id="preco" name="preco" required value="<?php echo htmlspecialchars($produto['preco']); ?>">
        </div>
        <?php if (isset($_SESSION['nivel']) && ($_SESSION['nivel'] === 'gerente' || $_SESSION['nivel'] === 'dono')): ?>
        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem (opcional)</label>
            <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
            <?php if ($produto['imagem']): ?>
                <img src="../../assets/img/<?php echo $produto['imagem']; ?>" alt="Imagem atual" class="img-thumbnail mt-2" style="max-width:150px;">
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="listar.php" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>
</body>
</html>
