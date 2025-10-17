<?php
session_start();

// Incluir sistema de autenticação
include_once "../includes/auth.php";

// Verificar se usuário já está logado (usando a nova função)
redirecionarSeLogado();

// Headers para evitar cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include('../config/database.php');
include('../models/Usuario.php');

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

$mensagem = "";
$tipo_mensagem = "";

if($_POST){
    $usuario->email = $_POST['email'];
    $senha = $_POST['senha'];
    $usuario->senha = $senha;

    if($usuario->login()){
        // Sistema de autenticação atualizado - usando os nomes corretos da estrutura
        $_SESSION['usuario_id'] = $usuario->id_usuario;
        $_SESSION['usuario_nome'] = $usuario->nome_completo;
        $_SESSION['usuario_tipo'] = $usuario->tipo;
        $_SESSION['usuario_email'] = $usuario->email;
        $_SESSION['logado'] = true;
        $_SESSION['ultimo_acesso'] = time();
        
        header("Location: home.php");
        exit();
    } else {
        $mensagem = "Email ou senha incorretos!";
        $tipo_mensagem = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DevCoffee</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
    
    <!-- Scripts para Google Login -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Fallback inline caso o CSS externo não carregue */
        .fallback-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: #f5f5f5;
            font-family: 'Montserrat', sans-serif;
        }
        
        /* Estilos para o botão do Google */
        .google-login-container {
            display: flex;
            justify-content: center;
            margin: 1rem 0;
        }
        
        .auth-divider {
            text-align: center;
            margin: 1.5rem 0;
            color: #666;
            position: relative;
        }
        
        .auth-divider::before,
        .auth-divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background: #ddd;
        }
        
        .auth-divider::before {
            left: 0;
        }
        
        .auth-divider::after {
            right: 0;
        }

        /* Loading state para botão do Google */
        .google-loading {
            opacity: 0.7;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <noscript>
        <div style="background: #ffebee; color: #c53030; padding: 20px; text-align: center;">
            JavaScript está desabilitado. Alguns recursos podem não funcionar corretamente.
        </div>
    </noscript>

    <div class="auth-container">
        <!-- Lado Esquerdo - Formulário -->
        <div class="auth-left">
            <div class="coffee-splash"></div>
            <div class="auth-form-container">
                <h1 class="auth-title">Entrar</h1>
                
                <?php if($mensagem): ?>
                    <div class="auth-message <?php echo $tipo_mensagem; ?>">
                        <?php echo $mensagem; ?>
                    </div>
                <?php endif; ?>

                <!-- Botão de Login com Google -->
                <div class="google-login-container">
                    <div id="g_id_onload"
                         data-client_id="154360656663-bftehkt4m59kv8r3sb94licc2b6nso43.apps.googleusercontent.com"
                         data-context="signin"
                         data-ux_mode="popup"
                         data-callback="handleGoogleLogin"
                         data-auto_prompt="false">
                    </div>

                    <div class="g_id_signin"
                         data-type="standard"
                         data-shape="rectangular"
                         data-theme="outline"
                         data-text="signin_with"
                         data-size="large"
                         data-logo_alignment="left"
                         data-width="300">
                    </div>
                </div>

                <p class="auth-divider">ou use sua conta</p>

                <form class="auth-form" method="POST" action="">
                    <div class="form-group">
                        <input type="email" class="form-input" name="email" placeholder="Email" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                               required autocomplete="email">
                    </div>

                    <div class="form-group">
                        <input type="password" class="form-input" name="senha" placeholder="Senha" 
                               required autocomplete="current-password">
                    </div>

                    <a href="redefinir.php" class="forgot-link">Esqueceu a senha?</a>

                    <button type="submit" class="auth-btn-primary" id="login-btn">
                        LOGIN
                    </button>
                </form>

                <!-- Links adicionais para mobile -->
                <div class="mobile-links" style="display: none;">
                    <p style="text-align: center; margin-top: 2rem; color: #666;">
                        Não tem uma conta? 
                        <a href="cadastro.php" style="color: #E0B76F; text-decoration: none; font-weight: 600;">
                            Registre-se
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Lado Direito - Background Café -->
        <div class="auth-right">
            <div class="auth-logo-container">
                <a href="index.php" class="auth-logo">
                    <Img src="../img/devcoffee_logo.png" alt="Dev Coffee Logo" style="height: 40px;">
                </a>
            </div>
            <div class="auth-right-content">
                <h1 class="auth-right-title">Olá!</h1>
                <p class="auth-right-subtitle">Entre com suas informações e viva sua melhor experiência!</p>
                <a href="cadastro.php" class="auth-btn-outline">REGISTRAR</a>
            </div>
        </div>
    </div>

    <script>
        // Função para lidar com o login do Google
        function handleGoogleLogin(response) {
            console.log('Resposta do Google recebida:', response);
            
            // Mostrar loading no botão do Google
            const googleBtn = document.querySelector('.g_id_signin');
            if (googleBtn) {
                googleBtn.classList.add('google-loading');
            }
            
            // Enviar o token para o servidor para validação
            fetch('../api/google-login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    credential: response.credential
                })
            })
            .then(response => {
                console.log('Resposta do servidor:', response);
                if (!response.ok) {
                    throw new Error('Erro na resposta do servidor: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Dados recebidos:', data);
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'home.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro no login',
                        text: data.message || 'Erro ao fazer login com Google.'
                    });
                    
                    // Resetar botão do Google
                    if (googleBtn) {
                        googleBtn.classList.remove('google-loading');
                    }
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de conexão',
                    text: 'Erro de conexão. Tente novamente.'
                });
                
                // Resetar botão do Google
                if (googleBtn) {
                    googleBtn.classList.remove('google-loading');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página de login carregada - inicializando Google Sign-In');
            
            // Inicializar manualmente para garantir
            if (typeof google !== 'undefined') {
                google.accounts.id.initialize({
                    client_id: "154360656663-bftehkt4m59kv8r3sb94licc2b6nso43.apps.googleusercontent.com",
                    callback: handleGoogleLogin,
                    context: "signin",
                    ux_mode: "popup"
                });
            }

            // Prevenir navegação com botão voltar após logout
            window.history.pushState(null, null, window.location.href);
            window.onpopstate = function() {
                window.history.go(1);
            };

            // Limpar qualquer dado residual no localStorage
            localStorage.removeItem('userData');
            sessionStorage.clear();

            // Fallback para imagem de background
            const authRight = document.querySelector('.auth-right');
            const testImage = new Image();
            testImage.src = '../img/login_cadas.jpeg';
            
            testImage.onerror = function() {
                authRight.classList.add('no-bg-image');
                console.log('Imagem de background não encontrada. Usando fallback CSS.');
            };

            testImage.onload = function() {
                console.log('Imagem de background carregada com sucesso.');
            };
            
            // Adiciona loading state no botão de login
            const loginForm = document.querySelector('.auth-form');
            const loginBtn = document.getElementById('login-btn');
            
            if (loginForm && loginBtn) {
                loginForm.addEventListener('submit', function(e) {
                    // Validação básica do cliente
                    const email = loginForm.querySelector('input[name="email"]');
                    const senha = loginForm.querySelector('input[name="senha"]');
                    
                    if (!email.value || !senha.value) {
                        e.preventDefault();
                        return;
                    }
                    
                    loginBtn.classList.add('loading');
                    loginBtn.disabled = true;
                    loginBtn.innerHTML = 'ENTRANDO...';
                    
                    // Timeout para evitar loading infinito
                    setTimeout(function() {
                        loginBtn.classList.remove('loading');
                        loginBtn.disabled = false;
                        loginBtn.innerHTML = 'LOGIN';
                    }, 10000);
                });
            }

            // Mostrar links adicionais em mobile
            function checkMobile() {
                const mobileLinks = document.querySelector('.mobile-links');
                if (window.innerWidth <= 768) {
                    mobileLinks.style.display = 'block';
                } else {
                    mobileLinks.style.display = 'none';
                }
            }

            checkMobile();
            window.addEventListener('resize', checkMobile);
        });
    </script>
</body>
</html>