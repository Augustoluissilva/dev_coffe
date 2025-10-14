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
    <link rel="stylesheet" href="../css/redefinir.css">
</head>
<body>
    <div class="auth-split">
        <!-- Lado esquerdo - Imagem com texto -->
        <div class="auth-image-side">
            <div class="image-content">
                <div class="illustration-image">
                    <img src="../img/redefinir.png" alt="Recuperação de senha">
                </div>
                <h2 class="illustration-title">Problemas com a senha?</h2>
                <p class="illustration-subtitle">Não se preocupe! Envie seu email e nós te ajudaremos a recuperar o acesso à sua conta.</p>
            </div>
            
            <!-- Mancha de café decorativa -->
            <div class="coffee-stain"></div>
        </div>

        <!-- Lado direito - Formulário -->
        <div class="auth-form-side">
            <!-- Logo no canto superior direito -->
            <div class="auth-logo">
                <img src="../img/devcoffee_logo.png" alt="DevCoffee Logo">
            </div>

            <div class="form-container">
                <h1 class="auth-title">Recuperação de senha</h1>
                <p class="auth-subtitle">Informe seu email para recuperar sua senha:</p>

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
                        <input type="email" 
                               class="form-input"
                               name="usuario" 
                               placeholder="Email" 
                               value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>" 
                               required
                               autocomplete="email">
                    </div>

                    <button type="submit" name="SendRecupSenha" class="btn-primary">
                        ENVIAR
                    </button>
                </form>

                <div class="auth-links">
                    <a href="login.php">← Voltar para o login</a>
                </div>
            </div>

            <!-- Mancha de café decorativa -->
            <div class="coffee-stain"></div>
        </div>
    </div>
</body>
</html>