<?php
/**
 * Ponto de entrada principal da aplicação.
 *
 * Este script verifica se a configuração inicial (criação do usuário 'dono')
 * foi concluída. Se não, redireciona para a página de setup.
 * Caso contrário, redireciona para a página de login.
 */

// Inicia a sessão, pode ser útil para futuras implementações
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Inclui a configuração do banco de dados.
// O caminho é relativo à localização deste arquivo (raiz do projeto).
require_once 'admin/config/database.php';

// 2. Verifica se um usuário 'dono' já existe.
// A variável $conn vem do arquivo database.php
$donoExists = false;
try {
    $query = "SELECT COUNT(*) as total FROM usuarios WHERE nivel = 'dono'";
    $result = $conn->query($query);

    if ($result) {
        $row = $result->fetch_assoc();
        if (isset($row['total']) && $row['total'] > 0) {
            $donoExists = true;
        }
        $result->close();
    }
} catch (Exception $e) {
    // Em caso de erro (ex: tabela 'usuarios' não existe), é seguro assumir que o setup não foi feito.
    $donoExists = false;
}

$conn->close();

// 3. Redireciona com base na existência do 'dono'.
header('Location: ' . ($donoExists ? 'login.php' : 'admin/config/primeiro-usuario.php'));
exit;