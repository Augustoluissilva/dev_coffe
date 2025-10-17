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
    $usuario->nome = $_POST['nome'];
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
        /* Estilos para o botão do Google personalizado */
        .google-login-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px 20px;
            background: white;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            color: #333;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 1rem 0;
        }
        
        .google-login-btn:hover {
            border-color: #4285f4;
            background: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .google-login-btn:active {
            transform: translateY(0);
        }
        
        .google-icon {
            width: 18px;
            height: 18px;
            margin-right: 12px;
            background: conic-gradient(from -45deg, #ea4335 110deg, #4285f4 90deg 180deg, #34a853 180deg 270deg, #fbbc05 270deg) 73% 55%/150% 150% no-repeat;
            border-radius: 2px;
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
        
        /* Esconder o botão padrão do Google */
        #g_id_onload,
        .g_id_signin {
            display: none !important;
        }
        
        /* Loading state */
        .loading {
            opacity: 0.7;
            pointer-events: none;
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

                <!-- Botão de Cadastro com Google -->
                <button class="google-login-btn" id="custom-google-btn">
                    <div class="google-icon"></div>
                    Cadastrar com Google
                </button>

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

    <!-- Configuração do Google Identity Services (hidden) -->
    <div id="g_id_onload"
         data-client_id="299295953821-nqbqqb8va16klodnvebgdja6h40mogc5.apps.googleusercontent.com"
         data-context="signup"
         data-ux_mode="popup"
         data-callback="handleGoogleSignup"
         data-auto_prompt="false">
    </div>

    <script>
        // Função para lidar com o cadastro do Google
        function handleGoogleSignup(response) {
            console.log('Resposta do Google Signup:', response);
            
            const customGoogleBtn = document.getElementById('custom-google-btn');
            customGoogleBtn.classList.add('loading');
            customGoogleBtn.innerHTML = '<div class="google-icon"></div>Cadastrando...';
            
            // Enviar o token para o servidor para validação e cadastro
            fetch('../api/google-signup.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    credential: response.credential,
                    client_id: response.clientId 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cadastro realizado!',
                        text: data.message || 'Conta criada com sucesso com sua conta Google.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'home.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro no cadastro',
                        text: data.message || 'Erro ao criar conta com Google.'
                    });
                    
                    // Resetar botão
                    customGoogleBtn.classList.remove('loading');
                    customGoogleBtn.innerHTML = '<div class="google-icon"></div>Cadastrar com Google';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro de conexão. Tente novamente.'
                });
                
                // Resetar botão
                customGoogleBtn.classList.remove('loading');
                customGoogleBtn.innerHTML = '<div class="google-icon"></div>Cadastrar com Google';
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar o Google Identity Services para cadastro
            google.accounts.id.initialize({
                client_id: "299295953821-nqbqqb8va16klodnvebgdja6h40mogc5.apps.googleusercontent.com",
                callback: handleGoogleSignup,
                context: "signup",
                ux_mode: "popup"
            });

            // Configurar o botão personalizado do Google
            const customGoogleBtn = document.getElementById('custom-google-btn');
            if (customGoogleBtn) {
                customGoogleBtn.addEventListener('click', function() {
                    google.accounts.id.prompt();
                });
            }

            // Máscara para Telefone
            document.querySelector('input[name="telefone"]')?.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                }
                e.target.value = value;
            });

            // Máscara para CPF
            document.querySelector('input[name="cpf"]')?.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                }
                e.target.value = value;
            });

            // Loading state para o formulário tradicional
            const authForm = document.querySelector('.auth-form');
            const authBtn = authForm?.querySelector('.auth-btn-primary');
            
            if (authForm && authBtn) {
                authForm.addEventListener('submit', function(e) {
                    authBtn.classList.add('loading');
                    authBtn.disabled = true;
                    authBtn.innerHTML = 'CADASTRANDO...';
                    
                    // Timeout para evitar loading infinito
                    setTimeout(function() {
                        authBtn.classList.remove('loading');
                        authBtn.disabled = false;
                        authBtn.innerHTML = 'REGISTRAR';
                    }, 10000);
                });
            }
        });
    </script>
</body>
</html>