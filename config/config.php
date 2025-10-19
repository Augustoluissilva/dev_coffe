<?php
// config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cafeteria";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    $error_message = "Desculpe, estamos enfrentando problemas técnicos. Tente novamente mais tarde.";
}

// Buscar informações do usuário se estiver logado
$usuario_nome = 'Visitante';
$usuario_avatar = 'default-avatar.jpg';

if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $sql = "SELECT nome_completo, avatar FROM usuarios WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $usuario_data = $result->fetch_assoc();
        $usuario_nome = $usuario_data['nome_completo'] ?? $_SESSION['usuario_nome'] ?? 'Visitante';
        $usuario_avatar = $usuario_data['avatar'] ?? 'default-avatar.jpg';
        
        // Atualizar sessão com dados mais recentes
        $_SESSION['usuario_nome'] = $usuario_nome;
        $_SESSION['usuario_avatar'] = $usuario_avatar;
    }
    $stmt->close();
}

$conn->close();
?>