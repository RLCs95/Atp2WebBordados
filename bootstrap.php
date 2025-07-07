<?php
/**
 * Ponto de entrada e inicialização para toda a aplicação.
 * Este arquivo deve ser incluído no início de todas as páginas PHP.
 */

// 1. Inicia a sessão em um único lugar. Essencial para login e mensagens.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Define uma constante com o caminho absoluto da raiz do projeto.
define('ROOT_PATH', __DIR__);

// 3. Carrega a configuração do banco de dados usando o caminho absoluto.
require_once ROOT_PATH . '/admin/config/database.php';