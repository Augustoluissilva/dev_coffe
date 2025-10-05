<?php
session_start();

// Verificar se usuário já está logado
if(isset($_SESSION['usuario_id'])){
    header("Location: index.php");
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
    $usuario->email = $_POST['email'];
    $senha = $_POST['senha'];
    $usuario->senha = $senha;

    if($usuario->login()){
        $_SESSION['usuario_id'] = $usuario->id;
        $_SESSION['usuario_nome'] = $usuario->nome;
        $_SESSION['usuario_tipo'] = $usuario->tipo;
        
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
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>
    <div class="auth-container">
        <!-- Lado Esquerdo - Background Café -->
        <div class="auth-left">
            <a href="index.php" class="auth-logo">devcoffee</a>
            <div class="auth-left-content">
                <h1 class="auth-left-title">Olá!</h1>
                <p class="auth-left-subtitle">Entre com suas informações e viva sua melhor experiência!</p>
                <a href="cadastro.php" class="auth-btn-outline">REGISTRAR</a>
            </div>
        </div>

        <!-- Lado Direito - Formulário -->
        <div class="auth-right">
            <div class="coffee-splash"></div>
            <div class="auth-form-container">
                <h1 class="auth-title">Entrar</h1>
                
                <?php if($mensagem): ?>
                    <div class="auth-message <?php echo $tipo_mensagem; ?>">
                        <?php echo $mensagem; ?>
                    </div>
                <?php endif; ?>

                <div class="social-buttons">
                    <button class="social-btn">
                        <img src="https://img.icons8.com/color/48/000000/facebook.png" alt="Facebook" class="social-icon">
                    </button>
                    <button class="social-btn">
                        <img src="https://img.icons8.com/color/48/000000/google-logo.png" alt="Google" class="social-icon">
                    </button>
                </div>

                <p class="auth-divider">ou use sua conta</p>

                <form class="auth-form" method="POST" action="">
                    <div class="form-group">
                        <input type="email" class="form-input" name="email" placeholder="Email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <input type="password" class="form-input" name="senha" placeholder="Senha" required>
                    </div>

                    <a href="#" class="forgot-link">Esqueceu a senha?</a>

                    <button type="submit" class="auth-btn-primary">LOGIN</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>