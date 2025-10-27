<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar se o ID do endereço foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Endereço inválido.";
    header('Location: meus_dados.php');
    exit;
}

$endereco_id = $_GET['id'];

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cafeteria";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    $_SESSION['error_message'] = "Desculpe, estamos enfrentando problemas técnicos. Tente novamente mais tarde.";
    header('Location: meus_dados.php');
    exit;
}

// Verificar se o endereço pertence ao usuário antes de excluir
$sql_verificar = "SELECT id FROM enderecos WHERE id = ? AND usuario_id = ?";
$stmt_verificar = $conn->prepare($sql_verificar);
$stmt_verificar->bind_param("ii", $endereco_id, $_SESSION['usuario_id']);
$stmt_verificar->execute();
$result_verificar = $stmt_verificar->get_result();

if ($result_verificar->num_rows === 0) {
    $_SESSION['error_message'] = "Endereço não encontrado ou você não tem permissão para excluí-lo.";
    header('Location: meus_dados.php');
    exit;
}

// Excluir o endereço
$sql_excluir = "DELETE FROM enderecos WHERE id = ? AND usuario_id = ?";
$stmt_excluir = $conn->prepare($sql_excluir);
$stmt_excluir->bind_param("ii", $endereco_id, $_SESSION['usuario_id']);

if ($stmt_excluir->execute()) {
    $_SESSION['success_message'] = "Endereço excluído com sucesso!";
} else {
    $_SESSION['error_message'] = "Erro ao excluir endereço. Tente novamente.";
}

$stmt_verificar->close();
$stmt_excluir->close();
$conn->close();

// Redirecionar de volta para a página de meus dados
header('Location: meus_dados.php');
exit;
?>