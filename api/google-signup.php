<?php
// ../api/google-signup.php
session_start();
require_once '../config/database.php';
require_once '../models/Usuario.php';

header('Content-Type: application/json');

// Configurações do Google
$CLIENT_ID = "299295953821-nqbqqb8va16klodnvebgdja6h40mogc5.apps.googleusercontent.com";

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

// Verificar e decodificar o token JWT
$tokenParts = explode('.', $credential);
if (count($tokenParts) !== 3) {
    echo json_encode(['success' => false, 'message' => 'Token inválido']);
    exit;
}

$payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
$userData = json_decode($payload, true);

// Verificar se o token é válido
if (!$userData || $userData['aud'] !== $CLIENT_ID) {
    echo json_encode(['success' => false, 'message' => 'Token inválido']);
    exit;
}

// Verificar expiração
if (isset($userData['exp']) && $userData['exp'] < time()) {
    echo json_encode(['success' => false, 'message' => 'Token expirado']);
    exit;
}

// Dados do usuário do Google
$googleId = $userData['sub'];
$email = $userData['email'];
$nome = $userData['name'] ?? '';
$foto = $userData['picture'] ?? '';

try {
    $database = new Database();
    $db = $database->getConnection();
    $usuario = new Usuario($db);
    
    // Verificar se o usuário já existe
    $usuario->email = $email;
    
    if ($usuario->buscarPorEmail()) {
        // Usuário já existe - fazer login automaticamente
        $_SESSION['usuario_id'] = $usuario->id;
        $_SESSION['usuario_nome'] = $usuario->nome;
        $_SESSION['usuario_tipo'] = $usuario->tipo;
        $_SESSION['usuario_email'] = $usuario->email;
        $_SESSION['logado'] = true;
        $_SESSION['ultimo_acesso'] = time();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Login realizado com sucesso! Sua conta já existia no sistema.'
        ]);
    } else {
        // Usuário não existe - criar nova conta com Google
        $usuario->nome = $nome;
        $usuario->email = $email;
        $usuario->senha = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT); // Senha aleatória
        $usuario->tipo = 'usuario'; // Tipo padrão
        $usuario->google_id = $googleId;
        
        // Campos opcionais para cadastro com Google
        $usuario->telefone = ''; // Pode ser preenchido depois
        $usuario->cpf = ''; // Pode ser preenchido depois
        
        if ($usuario->cadastrarComGoogle()) {
            // Login após criar conta
            $_SESSION['usuario_id'] = $usuario->id;
            $_SESSION['usuario_nome'] = $usuario->nome;
            $_SESSION['usuario_tipo'] = $usuario->tipo;
            $_SESSION['usuario_email'] = $usuario->email;
            $_SESSION['logado'] = true;
            $_SESSION['ultimo_acesso'] = time();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Conta criada com sucesso com sua conta Google!'
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
        'message' => 'Erro interno do servidor: ' . $e->getMessage()
    ]);
}