<?php
session_start();

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cafeteria";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    $error_message = "Desculpe, estamos enfrentando problemas técnicos. Tente novamente mais tarde.";
}

$usuario_nome = isset($_SESSION['usuario_nome']) ? htmlspecialchars($_SESSION['usuario_nome']) : 'Visitante';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Coffee - Contato</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/contato.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Hero Banner do Contato -->
    <section class="contact-hero">
        <div class="container">
            <h1>Contato</h1>
            <p>Entre em contato conosco para mais informações ou suporte</p>
        </div>
    </section>

    <!-- Formulário de Contato -->
    <section class="contact-form">
        <div class="container">
            <form action="../processa_contato.php" method="POST">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="servico">Telefone</label>
                    <input type="text" id="servico" name="servico" required>
                </div>
                <div class="form-group">
                    <label for="mensagem">Mensagem</label>
                    <textarea id="mensagem" name="mensagem" rows="5" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Enviar</button>
            </form>
        </div>
    </section>

    <!-- Informações de Contato -->
    <section class="contact-info">
        <div class="container">
            <p>Caçapava-SP</p>
            <p>Horário de Funcionamento: Segunda - Sábado, 9:00 - 17:00</p>
            <p>Email: devcoffee.cafeteria25@gmail.com</p>
            <p>Telefone (12) 99999-9999</p>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
</body>
</html>