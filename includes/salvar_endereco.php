<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cafeteria";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Prepara os dados
$usuario_id = $_SESSION['usuario_id'];
$cep = $conn->real_escape_string($_POST['cep']);
$endereco = $conn->real_escape_string($_POST['endereco']);
$numero = $conn->real_escape_string($_POST['numero']);
$complemento = $conn->real_escape_string($_POST['complemento']);
$bairro = $conn->real_escape_string($_POST['bairro']);
$cidade = $conn->real_escape_string($_POST['cidade']);
$estado = $conn->real_escape_string($_POST['estado']);
$referencia = $conn->real_escape_string($_POST['referencia']);
$tipo_endereco = $conn->real_escape_string($_POST['tipo_endereco']);

// Validação básica
if (empty($cep) || empty($endereco) || empty($numero) || empty($bairro) || empty($cidade) || empty($estado) || empty($tipo_endereco)) {
    $_SESSION['error_message'] = "Por favor, preencha todos os campos obrigatórios.";
    header('Location: formulario_endereco.php');
    exit;
}

// Verificar se é modo edição
if (isset($_POST['modo_edicao']) && $_POST['modo_edicao'] == '1' && isset($_POST['endereco_id'])) {
    $endereco_id = $_POST['endereco_id'];
    
    // Verificar se o endereço pertence ao usuário
    $sql_verificar = "SELECT id FROM enderecos WHERE id = ? AND usuario_id = ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("ii", $endereco_id, $usuario_id);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();
    
    if ($result_verificar->num_rows > 0) {
        // Atualizar endereço existente
        $sql = "UPDATE enderecos SET cep = ?, endereco = ?, numero = ?, complemento = ?, bairro = ?, cidade = ?, estado = ?, referencia = ?, tipo_endereco = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sssssssssi", $cep, $endereco, $numero, $complemento, $bairro, $cidade, $estado, $referencia, $tipo_endereco, $endereco_id);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Endereço atualizado com sucesso!";
            } else {
                $_SESSION['error_message'] = "Erro ao atualizar endereço: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Erro ao preparar a consulta: " . $conn->error;
        }
    } else {
        $_SESSION['error_message'] = "Endereço não encontrado ou você não tem permissão para editá-lo.";
    }
    
    $stmt_verificar->close();
} else {
    // Inserir novo endereço
    $sql = "INSERT INTO enderecos (usuario_id, cep, endereco, numero, complemento, bairro, cidade, estado, referencia, tipo_endereco) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("isssssssss", $usuario_id, $cep, $endereco, $numero, $complemento, $bairro, $cidade, $estado, $referencia, $tipo_endereco);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Endereço cadastrado com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao cadastrar endereço: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Erro ao preparar a consulta: " . $conn->error;
    }
}

$conn->close();
header('Location: formulario_endereco.php');
exit;
?>