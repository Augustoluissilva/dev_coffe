<?php
session_start();

// Verificar se usuário já está logado
if(isset($_SESSION['usuario_id'])){
    header("Location: home.php");
    exit();
}

include('../config/database.php');
include('../models/Usuario.php');

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

$mensagem = "";
$tipo_mensagem = "";

if($_POST){
    $usuario->nome_completo = $_POST['nome'];
    $usuario->email = $_POST['email'];
    $usuario->senha = $_POST['senha'];
    $usuario->telefone = $_POST['telefone'];
    $usuario->cpf = $_POST['cpf'];

    // Validar dados
    if($usuario->emailExiste()){
        $mensagem = "Este email já está cadastrado!";
        $tipo_mensagem = "error";
    } else if($usuario->cpfExiste()){
        $mensagem = "Este CPF já está cadastrado!";
        $tipo_mensagem = "error";
    } else if(strlen($_POST['senha']) < 6){
        $mensagem = "A senha deve ter pelo menos 6 caracteres!";
        $tipo_mensagem = "error";
    } else {
        // Tentar cadastrar
        if($usuario->cadastrar()){
            $mensagem = "Cadastro realizado com sucesso! Faça login para continuar.";
            $tipo_mensagem = "success";
            $_POST = array(); // Limpar formulário
        } else {
            $mensagem = "Erro ao cadastrar. Tente novamente.";
            $tipo_mensagem = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - DevCoffee</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/cadastro.css">
    
    <!-- Scripts para Google Login -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
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
        
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        #google-container {
            display: flex;
            justify-content: center;
            margin: 1rem 0;
        }

        /* Botão Google customizado como fallback */
        .google-btn-custom {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 300px;
            padding: 12px 20px;
            background: white;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            color: #333;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0 auto;
        }
        
        .google-btn-custom:hover {
            border-color: #4285f4;
            background: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .google-icon {
            width: 18px;
            height: 18px;
            margin-right: 12px;
            background: conic-gradient(from -45deg, #ea4335 110deg, #4285f4 90deg 180deg, #34a853 180deg 270deg, #fbbc05 270deg) 73% 55%/150% 150% no-repeat;
            border-radius: 2px;
        }

        .google-loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .hidden {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Lado Esquerdo - Background Café -->
        <div class="auth-left">
            <div class="auth-logo-container">
                <a href="index.php" class="auth-logo">
                    <img src="../img/devcoffee_logo.png" alt="Dev Coffee Logo">
                </a>
            </div>
            <div class="auth-left-content">
                <h1 class="auth-left-title">Olá de volta!</h1>
                <p class="auth-left-subtitle">Para se manter conectado conosco por gentileza logue com suas informações pessoais</p>
                <a href="login.php" class="auth-btn-outline">LOGIN</a>
            </div>
        </div>

        <!-- Lado Direito - Formulário -->
        <div class="auth-right">
            <div class="coffee-splash"></div>
            <div class="auth-form-container">
                <h1 class="auth-title">Criar uma conta</h1>
                
                <?php if($mensagem): ?>
                    <div class="auth-message <?php echo $tipo_mensagem; ?>">
                        <?php echo $mensagem; ?>
                    </div>
                <?php endif; ?>

                <!-- Container para o botão do Google -->
                <div id="google-container">
                    <!-- Google Sign-In Button -->
                    <div id="g_id_onload"
                         data-client_id="154360656663-bftehkt4m59kv8r3sb94licc2b6nso43.apps.googleusercontent.com"
                         data-context="signup"
                         data-ux_mode="popup"
                         data-callback="handleGoogleSignIn"
                         data-auto_prompt="false">
                    </div>

                    <div class="g_id_signin"
                         data-type="standard"
                         data-shape="rectangular"
                         data-theme="outline"
                         data-text="signup_with"
                         data-size="large"
                         data-logo_alignment="left"
                         data-width="300">
                    </div>

                    <!-- Fallback Button -->
                    <button id="googleManualBtn" class="google-btn-custom hidden">
                        <div class="google-icon"></div>
                        <span>Cadastrar com Google</span>
                    </button>
                </div>

                <p class="auth-divider">ou use seu email para se registrar</p>

                <form class="auth-form" method="POST" action="">
                    <div class="form-group">
                        <input type="text" class="form-input" name="nome" placeholder="Nome" 
                               value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <input type="email" class="form-input" name="email" placeholder="Email" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <input type="password" class="form-input" name="senha" placeholder="Senha" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <input type="tel" class="form-input" name="telefone" placeholder="Telefone" 
                                   value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-input" name="cpf" placeholder="CPF" 
                                   value="<?php echo isset($_POST['cpf']) ? htmlspecialchars($_POST['cpf']) : ''; ?>" required>
                        </div>
                    </div>

                    <button type="submit" class="auth-btn-primary">REGISTRAR</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Variável global para controlar o estado do Google
        let googleAPILoaded = false;

        // Função chamada quando o Google Sign-In é bem-sucedido
        function handleGoogleSignIn(response) {
            console.log('Google Sign-In response:', response);
            
            if (!response.credential) {
                showError('Não foi possível obter as credenciais do Google.');
                return;
            }

            showLoading(true);
            
            // Decodificar o token para debug
            try {
                const tokenParts = response.credential.split('.');
                const payload = JSON.parse(atob(tokenParts[1]));
                console.log('Google Payload:', payload);
            } catch (e) {
                console.error('Error decoding token:', e);
            }

            // Enviar para o servidor
            fetch('../api/google-signup.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    credential: response.credential
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro no servidor: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Server response:', data);
                
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
                    showError(data.message || 'Erro ao processar cadastro com Google.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showError('Erro de conexão: ' + error.message);
            })
            .finally(() => {
                showLoading(false);
            });
        }

        // Função para mostrar/ocultar loading
        function showLoading(show) {
            const googleBtn = document.querySelector('.g_id_signin');
            const manualBtn = document.getElementById('googleManualBtn');
            
            if (show) {
                if (googleBtn) googleBtn.style.opacity = '0.6';
                if (manualBtn) manualBtn.classList.add('google-loading');
            } else {
                if (googleBtn) googleBtn.style.opacity = '1';
                if (manualBtn) manualBtn.classList.remove('google-loading');
            }
        }

        // Função para mostrar erro
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: message,
                confirmButtonText: 'OK'
            });
        }

        // Função para inicialização manual do Google Sign-In
        function initManualGoogleSignIn() {
            const manualBtn = document.getElementById('googleManualBtn');
            if (!manualBtn) return;

            manualBtn.addEventListener('click', function() {
                if (typeof google !== 'undefined' && google.accounts && google.accounts.id) {
                    // Se a API está carregada, use o método padrão
                    google.accounts.id.prompt();
                } else {
                    // Se não, mostre instruções
                    Swal.fire({
                        icon: 'info',
                        title: 'Google não disponível',
                        html: `
                            <p>O cadastro com Google não está carregando automaticamente.</p>
                            <p><strong>Soluções:</strong></p>
                            <ul style="text-align: left; margin: 10px 0;">
                                <li>Verifique sua conexão com internet</li>
                                <li>Desative bloqueadores de anúncios</li>
                                <li>Tente atualizar a página</li>
                                <li>Use o formulário tradicional abaixo</li>
                            </ul>
                        `,
                        confirmButtonText: 'Entendi'
                    });
                }
            });
        }

        // Verificar se o Google API carregou
        function checkGoogleAPI() {
            if (typeof google !== 'undefined' && google.accounts && google.accounts.id) {
                console.log('✅ Google API carregada com sucesso');
                googleAPILoaded = true;
                
                // Ocultar botão manual se o Google estiver funcionando
                const manualBtn = document.getElementById('googleManualBtn');
                if (manualBtn) {
                    manualBtn.classList.add('hidden');
                }
                
                return true;
            }
            return false;
        }

        // Inicialização quando a página carrega
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Página de cadastro carregada');

            // Verificar imediatamente se o Google API carregou
            if (!checkGoogleAPI()) {
                console.log('⚠️ Google API não carregada - aguardando...');
                
                // Tentar verificar novamente após 2 segundos
                setTimeout(() => {
                    if (!checkGoogleAPI()) {
                        console.log('❌ Google API ainda não carregada - mostrando fallback');
                        const manualBtn = document.getElementById('googleManualBtn');
                        if (manualBtn) {
                            manualBtn.classList.remove('hidden');
                        }
                    }
                }, 2000);

                // Última tentativa após 5 segundos
                setTimeout(() => {
                    if (!checkGoogleAPI()) {
                        console.log('💀 Google API falhou ao carregar');
                        const manualBtn = document.getElementById('googleManualBtn');
                        if (manualBtn) {
                            manualBtn.classList.remove('hidden');
                        }
                    }
                }, 5000);
            }

            // Inicializar botão manual
            initManualGoogleSignIn();

            // Máscaras para telefone e CPF
            initMasks();
        });

        // Inicializar máscaras
        function initMasks() {
            // Máscara para Telefone
            const telefoneInput = document.querySelector('input[name="telefone"]');
            if (telefoneInput) {
                telefoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length <= 11) {
                        value = value.replace(/(\d{2})(\d)/, '($1) $2');
                        value = value.replace(/(\d{5})(\d)/, '$1-$2');
                    }
                    e.target.value = value;
                });
            }

            // Máscara para CPF
            const cpfInput = document.querySelector('input[name="cpf"]');
            if (cpfInput) {
                cpfInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length <= 11) {
                        value = value.replace(/(\d{3})(\d)/, '$1.$2');
                        value = value.replace(/(\d{3})(\d)/, '$1.$2');
                        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                    }
                    e.target.value = value;
                });
            }
        }

        // Função global para debug
        window.handleGoogleSignIn = handleGoogleSignIn;
    </script>
</body>
</html>