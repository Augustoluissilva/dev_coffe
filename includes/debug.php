<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 DEBUG - ERROS DO FORMULÁRIO</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>🔍 SESSÃO</h2>";
print_r($_SESSION);

if (isset($_SESSION['erro_contato'])) {
    echo "<div style='background: red; color: white; padding: 10px;'>ERRO: " . $_SESSION['erro_contato'] . "</div>";
}
?>