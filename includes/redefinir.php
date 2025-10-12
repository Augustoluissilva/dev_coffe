<?php
session_start();
ob_start();

include_once "../config/database.php";

// Criar conexão
$database = new Database();
$db = $database->getConnection();

// Incluir a configuração do PHPMailer
include_once "phpmailer_config.php";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevCoffee - Recuperar Senha</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: linear-gradient(135deg, #6f4e37 0%, #8b6b4d 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .container { background: white; border-radius: 15px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2); overflow: hidden; width: 100%; max-width: 450px; }
        .header { background: #6f4e37; color: white; padding: 30px 20px; text-align: center; }
        .header h1 { font-size: 28px; margin-bottom: 10px; }
        .content { padding: 30px; }
        .message { padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .msg-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .msg-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .msg-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        input[type="email"] { width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; transition: border-color 0.3s ease; }
        input[type="email"]:focus { outline: none; border-color: #6f4e37; }
        .btn { width: 100%; padding: 14px; background: #6f4e37; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background 0.3s ease; }
        .btn:hover { background: #5a3e2b; }
        .links { text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e0e0e0; }
        .links a { color: #6f4e37; text-decoration: none; font-weight: bold; transition: color 0.3s ease; }
        .links a:hover { color: #5a3e2b; text-decoration: underline; }
        .coffee-icon { font-size: 40px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="coffee-icon">☕</div>
            <h1>DevCoffee</h1>
            <p>Recuperação de Senha</p>
        </div>

        <div class="content">
            <?php
            // Verificar se o formulário foi enviado
            if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['SendRecupSenha'])){
                
                $email = trim($_POST['usuario'] ?? '');
                
                // Validar email
                if(empty($email)) {
                    $_SESSION['msg'] = "<div class='message msg-error'>❌ Por favor, digite seu email!</div>";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $_SESSION['msg'] = "<div class='message msg-error'>❌ Por favor, digite um email válido!</div>";
                } else {
                    try {
                        // Verificar se o email existe no banco
                        $query_usuario = "SELECT id_usuario, nome_completo, email FROM usuarios WHERE email = :email LIMIT 1";
                        $stmt = $db->prepare($query_usuario);
                        $stmt->bindParam(':email', $email);
                        $stmt->execute();
                        
                        if($stmt->rowCount() > 0) {
                            $row_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            // Gerar token
                            $token = bin2hex(random_bytes(25));
                            $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));
                            
                            // Verificar se as colunas de token existem, se não, criar
                            $check_columns = $db->query("SHOW COLUMNS FROM usuarios LIKE 'token_senha'");
                            if($check_columns->rowCount() == 0) {
                                $db->exec("ALTER TABLE usuarios ADD COLUMN token_senha VARCHAR(100) NULL");
                            }
                            
                            $check_columns = $db->query("SHOW COLUMNS FROM usuarios LIKE 'token_expiracao'");
                            if($check_columns->rowCount() == 0) {
                                $db->exec("ALTER TABLE usuarios ADD COLUMN token_expiracao DATETIME NULL");
                            }
                            
                            // Salvar token no banco
                            $query_token = "UPDATE usuarios 
                                           SET token_senha = :token, 
                                               token_expiracao = :expiracao 
                                           WHERE email = :email";
                            
                            $stmt_token = $db->prepare($query_token);
                            $stmt_token->bindParam(':token', $token);
                            $stmt_token->bindParam(':expiracao', $expiracao);
                            $stmt_token->bindParam(':email', $email);
                            
                            if($stmt_token->execute()) {
                                // Enviar email
                                $nome_usuario = $row_usuario['nome_completo'] ?? $row_usuario['nome'] ?? 'Usuário';
                                
                                // DEBUG: Mostrar informações antes do envio
                                echo "<!-- DEBUG: Tentando enviar para: $email -->";
                                
                                if(enviarEmailRecuperacao($email, $nome_usuario, $token)) {
                                    $_SESSION['msg'] = "<div class='message msg-success'>✅ Email enviado com sucesso!</div>";
                                    $_SESSION['msg'] .= "<div class='message msg-info'>📧 Verifique sua caixa de entrada e a pasta de spam.</div>";
                                } else {
                                    $_SESSION['msg'] = "<div class='message msg-error'>❌ Erro ao enviar email. Tente novamente.</div>";
                                }
                            } else {
                                $_SESSION['msg'] = "<div class='message msg-error'>❌ Erro ao processar solicitação!</div>";
                            }
                        } else {
                            $_SESSION['msg'] = "<div class='message msg-error'>❌ Nenhum usuário encontrado com este email!</div>";
                        }
                    } catch (Exception $e) {
                        $_SESSION['msg'] = "<div class='message msg-error'>❌ Erro: " . $e->getMessage() . "</div>";
                    }
                }
                
                // Redirecionar para evitar reenvio
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }

            // Exibir mensagens
            if (isset($_SESSION['msg'])) {
                echo $_SESSION['msg'];
                unset($_SESSION['msg']);
            }
            ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="email">Digite seu email:</label>
                    <input type="email" 
                           name="usuario" 
                           id="email"
                           placeholder="seu.email@exemplo.com" 
                           value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>" 
                           required
                           autocomplete="email">
                </div>

                <button type="submit" name="SendRecupSenha" class="btn">
                    🔐 Enviar Link de Recuperação
                </button>
            </form>

            <div class="links">
                <p>
                    Lembrou a senha? <a href="login.php">Faça login</a><br>
                    Não tem conta? <a href="cadastro.php">Cadastre-se</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>