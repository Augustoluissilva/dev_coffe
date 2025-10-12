<?php
// auth.php - Sistema de autenticação centralizado

function verificarAuth() {
    // Verificar se a sessão está ativa
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Verificar se o usuário está logado
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_nome']) || !isset($_SESSION['logado'])) {
        return false;
    }
    
    // Verificar se a sessão ainda é válida
    if ($_SESSION['logado'] !== true) {
        return false;
    }
    
    // Verificar tempo de inatividade (30 minutos)
    if (isset($_SESSION['ultimo_acesso']) && (time() - $_SESSION['ultimo_acesso'] > 1800)) {
        // Sessão expirada
        session_unset();
        session_destroy();
        return false;
    }
    
    // Atualizar último acesso
    $_SESSION['ultimo_acesso'] = time();
    
    return true;
}

// Função para requerir autenticação
function requerirAuth() {
    if (!verificarAuth()) {
        // Headers para evitar cache
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        header("Location: login.php");
        exit();
    }
}

// Função para redirecionar usuários já logados
function redirecionarSeLogado() {
    if (verificarAuth()) {
        // Headers para evitar cache
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        header("Location: home.php");
        exit();
    }
}

// Função para fazer logout
function fazerLogout() {
    // Limpar todas as variáveis de sessão
    $_SESSION = array();

    // Se deseja destruir a sessão completamente, apague também o cookie de sessão.
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finalmente, destruir a sessão.
    session_destroy();
    
    // Headers para evitar cache
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
}
?>