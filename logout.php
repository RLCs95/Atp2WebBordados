<?php
/**
 * Script de Logout.
 *
 * Este arquivo é responsável por encerrar a sessão do usuário de forma segura.
 */

// 1. Inicia a sessão para poder acessá-la.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Limpa todas as variáveis da sessão.
$_SESSION = [];

// 3. Destrói a sessão.
session_destroy();

// 4. Redireciona o usuário para a página de login.
header('Location: login.php');
exit;