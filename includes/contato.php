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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #2d1b14;
            background: #f8f4f0;
            overflow-x: hidden;
        }

        /* Hero Section */
        .contact-hero {
            background: linear-gradient(135deg, #3e2723 0%, #5d4037 50%, #8d6e63 100%);
            color: #fff5e6;
            padding: 120px 0 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .contact-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,245,230,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,245,230,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .contact-hero .container {
            position: relative;
            z-index: 2;
        }

        .contact-hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .contact-hero p {
            font-size: 1.2rem;
            opacity: 0.95;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Form Section */
        .contact-form-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #fff8e1 0%, #f5e6cc 100%);
        }

        .form-container {
            max-width: 900px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: start;
        }

        .form-wrapper {
            background: linear-gradient(145deg, #ffffff 0%, #f9f5f0 100%);
            border-radius: 24px;
            padding: 50px;
            box-shadow: 
                0 25px 50px -12px rgba(62, 39, 35, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.8) inset;
            border: 1px solid rgba(139, 110, 99, 0.1);
            position: relative;
            overflow: hidden;
            animation: slideUp 0.8s ease-out;
        }

        .form-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #6d4c41, #8d6e63, #a1887f, #d7ccc8);
            background-size: 300% 300%;
            animation: gradientShift 3s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-wrapper h2 {
            color: #3e2723;
            font-size: 1.8rem;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            color: #5d4037;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid rgba(139, 110, 99, 0.2);
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #6d4c41;
            background: white;
            box-shadow: 0 0 0 3px rgba(109, 76, 65, 0.1);
            transform: translateY(-2px);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-btn {
            background: linear-gradient(135deg, #6d4c41 0%, #8d6e63 100%);
            color: #fff5e6;
            padding: 18px 40px;
            border: none;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(109, 76, 65, 0.3);
            width: 100%;
            position: relative;
            overflow: hidden;
            margin-top: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(109, 76, 65, 0.4);
            background: linear-gradient(135deg, #5d4037 0%, #6d4c41 100%);
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        /* Contact Info Sidebar */
        .contact-info-sidebar {
            background: linear-gradient(145deg, #ffffff 0%, #f9f5f0 100%);
            border-radius: 24px;
            padding: 50px 30px;
            box-shadow: 
                0 25px 50px -12px rgba(62, 39, 35, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.8) inset;
            border: 1px solid rgba(139, 110, 99, 0.1);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .contact-info-sidebar h3 {
            color: #3e2723;
            font-size: 1.5rem;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding: 15px;
            background: rgba(255, 245, 230, 0.5);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            transform: translateX(5px);
            background: rgba(255, 245, 230, 0.8);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .contact-item i {
            font-size: 1.5rem;
            color: #6d4c41;
            width: 40px;
            text-align: center;
            margin-right: 15px;
        }

        .contact-item div {
            flex: 1;
        }

        .contact-item div p {
            margin: 0;
            color: #5d4037;
            font-size: 0.95rem;
        }

        .contact-item div small {
            color: #8d6e63;
            font-size: 0.85rem;
        }

        /* Floating Elements */
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
            pointer-events: none;
        }

        .coffee-bean {
            position: absolute;
            width: 20px;
            height: 12px;
            background: radial-gradient(ellipse at center, #6d4c41 0%, #4e342e 70%);
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            animation: floatCoffee 6s ease-in-out infinite;
        }

        .coffee-bean::before {
            content: '';
            position: absolute;
            width: 8px;
            height: 8px;
            background: #3e2723;
            border-radius: 50%;
            top: 2px;
            left: 2px;
        }

        .coffee-bean:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
        .coffee-bean:nth-child(2) { top: 60%; right: 15%; animation-delay: -2s; animation-duration: 8s; }
        .coffee-bean:nth-child(3) { top: 80%; left: 20%; animation-delay: -1s; animation-duration: 7s; }

        @keyframes floatCoffee {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.6; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .contact-hero h1 { font-size: 2.5rem; }
            .form-container {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .form-wrapper { padding: 40px 30px; }
            .contact-info-sidebar { order: -1; }
        }

        @media (max-width: 480px) {
            .form-wrapper, .contact-info-sidebar { padding: 30px 20px; }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Hero Banner -->
    <section class="contact-hero">
        <div class="floating-elements">
            <div class="coffee-bean"></div>
            <div class="coffee-bean"></div>
            <div class="coffee-bean"></div>
        </div>
        <div class="container">
            <h1><i class="fas fa-coffee"></i> Fale Conosco</h1>
            <p>☕ Deixe sua mensagem que preparamos um café pra você enquanto conversamos!</p>
        </div>
    </section>

    <!-- Form Section -->
    <section class="contact-form-section">
        <div class="container">
            <div class="form-container">
                <!-- Formulário -->
                <div class="form-wrapper">
                    <h2><i class="fas fa-edit"></i> Envie sua Mensagem</h2>
                    <form action="../includes/email_template.html" method="POST">
                        <div class="form-group">
                            <label for="nome"><i class="fas fa-user"></i> Nome Completo</label>
                            <input type="text" id="nome" name="nome" placeholder="Ex: João Silva" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
                            <input type="email" id="email" name="email" placeholder="seu@email.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telefone"><i class="fas fa-phone"></i> Telefone/WhatsApp</label>
                            <input type="tel" id="telefone" name="telefone" placeholder="(12) 99999-9999" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="mensagem"><i class="fas fa-comment"></i> Mensagem</label>
                            <textarea id="mensagem" name="mensagem" placeholder="Conte-nos o que você precisa... ☕" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Enviar Mensagem
                        </button>
                    </form>
                </div>

                <!-- Informações de Contato -->
                <div class="contact-info-sidebar">
                    <h3><i class="fas fa-map-marker-alt"></i> Estamos Aqui!</h3>
                    
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <p><strong>Caçapava - SP</strong></p>
                            <small>O aroma do café te espera!</small>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <p>Seg - Sáb: 9h às 17h</p>
                            <small>Dom: Descanso ☕</small>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <p>devcoffee.cafeteria25@gmail.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <p>(12) 99999-9999</p>
                            <small>WhatsApp disponível</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Animações do formulário
        document.querySelectorAll('.form-group input, .form-group textarea').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Efeito de digitação no placeholder da mensagem
        const mensagemInput = document.getElementById('mensagem');
        const messages = [
            "Gostaria de fazer um evento aqui?",
            "Precisa de mais informações sobre nossos cafés?",
            "Quer reservar uma mesa especial?",
            "Dúvidas sobre delivery? Conte-nos!",
            "Qualquer outra dúvida, estamos aqui ☕"
        ];
        
        let messageIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        
        function typeMessage() {
            const currentMessage = messages[messageIndex];
            if (isDeleting) {
                mensagemInput.placeholder = currentMessage.substring(0, charIndex - 1);
                charIndex--;
            } else {
                mensagemInput.placeholder = currentMessage.substring(0, charIndex + 1);
                charIndex++;
            }
            
            let typeSpeed = isDeleting ? 50 : 100;
            setTimeout(typeMessage, typeSpeed);
            
            if (!isDeleting && charIndex === currentMessage.length) {
                setTimeout(() => { isDeleting = true; }, 2000);
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                messageIndex = (messageIndex + 1) % messages.length;
            }
        }
        
        typeMessage();

        // Criação dinâmica de grãos de café
        function createMoreCoffeeBeans() {
            const floatingContainer = document.querySelector('.floating-elements');
            for(let i = 4; i < 10; i++) {
                const bean = document.createElement('div');
                bean.className = 'coffee-bean';
                bean.style.left = Math.random() * 100 + '%';
                bean.style.top = Math.random() * 100 + '%';
                bean.style.animationDelay = Math.random() * 6 + 's';
                bean.style.animationDuration = (Math.random() * 3 + 5) + 's';
                floatingContainer.appendChild(bean);
            }
        }
        createMoreCoffeeBeans();
    </script>
</head>
<body>
</html>