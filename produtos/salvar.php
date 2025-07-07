<?php
session_start();
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $imagem = null;

    // Só gerente ou dono pode enviar imagem
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

    // Inserir no banco
    $stmt = $conn->prepare('INSERT INTO produtos (nome, descricao, preco, imagem) VALUES (?, ?, ?, ?)');
    $stmt->execute(['ssds', $nome, $descricao, $preco, $imagem]);
    
        header('Location: listar.php?msg=sucesso');
        exit;
    } else {
        echo '<div class="alert alert-danger">Erro ao cadastrar produto.</div>';
    }
?>
