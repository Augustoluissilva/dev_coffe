<?php
session_start();
ob_start();

include_once "../config/database.php";

// Criar conexão
$database = new Database();
$db = $database->getConnection();

// Verificar se o token foi passado
$token = filter_input(INPUT_GET, 'token', FILTER_DEFAULT);

if(empty($token)){
    $_SESSION['msg'] = "<div class='message msg-error'>Token inválido!</div>";
    header("Location: redefinir.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevCoffee - Nova Senha</title>
    <link rel="stylesheet" href="../css/nova_senha.css">
</head>
<body>
    <div class="auth-split">
        <!-- Lado esquerdo - Formulário (invertido) -->
        <div class="auth-form-side">
            <!-- Logo no canto superior direito -->
            <div class="auth-logo">
                <img src="../img/devcoffee_logo.png" alt="DevCoffee Logo">
            </div>

            <div class="form-container">
                <h1 class="auth-title">Redefinir Senha</h1>
                <p class="auth-subtitle">Crie uma nova senha para sua conta</p>

                <?php
                // Verificar se o formulário foi submetido
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    $nova_senha = $_POST['nova_senha'] ?? '';
                    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
                    
                    if(empty($nova_senha) || empty($confirmar_senha)){
                        $_SESSION['msg'] = "<div class='message msg-error'>Preencha todos os campos!</div>";
                    } elseif($nova_senha != $confirmar_senha){
                        $_SESSION['msg'] = "<div class='message msg-error'>As senhas não coincidem!</div>";
                    } elseif(strlen($nova_senha) < 6){
                        $_SESSION['msg'] = "<div class='message msg-error'>A senha deve ter pelo menos 6 caracteres!</div>";
                    } else {
                        // Verificar se o token é válido e não expirou
                        $query = "SELECT * FROM usuarios WHERE token_senha = :token AND token_expiracao > NOW()";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':token', $token);
                        $stmt->execute();
                        
                        if($stmt->rowCount() == 1){
                            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            // Atualizar a senha
                            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                            
                            $query_update = "UPDATE usuarios 
                                           SET senha = :senha, 
                                               token_senha = NULL, 
                                               token_expiracao = NULL 
                                           WHERE token_senha = :token";
                            
                            $stmt_update = $db->prepare($query_update);
                            $stmt_update->bindParam(':senha', $senha_hash);
                            $stmt_update->bindParam(':token', $token);
                            
                            if($stmt_update->execute()){
                                $_SESSION['msg'] = "<div class='message msg-success'>Senha redefinida com sucesso! <a href='login.php'>Faça login</a></div>";
                                header("Refresh: 3; url=login.php");
                            } else {
                                $_SESSION['msg'] = "<div class='message msg-error'>Erro ao redefinir senha!</div>";
                            }
                        } else {
                            $_SESSION['msg'] = "<div class='message msg-error'>Token inválido ou expirado!</div>";
                            header("Refresh: 3; url=redefinir.php");
                        }
                    }
                }

                // Exibir mensagens
                if (isset($_SESSION['msg'])) {
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                }
                ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <input type="password" 
                               class="form-input"
                               name="nova_senha" 
                               placeholder="Nova senha" 
                               required 
                               minlength="6"
                               autocomplete="new-password">
                    </div>

                    <div class="form-group">
                        <input type="password" 
                               class="form-input"
                               name="confirmar_senha" 
                               placeholder="Confirme nova senha" 
                               required 
                               minlength="6"
                               autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn-primary">
                        ENVIAR
                    </button>
                </form>

                <div class="auth-links">
                    <a href="redefinir.php">Solicitar novo link</a>
                </div>
            </div>

            <!-- Mancha de café decorativa -->
            <div class="coffee-stain"></div>
        </div>

        <!-- Lado direito - Imagem com ilustração (invertido) -->
        <div class="auth-image-side">
                    <div class="illustration-image">
                        <img src="../img/nova_senha.png" alt="Problemas com a senha">
                    </div>
                    <h2 class="illustration-title">Problemas com a senha?</h2>
                    <p class="illustration-subtitle">Crie uma nova senha segura para acessar sua conta</p>
                </div>
            </div>
            
            <!-- Mancha de café decorativa -->
            <div class="coffee-stain"></div>
        </div>
    </div>
</body>
</html>