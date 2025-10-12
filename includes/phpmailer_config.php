<?php
// C:\xampp\htdocs\Coffe\includes\phpmailer_config.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluir PHPMailer manualmente (SEM Composer)
require_once __DIR__ . '/../phpmailer/src/Exception.php';
require_once __DIR__ . '/../phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../phpmailer/src/SMTP.php';

function enviarEmailRecuperacao($email, $nome, $token) {
    $mail = new PHPMailer(true);
    
    try {
        // Configurações do servidor - GMAIL
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'luis.agsilva22@gmail.com'; // SEU EMAIL GMAIL
        $mail->Password = 'derx swor hrqv zshc'; // SUA SENHA DE APP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Configurações adicionais para melhor compatibilidade
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Remetente (DEVE ser o MESMO email do Gmail)
        $mail->setFrom('luis.agsilva22@gmail.com', 'DevCoffee');
        $mail->addAddress($email, $nome);
        $mail->addReplyTo('nao-responder@devcoffee.com', 'DevCoffee');
        
        // Conteúdo do email
        $mail->isHTML(true);
        $mail->Subject = 'Redefinição de Senha - DevCoffee';
        
        $link = "http://" . $_SERVER['HTTP_HOST'] . "/Coffe/includes/nova_senha.php?token=" . $token;
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    background-color: #f4f4f4; 
                    margin: 0; 
                    padding: 20px; 
                }
                .container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    background: white; 
                    padding: 20px; 
                    border-radius: 8px; 
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .header { 
                    background: #6f4e37; 
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center; 
                    border-radius: 8px 8px 0 0; 
                }
                .content { 
                    padding: 30px; 
                    line-height: 1.6; 
                    color: #333;
                }
                .button { 
                    display: inline-block; 
                    padding: 15px 30px; 
                    background: #6f4e37; 
                    color: white; 
                    text-decoration: none; 
                    border-radius: 8px; 
                    font-size: 16px; 
                    font-weight: bold;
                    margin: 20px 0;
                }
                .footer { 
                    text-align: center; 
                    margin-top: 30px; 
                    padding-top: 20px; 
                    border-top: 1px solid #e0e0e0; 
                    color: #666; 
                    font-size: 12px; 
                }
                .link-box { 
                    background: #f9f9f9; 
                    padding: 15px; 
                    border-radius: 6px; 
                    margin: 20px 0; 
                    word-break: break-all; 
                    font-size: 14px;
                    border: 1px solid #e0e0e0;
                }
                .warning {
                    color: #d9534f;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1 style='margin:0;'>☕ DevCoffee</h1>
                </div>
                <div class='content'>
                    <h2 style='color: #6f4e37; margin-top: 0;'>Redefinição de Senha</h2>
                    <p>Olá, <strong>$nome</strong>!</p>
                    <p>Recebemos uma solicitação para redefinir sua senha. Clique no botão abaixo para criar uma nova senha:</p>
                    
                    <div style='text-align: center;'>
                        <a href='$link' class='button'>Redefinir Senha</a>
                    </div>
                    
                    <p>Se o botão não funcionar, copie e cole este link no seu navegador:</p>
                    <div class='link-box'>$link</div>
                    
                    <p class='warning'>⚠️ Este link expira em 1 hora.</p>
                    <p>Se você não solicitou a redefinição de senha, ignore este email.</p>
                    
                    <p>Atenciosamente,<br><strong>Equipe DevCoffee</strong></p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " DevCoffee. Todos os direitos reservados.</p>
                    <p>Este é um email automático, por favor não responda.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Email em texto puro para clientes que não suportam HTML
        $mail->AltBody = "Redefinição de Senha - DevCoffee\n\nOlá $nome!\n\nPara redefinir sua senha, acesse o link abaixo:\n\n$link\n\nEste link expira em 1 hora.\n\nSe você não solicitou esta redefinição, ignore este email.\n\nAtenciosamente,\nEquipe DevCoffee";
        
        // Habilitar debug se necessário (descomente para testar)
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = function($str, $level) {
        //     error_log("PHPMailer: $str");
        // };
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // Log do erro detalhado
        error_log("ERRO PHPMailer: " . $e->getMessage());
        error_log("ERRO Detalhado: " . $mail->ErrorInfo);
        return false;
    }
}
?>