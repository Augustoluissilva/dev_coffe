<?php
// config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'Database.php';

// Conexão com o banco usando PDO
$database = new Database();
$conn = $database->getConnection();

$usuario_nome = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Visitante';
?>