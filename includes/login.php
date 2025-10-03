<?php
session_start();

// Verificar se usuário já está logado
if(isset($_SESSION['usuario_id'])){
    header("Location: index.php");
    exit();
}

// Incluir arquivos com caminhos corrigidos
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
        $_SESSION['usuario_avatar'] = $usuario->avatar;
        
        header("Location: index.php");
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
    <title>Login - Dev Coffee</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        /* Estilos do login (mesmos do cadastro) */
        .auth-container {
            background: var(--black);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .auth-form {
            background: var(--bg);
            padding: 3rem;
            border-radius: 1rem;
            border: var(--border);
            width: 100%;
            max-width: 400px;
        }

        .auth-form h2 {
            color: var(--main-color);
            font-size: 3rem;
            text-align: center;
            margin-bottom: 2rem;
            text-transform: uppercase;
        }

        .form-group {
            margin-bottom: 2rem;
        }

        .form-group label {
            display: block;
            color: #fff;
            font-size: 1.6rem;
            margin-bottom: 0.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 1rem;
            background: var(--black);
            border: var(--border);
            border-radius: 0.5rem;
            color: #fff;
            font-size: 1.6rem;
        }

        .form-group input:focus {
            border-color: var(--main-color);
        }

        .auth-links {
            text-align: center;
            margin-top: 2rem;
        }

        .auth-links a {
            color: var(--main-color);
            font-size: 1.6rem;
            text-decoration: none;
            display: block;
            margin-bottom: 1rem;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .message {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 1.6rem;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <form class="auth-form" method="POST" action="">
            <h2>Login</h2>

            <?php if($mensagem): ?>
                <div class="message <?php echo $tipo_mensagem; ?>">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>

            <button type="submit" class="btn" style="width: 100%;">Entrar</button>

            <div class="auth-links">
                <a href="cadastro.php">Criar uma conta</a>
                <a href="#">Esqueci minha senha</a>
            </div>
        </form>
    </div>
</body>
</html>