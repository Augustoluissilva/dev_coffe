<?php
// ../api/google-cadastro.php
session_start();
require_once '../config/database.php';
require_once '../models/Usuario.php';

header('Content-Type: application/json');

// Configurações do Google - SEU CLIENT ID CORRETO
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

    // Verificar se o usuário já existe (email ou Google ID)
    $usuario->email = $email;
    
    if ($usuario->buscarPorEmail() || $usuario->buscarPorGoogleId($googleId)) {
        // Se o usuário já existe, informar para fazer login
        echo json_encode([
            'success' => false, 
            'message' => 'Esta conta Google já está cadastrada. Faça login em vez de cadastrar.'
        ]);
    } else {
        // CRIAR NOVA CONTA com Google (CADASTRO)
        $usuario->nome_completo = $nome;
        $usuario->email = $email;
        $usuario->google_id = $googleId;
        $usuario->telefone = '';
        $usuario->cpf = '';
        $usuario->senha = ''; // Senha será gerada automaticamente

        if ($usuario->cadastrarComGoogle()) {
            // Login automático após cadastro
            $_SESSION['usuario_id'] = $usuario->id_usuario;
            $_SESSION['usuario_nome'] = $usuario->nome_completo;
            $_SESSION['usuario_tipo'] = $usuario->tipo;
            $_SESSION['usuario_email'] = $usuario->email;
            $_SESSION['logado'] = true;
            $_SESSION['ultimo_acesso'] = time();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Cadastro realizado com sucesso com Google!'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Erro ao criar conta com Google. Tente novamente.'
            ]);
        }
    }
} catch (Exception $e) {
    error_log('Erro no cadastro com Google: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Erro: ' . $e->getMessage()
    ]);
}
?>