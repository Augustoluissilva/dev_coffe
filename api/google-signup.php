<?php
session_start();
require_once '../config/database.php';
require_once '../models/Usuario.php';

header('Content-Type: application/json');

// Verificar se o token foi enviado
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['credential'])) {
    echo json_encode(['success' => false, 'message' => 'Token não recebido']);
    exit;
}

$token = $data['credential'];

try {
    // Decodificar o token JWT manualmente
    $tokenParts = explode('.', $token);
    if (count($tokenParts) !== 3) {
        throw new Exception('Token JWT inválido');
    }

    $payload = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', $tokenParts[1]))), true);

    if (!$payload) {
        throw new Exception('Falha ao decodificar token');
    }

    // Verificar se o token é válido (expiração)
    $currentTime = time();
    if (isset($payload['exp']) && $payload['exp'] < $currentTime) {
        throw new Exception('Token expirado');
    }

    // Dados do usuário do Google
    $google_id = $payload['sub'];
    $email = $payload['email'];
    $nome = $payload['name'] ?? 'Usuário Google';

    // Log para debug
    error_log("Google Signup Attempt: " . $email . " - " . $google_id);

    $database = new Database();
    $db = $database->getConnection();
    $usuario = new Usuario($db);

    // Processar autenticação Google
    $result = $usuario->processarGoogleAuth($google_id, $email, $nome);

    if ($result['success'] && empty($usuario->id_usuario)) {
        $usuario->buscarPorEmail();
        error_log("Sessão antes de salvar: ID={$usuario->id_usuario}, Email={$usuario->email}");

        // Salvar na sessão
        $_SESSION['usuario_id'] = $usuario->id_usuario ?? null;
        $_SESSION['usuario_nome'] = $usuario->nome_completo;
        $_SESSION['usuario_email'] = $usuario->email;
        $_SESSION['usuario_tipo'] = $usuario->tipo;

        error_log("Google Signup SUCCESS: " . $email);
        error_log("Google API Debug: " . print_r($result, true));


        echo json_encode($result);
    } else {
        error_log("Google Signup FAILED: " . $result['message']);
        echo json_encode($result);
    }

} catch (Exception $e) {
    error_log("Google Signup ERROR: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor. Tente novamente.'
    ]);
}
?>