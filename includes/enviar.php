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
    <title>Dev Coffee - Mensagem Enviada | Obrigado!</title>
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
            overflow-x: hidden;
            background: #f8f4f0;
        }

        /* Hero Section - Café Escuro */
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

        /* Success Section - Creme/Café com Leite */
        .success-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #fff8e1 0%, #f5e6cc 100%);
            position: relative;
        }

        .success-card {
            max-width: 800px;
            margin: 0 auto;
            background: linear-gradient(145deg, #ffffff 0%, #f9f5f0 100%);
            border-radius: 24px;
            padding: 60px 40px;
            box-shadow: 
                0 25px 50px -12px rgba(62, 39, 35, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.8) inset;
            text-align: center;
            position: relative;
            overflow: hidden;
            transform: translateY(0);
            animation: slideUp 0.8s ease-out;
            border: 1px solid rgba(139, 110, 99, 0.1);
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

        .success-card::before {
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

        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #6d4c41 0%, #8d6e63 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 20px 40px rgba(109, 76, 65, 0.3);
            animation: pulse 2s infinite;
            position: relative;
        }

        .success-icon::after {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            background: linear-gradient(45deg, #ffcc02, #ff9800, #ffcc02);
            border-radius: 50%;
            z-index: -1;
            opacity: 0.3;
            animation: rotate 3s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 20px 40px rgba(109, 76, 65, 0.3); }
            50% { transform: scale(1.05); box-shadow: 0 25px 50px rgba(109, 76, 65, 0.4); }
            100% { transform: scale(1); box-shadow: 0 20px 40px rgba(109, 76, 65, 0.3); }
        }

        .success-icon i {
            font-size: 3rem;
            color: #fff5e6;
            z-index: 2;
            position: relative;
        }

        .success-card h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #3e2723;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .success-card p {
            font-size: 1.2rem;
            color: #5d4037;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #6d4c41 0%, #8d6e63 100%);
            color: #fff5e6;
            padding: 16px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(109, 76, 65, 0.3);
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(109, 76, 65, 0.4);
            background: linear-gradient(135deg, #5d4037 0%, #6d4c41 100%);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .cta-button:active {
            transform: translateY(-1px);
        }

        /* Contact Info Section - Madeira Escura */
        .contact-info {
            padding: 80px 0;
            background: linear-gradient(135deg, #4e342e 0%, #3e2723 100%);
            color: #fff5e6;
        }

        .contact-info .container {
            text-align: center;
        }

        .contact-info h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #d7ccc8, #bcaaa4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .contact-item {
            background: rgba(255, 245, 230, 0.1);
            padding: 30px 20px;
            border-radius: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 245, 230, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .contact-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .contact-item:hover::before {
            left: 100%;
        }

        .contact-item:hover {
            transform: translateY(-8px);
            background: rgba(255, 245, 230, 0.2);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .contact-item i {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #d7ccc8, #bcaaa4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            display: block;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }

        .contact-item p {
            margin: 0;
            font-size: 1.1rem;
            opacity: 0.95;
        }

        .contact-item small {
            opacity: 0.7;
            font-size: 0.9rem;
        }

        /* Floating Coffee Beans */
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

        .coffee-bean:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .coffee-bean:nth-child(2) {
            top: 60%;
            right: 15%;
            animation-delay: -2s;
            animation-duration: 8s;
        }

        .coffee-bean:nth-child(3) {
            top: 80%;
            left: 20%;
            animation-delay: -1s;
            animation-duration: 7s;
        }

        @keyframes floatCoffee {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg); 
                opacity: 0.6;
            }
            50% { 
                transform: translateY(-20px) rotate(180deg); 
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .contact-hero h1 {
                font-size: 2.5rem;
            }
            
            .success-card {
                margin: 0 20px;
                padding: 40px 30px;
            }
            
            .success-card h1 {
                font-size: 2rem;
            }
            
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
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
            <h1><i class="fas fa-coffee"></i> Contato</h1>
            <p>☕ Sua mensagem chegou quentinha na nossa cafeteria! Recebemos com todo carinho ☕</p>
        </div>
    </section>

    <!-- Success Section -->
    <section class="success-section">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Mensagem Recebida! ☕</h1>
            <p>Obrigado por entrar em contato! Nossa equipe barista já anotou seu pedido e entrará em contato em breve. Que tal um cappuccino enquanto espera? ✨</p>
            
            <a href="home.php" class="cta-button">
                <i class="fas fa-home"></i> Voltar ao Cardápio
            </a>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="contact-info">
        <div class="container">
            <h2><i class="fas fa-map-marker-alt"></i> Visite Nossa Cafeteria</h2>
            <div class="contact-grid">
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>Caçapava - SP<br><small>☕ O aroma do café te espera!</small></p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-clock"></i>
                    <p>Seg - Sáb: 9:00 - 17:00<br><small>Dom: Descanso merecido ☕</small></p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <p>devcoffee.cafeteria25@gmail.com</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <p>(12) 99999-9999</p>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Animação de vapor do café
        function createSteam() {
            const successIcon = document.querySelector('.success-icon');
            for(let i = 0; i < 3; i++) {
                setTimeout(() => {
                    const steam = document.createElement('div');
                    steam.style.cssText = `
                        position: absolute;
                        width: 4px; height: 8px;
                        background: rgba(255, 255, 255, 0.6);
                        border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
                        bottom: 100%; left: 50%;
                        animation: steamRise 2s ease-out forwards;
                        z-index: 3;
                    `;
                    steam.style.animationDelay = `${i * 0.5}s`;
                    successIcon.appendChild(steam);
                    
                    setTimeout(() => steam.remove(), 2000);
                }, i * 1000);
            }
        }

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

        // Inicia animações
        createSteam();
        setInterval(createSteam, 5000);
        createMoreCoffeeBeans();

        // Keyframes para vapor
        const style = document.createElement('style');
        style.textContent = `
            @keyframes steamRise {
                0% { transform: translateY(0) scale(1) rotate(0deg); opacity: 1; }
                100% { transform: translateY(-60px) scale(2) rotate(180deg); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>