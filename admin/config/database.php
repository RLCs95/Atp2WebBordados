<?php
$host = 'localhost';
$db = 'atp2webbordados';
$user = 'root';
$pass = '';
$port = '3308'; // Porta do seu MySQL, se for diferente da padrão (3306)

// Conexão usando MySQLi
$conn = new mysqli($host, $user, $pass, $db, $port);

// Define o charset para utf8mb4 para suportar todos os caracteres
$conn->set_charset('utf8mb4');

// Verifica se a conexão falhou
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>