<?php
session_start();

// Verificar se usuário está logado, se não, redirecionar para login
if(!isset($_SESSION['usuario_id'])){
    header("Location: login.php");
    exit();
}

$usuario_nome = $_SESSION['usuario_nome'];
$usuario_tipo = $_SESSION['usuario_tipo'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Dev Coffee</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        .welcome-section {
            background: var(--black);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            margin-top: 70px;
        }

        .welcome-content {
            max-width: 800px;
        }

        .welcome-icon {
            font-size: 8rem;
            margin-bottom: 2rem;
            color: var(--main-color);
        }

        .welcome-title {
            color: var(--main-color);
            font-size: 4rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }

        .welcome-subtitle {
            color: #fff;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            font-weight: 300;
        }

        .user-name {
            color: var(--main-color);
            font-weight: 700;
        }

        .welcome-text {
            color: #fff;
            font-size: 1.8rem;
            line-height: 1.8;
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }

        .dashboard-card {
            background: var(--bg);
            border: var(--border);
            border-radius: 1rem;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            border-color: var(--main-color);
        }

        .card-icon {
            font-size: 4rem;
            color: var(--main-color);
            margin-bottom: 1.5rem;
        }

        .card-title {
            color: #fff;
            font-size: 2rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .card-description {
            color: #ccc;
            font-size: 1.4rem;
            line-height: 1.6;
        }

        .quick-actions {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 3rem;
            flex-wrap: wrap;
        }

        .action-btn {
            background: var(--main-color);
            color: #fff;
            padding: 1.2rem 2.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-size: 1.6rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid var(--main-color);
        }

        .action-btn:hover {
            background: transparent;
            color: var(--main-color);
            transform: translateY(-2px);
        }

        .action-btn.secondary {
            background: transparent;
            color: var(--main-color);
            border: 2px solid var(--main-color);
        }

        .action-btn.secondary:hover {
            background: var(--main-color);
            color: #fff;
        }

        @media (max-width: 768px) {
            .welcome-title {
                font-size: 3rem;
            }
            
            .welcome-subtitle {
                font-size: 2rem;
            }
            
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .action-btn {
                width: 100%;
                max-width: 300px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <section>
            <a href="home.php" class="logo">
                <img src="img/logo_devcoffe.png" alt="logo">
            </a>
            <nav class="navbar">
                <a href="home.php">Home</a>
                <a href="index.php#menu">Menu</a>
                <a href="index.php#about">Sobre</a>
                <a href="index.php#review">Avaliações</a>
                <a href="index.php#address">Endereço</a>
            </nav>
            <div class="icons">
                <span>Olá, <?php echo $usuario_nome; ?></span>
                <a href="perfil.php">Perfil</a>
                <a href="logout.php">Sair</a>
                <img width="25" height="25" src="https://img.icons8.com/ios-filled/30/ffffff/search--v2.png" alt="search--v2" />
                <img width="25" height="25" src="https://img.icons8.com/ios-glyphs/30/ffffff/shopping-cart--v1.png" alt="shopping-cart--v1" />
            </div>
        </section>
    </header>

    <section class="welcome-section">
        <div class="welcome-content">
            <div class="welcome-icon">☕</div>
            <h1 class="welcome-title">Bem-vindo de volta!</h1>
            <h2 class="welcome-subtitle">É bom te ver aqui, <span class="user-name"><?php echo $usuario_nome; ?></span>!</h2>
            
            <p class="welcome-text">
                Sua jornada pelo mundo do café continua. Explore nosso menu exclusivo, 
                descubra novas combinações e aproveite os melhores grãos selecionados 
                especialmente para você.
            </p>

            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <div class="card-icon">📱</div>
                    <h3 class="card-title">Fazer Pedido</h3>
                    <p class="card-description">Explore nosso menu completo e faça seu pedido online com facilidade</p>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-icon">📋</div>
                    <h3 class="card-title">Meus Pedidos</h3>
                    <p class="card-description">Acompanhe seus pedidos anteriores e favoritos</p>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-icon">⭐</div>
                    <h3 class="card-title">Favoritos</h3>
                    <p class="card-description">Acesse suas bebidas preferidas rapidamente</p>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-icon">🎁</div>
                    <h3 class="card-title">Promoções</h3>
                    <p class="card-description">Confira as ofertas exclusivas para você</p>
                </div>
            </div>

            <div class="quick-actions">
                <a href="index.php#menu" class="action-btn">Ver Cardápio Completo</a>
                <a href="perfil.php" class="action-btn secondary">Meu Perfil</a>
                <a href="pedidos.php" class="action-btn secondary">Meus Pedidos</a>
            </div>
        </div>
    </section>

    <section class="footer">
        <div style="text-align: center; color: #fff; padding: 2rem; font-size: 1.4rem;">
            <p>&copy; 2024 Dev Coffee. Todos os direitos reservados.</p>
        </div>
    </section>
</body>
</html>