<?php
session_start();

// 1. Verificar login
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    $_SESSION['error_message'] = "Você precisa estar logado.";
    header('Location: login.php');
    exit;
}

$usuario_id = (int)$_SESSION['usuario_id']; // Forçar como inteiro

// Conexão
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cafeteria";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// 2. VERIFICAR SE O USUÁRIO EXISTE NO BANCO
$sql_check = "SELECT id_usuario FROM usuarios WHERE id_usuario = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $usuario_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    $stmt_check->close();
    $conn->close();
    $_SESSION['error_message'] = "Erro: Usuário não encontrado. Faça login novamente.";
    header('Location: login.php');
    exit;
}
$stmt_check->close();

// 3. Preparar dados do formulário
$cep = trim($conn->real_escape_string($_POST['cep']));
$endereco = trim($conn->real_escape_string($_POST['endereco']));
$numero = trim($conn->real_escape_string($_POST['numero']));
$complemento = trim($conn->real_escape_string($_POST['complemento']));
$bairro = trim($conn->real_escape_string($_POST['bairro']));
$cidade = trim($conn->real_escape_string($_POST['cidade']));
$estado = trim($conn->real_escape_string($_POST['estado']));
$referencia = trim($conn->real_escape_string($_POST['referencia']));
$tipo_endereco = trim($conn->real_escape_string($_POST['tipo_endereco']));

// 4. Validação
if (empty($cep) || empty($endereco) || empty($numero) || empty($bairro) || empty($cidade) || empty($estado) || empty($tipo_endereco)) {
    $_SESSION['error_message'] = "Por favor, preencha todos os campos obrigatórios.";
    header('Location: formulario_endereco.php');
    exit;
}

// 5. Modo Edição
if (isset($_POST['modo_edicao']) && $_POST['modo_edicao'] == '1' && isset($_POST['endereco_id'])) {
    $endereco_id = (int)$_POST['endereco_id'];

    // Verificar propriedade
    $sql_verif = "SELECT id_endereco FROM enderecos WHERE id_endereco = ? AND id_usuario = ?";
    $stmt_verif = $conn->prepare($sql_verif);
    $stmt_verif->bind_param("ii", $endereco_id, $usuario_id);
    $stmt_verif->execute();
    $res_verif = $stmt_verif->get_result();

    if ($res_verif->num_rows > 0) {
        $sql = "UPDATE enderecos SET 
                    cep = ?, logradouro = ?, numero = ?, complemento = ?, 
                    bairro = ?, cidade = ?, estado = ?, referencia = ?, tipo_endereco = ?
                WHERE id_endereco = ? AND id_usuario = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssii", $cep, $endereco, $numero, $complemento, $bairro, $cidade, $estado, $referencia, $tipo_endereco, $endereco_id, $usuario_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Endereço atualizado com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao atualizar: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Endereço não encontrado ou sem permissão.";
    }
    $stmt_verif->close();
} else {
    // 6. INSERIR NOVO ENDEREÇO
    $sql = "INSERT INTO enderecos (
                id_usuario, cep, logradouro, numero, complemento, 
                bairro, cidade, estado, referencia, tipo_endereco
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['error_message'] = "Erro ao preparar consulta: " . $conn->error;
    } else {
        $stmt->bind_param("isssssssss", $usuario_id, $cep, $endereco, $numero, $complemento, $bairro, $cidade, $estado, $referencia, $tipo_endereco);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Endereço cadastrado com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao salvar: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
header('Location: formulario_endereco.php');
exit;
?>