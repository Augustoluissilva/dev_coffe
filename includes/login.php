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
        // Sistema de autenticação atualizado
        $_SESSION['usuario_id'] = $usuario->id;
        $_SESSION['usuario_nome'] = $usuario->nome;
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

                <div class="social-buttons">
                    <button class="social-btn facebook" type="button" aria-label="Login com Facebook">
                        <!-- Ícone via CSS -->
                    </button>
                    <button class="social-btn google" type="button" aria-label="Login com Google">
                        <!-- Ícone via CSS -->
                    </button>
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
        document.addEventListener('DOMContentLoaded', function() {
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
                    }, 10000); // 10 segundos timeout
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

            // Verificar na carga inicial e no redimensionamento
            checkMobile();
            window.addEventListener('resize', checkMobile);

            // Efeitos de interação nos inputs
            const formInputs = document.querySelectorAll('.form-input');
            formInputs.forEach(input => {
                // Adiciona classe quando o input tem valor
                input.addEventListener('input', function() {
                    if (this.value) {
                        this.classList.add('has-value');
                    } else {
                        this.classList.remove('has-value');
                    }
                });

                // Verifica estado inicial
                if (input.value) {
                    input.classList.add('has-value');
                }

                // Efeito de foco melhorado
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });

            // Prevenir múltiplos cliques nos botões sociais
            const socialButtons = document.querySelectorAll('.social-btn');
            socialButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Feedback visual
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);

                    // Aqui você pode adicionar a lógica de login social
                    const socialType = this.classList.contains('facebook') ? 'Facebook' : 'Google';
                    console.log(`Login com ${socialType} clicado - Integre com sua API aqui`);
                    
                    // Exemplo de modal ou redirecionamento
                    // window.location.href = `auth-${socialType.toLowerCase()}.php`;
                });
            });

            // Melhorar acessibilidade do teclado
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const focused = document.activeElement;
                    if (focused.classList.contains('form-input') || 
                        focused.classList.contains('social-btn') ||
                        focused.classList.contains('auth-btn-primary') ||
                        focused.classList.contains('auth-btn-outline')) {
                        focused.blur();
                    }
                }
            });

            // Adicionar suporte para prefers-color-scheme
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.body.classList.add('dark-mode-support');
            }

            // Detecta se o CSS carregou corretamente
            setTimeout(function() {
                const authContainer = document.querySelector('.auth-container');
                if (!authContainer || getComputedStyle(authContainer).display === 'none') {
                    console.error('CSS não carregou corretamente. Aplicando fallback...');
                    document.body.innerHTML = `
                        <div class="fallback-loading">
                            <div style="text-align: center; padding: 2rem;">
                                <h1 style="color: #E0B76F; margin-bottom: 1rem;">DevCoffee</h1>
                                <p>Carregando...</p>
                            </div>
                        </div>
                    `;
                }
            }, 1000);
        });

        // Suporte para preferência de redução de movimento
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.documentElement.style.setProperty('--animation-duration', '0.01ms');
        }

        // Prevenir que a página seja armazenada em cache
        window.onpageshow = function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        };
    </script>

    <!-- Fallback para navegadores muito antigos -->
    <script>
        // Polyfill para NodeList.forEach em IE
        if (window.NodeList && !NodeList.prototype.forEach) {
            NodeList.prototype.forEach = Array.prototype.forEach;
        }

        // Polyfill para Element.classList em IE9
        if (!("classList" in document.documentElement)) {
            Object.defineProperty(HTMLElement.prototype, 'classList', {
                get: function() {
                    var self = this;
                    function update(fn) {
                        return function(value) {
                            var classes = self.className.split(/\s+/g);
                            var index = classes.indexOf(value);
                            fn(classes, index, value);
                            self.className = classes.join(" ");
                        };
                    }
                    return {
                        add: update(function(classes, index, value) {
                            if (!~index) classes.push(value);
                        }),
                        remove: update(function(classes, index) {
                            if (~index) classes.splice(index, 1);
                        }),
                        toggle: update(function(classes, index, value) {
                            if (~index) classes.splice(index, 1);
                            else classes.push(value);
                        }),
                        contains: function(value) {
                            return !!~self.className.split(/\s+/g).indexOf(value);
                        }
                    };
                }
            });
        }
    </script>
</body>
</html>