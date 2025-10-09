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

                <div class="social-buttons">
                    <button class="social-btn facebook" type="button" aria-label="Cadastrar com Facebook">
                        <!-- Ícone via CSS -->
                    </button>
                    <button class="social-btn google" type="button" aria-label="Cadastrar com Google">
                        <!-- Ícone via CSS -->
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
    </script>
</body>
</html>