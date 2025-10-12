<?php
session_start();
ob_start();

include_once "../config/database.php";

$database = new Database();
$db = $database->getConnection();

// Verificar se o token foi passado
$token = filter_input(INPUT_GET, 'token', FILTER_DEFAULT);

if(empty($token)){
    $_SESSION['msg'] = "<p class='msg-error'>Token inválido!</p>";
    header("Location: redefinir.php");
    exit();
}

// Verificar se o formulário foi submetido
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    if(empty($nova_senha) || empty($confirmar_senha)){
        $_SESSION['msg'] = "<p class='msg-error'>Preencha todos os campos!</p>";
    } elseif($nova_senha != $confirmar_senha){
        $_SESSION['msg'] = "<p class='msg-error'>As senhas não coincidem!</p>";
    } elseif(strlen($nova_senha) < 6){
        $_SESSION['msg'] = "<p class='msg-error'>A senha deve ter pelo menos 6 caracteres!</p>";
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
                $_SESSION['msg'] = "<p class='msg-success'>Senha redefinida com sucesso! <a href='login.php'>Faça login</a></p>";
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['msg'] = "<p class='msg-error'>Erro ao redefinir senha!</p>";
            }
        } else {
            $_SESSION['msg'] = "<p class='msg-error'>Token inválido ou expirado!</p>";
            header("Location: redefinir.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Senha - DevCoffee</title>
    <style>
        .msg-error { color: #f00; }
        .msg-success { color: green; }
        form { margin: 20px 0; }
        label { display: block; margin-bottom: 5px; }
        input[type="password"] { padding: 8px; width: 300px; }
        input[type="submit"] { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        input[type="submit"]:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Criar Nova Senha</h1>

    <?php
    if(isset($_SESSION['msg'])){
        echo $_SESSION['msg'];
        unset($_SESSION['msg']);
    }
    ?>

    <form method="POST">
        <label for="nova_senha">Nova Senha:</label>
        <input type="password" name="nova_senha" placeholder="Digite a nova senha" required minlength="6">
        <br><br>
        
        <label for="confirmar_senha">Confirmar Nova Senha:</label>
        <input type="password" name="confirmar_senha" placeholder="Confirme a nova senha" required minlength="6">
        <br><br>
        
        <input type="submit" value="Redefinir Senha">
    </form>

    <p><a href="redefinir.php">Solicitar novo link</a> | <a href="login.php">Voltar ao login</a></p>
</body>
</html>