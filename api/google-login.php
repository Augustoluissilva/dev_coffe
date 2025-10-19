<?php
// ../api/google-login.php
session_start();
require_once '../config/database.php';
require_once '../models/Usuario.php';

header('Content-Type: application/json');

// Configurações do Google - SEU CLIENT ID
$CLIENT_ID = "154360656663-bftehkt4m59kv8r3sb94licc2b6nso43.apps.googleusercontent.com";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$credential = $data['credential'] ?? '';

if (empty($credential)) {
    echo json_encode(['success' => false, 'message' => 'Token não fornecido']);
    exit;
}

try {
    // Decodificar o token JWT do Google
    $tokenParts = explode('.', $credential);
    if (count($tokenParts) !== 3) {
        throw new Exception('Token JWT inválido');
    }

    $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
    $userData = json_decode($payload, true);

    if (!$userData) {
        throw new Exception('Não foi possível decodificar o token');
    }

    // Verificar se o token é válido
    if ($userData['aud'] !== $CLIENT_ID) {
        throw new Exception('Token não é válido para este aplicativo');
    }

    // Verificar expiração
    if (isset($userData['exp']) && $userData['exp'] < time()) {
        throw new Exception('Token expirado');
    }

    // Dados do usuário do Google
    $googleId = $userData['sub'];
    $email = $userData['email'];
    $nome = $userData['name'] ?? 'Usuário Google';

    $database = new Database();
    $db = $database->getConnection();
    $usuario = new Usuario($db);

    // Verificar se o usuário já existe pelo Google ID
    if ($usuario->buscarPorGoogleId($googleId)) {
        // Usuário existe - fazer login
        $_SESSION['usuario_id'] = $usuario->id_usuario;
        $_SESSION['usuario_nome'] = $usuario->nome_completo;
        $_SESSION['usuario_tipo'] = $usuario->tipo;
        $_SESSION['usuario_email'] = $usuario->email;
        $_SESSION['logado'] = true;
        $_SESSION['ultimo_acesso'] = time();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Login realizado com sucesso!'
        ]);
    } else {
        // Verificar se o email já existe
        $usuario->email = $email;
        if ($usuario->buscarPorEmail()) {
            // Email existe - vincular conta Google
            if ($usuario->vincularGoogle($usuario->id_usuario, $googleId)) {
                $_SESSION['usuario_id'] = $usuario->id_usuario;
                $_SESSION['usuario_nome'] = $usuario->nome_completo;
                $_SESSION['usuario_tipo'] = $usuario->tipo;
                $_SESSION['usuario_email'] = $usuario->email;
                $_SESSION['logado'] = true;
                $_SESSION['ultimo_acesso'] = time();
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Conta vinculada com sucesso!'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Erro ao vincular conta Google.'
                ]);
            }
        } else {
            // Criar nova conta com Google
            $usuario->nome_completo = $nome;
            $usuario->email = $email;
            $usuario->google_id = $googleId;
            $usuario->telefone = '';
            $usuario->cpf = '';

            if ($usuario->cadastrarComGoogle()) {
                $_SESSION['usuario_id'] = $usuario->id_usuario;
                $_SESSION['usuario_nome'] = $usuario->nome_completo;
                $_SESSION['usuario_tipo'] = $usuario->tipo;
                $_SESSION['usuario_email'] = $usuario->email;
                $_SESSION['logado'] = true;
                $_SESSION['ultimo_acesso'] = time();
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Conta criada com sucesso com Google!'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Erro ao criar conta com Google.'
                ]);
            }
        }
    }
} catch (Exception $e) {
    error_log('Erro no login com Google: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erro: ' . $e->getMessage()
    ]);
}
?>