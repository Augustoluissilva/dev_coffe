<?php
session_start();
require_once 'config/database.php';
require_once 'models/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

echo "<h2>Teste de Estrutura da Tabela</h2>";
echo "<pre>";
print_r($usuario->debugTableStructure());
echo "</pre>";

echo "<h2>Teste de Conexão</h2>";
echo "<pre>";
print_r($usuario->testConnection());
echo "</pre>";

echo "<h2>Client ID do Google</h2>";
echo "299295953821-nqbqqb8va16klodnvebgdja6h40mogc5.apps.googleusercontent.com";
?>