<?php
// teste_email.php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../phpmailer/src/Exception.php';
require_once __DIR__ . '/../phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'luis.agsilva22@gmail.com';
    $mail->Password = 'derx swor hrqv zshc';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    $mail->setFrom('luis.agsilva22@gmail.com', 'DevCoffee');
    $mail->addAddress('luis.agsilva22@gmail.com', 'Teste');
    
    $mail->isHTML(true);
    $mail->Subject = 'Teste PHPMailer';
    $mail->Body = '<h1>Funcionou!</h1><p>O PHPMailer está configurado corretamente.</p>';
    
    $mail->send();
    echo "✅ Email enviado com sucesso!";
} catch (Exception $e) {
    echo "❌ Erro: " . $mail->ErrorInfo;
}
?>