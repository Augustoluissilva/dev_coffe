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
    $result_products = null;
} else {
    $sql_products = "SELECT p.*, c.nome as categoria_nome 
                    FROM produtos p 
                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                    WHERE p.disponivel = 1 
                    ORDER BY p.id_produto DESC 
                    LIMIT 8";
    $result_products = $conn->query($sql_products);
}

if (!isset($_SESSION['usuario_id'])) {
    // header("Location: login.php"); // Descomentado apenas se login for obrigatório
    // exit();
}
$usuario_nome = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Visitante';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Coffee - Café todo dia!</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        :root {
            --primary: #2C1810;
            --secondary: #8B4513;
            --accent: #D2691E;
            --light: #F8F8F8;
            --white: #FFFFFF;
            --text: #333333;
            --gray: #666666;
            --border: #E5E5E5;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            color: var(--text);
            line-height: 1.6;
            background-color: var(--white);
            font-size: 14px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* ===== HEADER SUPERIOR ===== */
        .top-header {
            background: var(--primary);
            color: var(--white);
            padding: 8px 0;
            font-size: 12px;
        }

        .top-header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .contact-info {
            display: flex;
            gap: 20px;
        }

        .contact-info span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .user-menu {
            display: flex;
            gap: 15px;
        }

        .user-menu a {
            color: var(--white);
            text-decoration: none;
            transition: color 0.3s;
        }

        .user-menu a:hover {
            color: var(--accent);
        }

        /* ===== HEADER PRINCIPAL ===== */
        .main-header {
            background: var(--primary);
            padding: 20px 0;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo {
            height: 50px;
            width: auto;
            transition: transform 0.3s ease;
        }

        .logo-container:hover .logo {
            transform: scale(1.1) rotate(5deg);
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: var(--white);
        }

        .logo-text span {
            color: var(--accent);
        }

        /* Navigation */
        .main-nav {
            display: flex;
        }

        .main-nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .main-nav ul li a {
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
            transition: color 0.3s;
            position: relative;
        }

        .main-nav ul li a:hover {
            color: var(--accent);
        }

        .main-nav ul li a.active {
            color: var(--accent);
            font-weight: 600;
        }

        .main-nav ul li a.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--accent);
        }

        /* Header Actions */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-container {
            position: relative;
        }

        .search-bar {
            padding: 8px 15px 8px 35px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            width: 200px;
            font-size: 13px;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
        }

        .search-bar::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .search-bar:focus {
            outline: none;
            border-color: var(--accent);
            width: 250px;
            background: rgba(255, 255, 255, 0.15);
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
        }

        .cart-icon {
            position: relative;
            color: var(--white);
            font-size: 18px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .cart-icon:hover {
            color: var(--accent);
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--accent);
            color: var(--white);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-greeting {
            font-size: 13px;
            color: var(--white);
        }

        .mobile-menu {
            display: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--white);
            transition: color 0.3s;
        }

        .mobile-menu:hover {
            color: var(--accent);
        }

        /* ===== HERO BANNER ===== */
        .hero-banner {
            background: linear-gradient(rgba(44, 24, 16, 0.8), rgba(44, 24, 16, 0.8)), url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .hero-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .hero-subtitle {
            font-size: 16px;
            color: var(--accent);
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hero-title {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-description {
            font-size: 16px;
            margin-bottom: 30px;
            color: rgba(255, 255, 255, 0.8);
        }

        .cta-button {
            display: inline-block;
            background: var(--accent);
            color: var(--white);
            padding: 12px 30px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .cta-button:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        /* ===== CATEGORIAS ===== */
        .categories {
            padding: 50px 0;
            background: var(--light);
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h2 {
            font-size: 28px;
            color: var(--primary);
            font-weight: 600;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .category-card {
            background: var(--white);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s;
            text-align: center;
            padding: 20px;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            background: var(--light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary);
            font-size: 24px;
        }

        .category-card h3 {
            font-size: 16px;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .category-card p {
            font-size: 13px;
            color: var(--gray);
        }

        /* ===== PRODUTOS ===== */
        .products-section {
            padding: 60px 0;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
        }

        .product-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
        }

        .product-card:hover {
            box-shadow: var(--shadow);
            border-color: var(--secondary);
        }

        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--secondary);
            color: var(--white);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            z-index: 2;
        }

        .product-badge.new {
            background: var(--accent);
        }

        .product-badge.out-of-stock {
            background: #dc3545;
        }

        .product-image {
            height: 200px;
            overflow: hidden;
            position: relative;
            background: var(--light);
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-info {
            padding: 15px;
        }

        .product-category {
            font-size: 11px;
            color: var(--gray);
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .product-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 10px;
            line-height: 1.4;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-description {
            font-size: 12px;
            color: var(--gray);
            margin-bottom: 10px;
            line-height: 1.4;
            height: 34px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .current-price {
            font-size: 16px;
            font-weight: 700;
            color: var(--secondary);
        }

        .original-price {
            font-size: 13px;
            color: var(--gray);
            text-decoration: line-through;
        }

        .product-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 12px;
            color: var(--gray);
        }

        .product-weight, .product-stock {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stock-available {
            color: #28a745;
            font-weight: 600;
        }

        .stock-out {
            color: #dc3545;
            font-weight: 600;
        }

        .add-to-cart {
            width: 100%;
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 10px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            text-transform: uppercase;
        }

        .add-to-cart:hover {
            background: var(--secondary);
        }

        .add-to-cart:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* ===== BANNER PROMOCIONAL ===== */
        .promo-banner {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: var(--white);
            padding: 40px 0;
            text-align: center;
        }

        .promo-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .promo-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .promo-subtitle {
            font-size: 16px;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .promo-button {
            display: inline-block;
            background: var(--white);
            color: var(--primary);
            padding: 12px 30px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            transition: all 0.3s;
        }

        .promo-button:hover {
            background: var(--accent);
            color: var(--white);
        }

        /* ===== NEWSLETTER ===== */
        .newsletter {
            background: var(--light);
            padding: 50px 0;
            text-align: center;
        }

        .newsletter-content {
            max-width: 500px;
            margin: 0 auto;
        }

        .newsletter-title {
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .newsletter-subtitle {
            font-size: 14px;
            color: var(--gray);
            margin-bottom: 25px;
        }

        .newsletter-form {
            display: flex;
            gap: 10px;
        }

        .newsletter-input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 13px;
        }

        .newsletter-input:focus {
            outline: none;
            border-color: var(--secondary);
        }

        .newsletter-button {
            background: var(--secondary);
            color: var(--white);
            border: none;
            padding: 12px 25px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            transition: background 0.3s;
        }

        .newsletter-button:hover {
            background: var(--primary);
        }

        /* ===== FOOTER ===== */
        .footer {
            background: var(--primary);
            color: var(--white);
            padding: 50px 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-column h3 {
            font-size: 16px;
            margin-bottom: 20px;
            color: var(--white);
            font-weight: 600;
        }

        .footer-column p, .footer-column a {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 10px;
            display: block;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s;
        }

        .footer-column a:hover {
            color: var(--accent);
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transition: all 0.3s;
            color: var(--white);
        }

        .social-links a:hover {
            background: var(--accent);
            transform: translateY(-2px);
        }

        .payment-methods {
            margin-top: 20px;
        }

        .payment-methods img {
            height: 25px;
            margin-right: 5px;
            opacity: 0.7;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
        }

        .no-products {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }

        .no-products i {
            font-size: 64px;
            margin-bottom: 20px;
            color: var(--border);
        }

        .no-products h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--primary);
        }

        .no-products p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .admin-link {
            display: inline-block;
            background: var(--accent);
            color: var(--white);
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }

        .admin-link:hover {
            background: var(--secondary);
        }

        /* ===== RESPONSIVIDADE ===== */
        @media (max-width: 1024px) {
            .categories-grid,
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .footer-content {
                grid-template-columns: 1fr 1fr;
                gap: 30px;
            }
        }

        @media (max-width: 768px) {
            .top-header {
                display: none;
            }
            
            .main-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: var(--primary);
                padding: 20px 0;
                box-shadow: var(--shadow);
            }
            
            .main-nav.active {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            
            .main-nav ul {
                flex-direction: column;
                gap: 15px;
            }
            
            .mobile-menu {
                display: block;
            }
            
            .search-bar {
                width: 150px;
            }
            
            .search-bar:focus {
                width: 180px;
            }
            
            .categories-grid,
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .hero-title {
                font-size: 36px;
            }
            
            .newsletter-form {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .categories-grid,
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
            }
            
            .hero-title {
                font-size: 28px;
            }
            
            .logo-text {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header Superior -->
    <div class="top-header">
        <div class="container top-header-container">
            <div class="contact-info">
                <span><i class="fas fa-phone"></i> (11) 9999-9999</span>
                <span><i class="fas fa-envelope"></i> contato@devcoffee.com.br</span>
            </div>
            <div class="user-menu">
                <a href="#"><i class="fas fa-heart"></i> Favoritos</a>
                <span class="user-greeting">Olá, <?php echo htmlspecialchars($usuario_nome); ?></span>
            </div>
        </div>
    </div>

    <!-- Header Principal -->
    <header class="main-header">
        <div class="container header-container">
            <div class="logo-container">
                <img src="../img/devcoffee_logo.png" alt="Dev Coffee Logo" class="logo">
            
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="#" class="active">Home</a></li>
                    <li><a href="produtos.php">Produtos</a></li>
                    <li><a href="#">Categorias</a></li>
                    <li><a href="#">Sobre</a></li>
                    <li><a href="#">Contato</a></li>
                    <!-- <li><a href="admin_login.php">Admin</a></li> -->
                </ul>
            </nav>
            
            <div class="header-actions">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-bar" placeholder="Pesquisar produtos..." id="search-bar">
                </div>
                <a href="#" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </a>
                <div class="mobile-menu">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="container hero-content">
            <div class="hero-subtitle">Café Especial</div>
            <h1 class="hero-title">Descubra o Melhor Café do Brasil</h1>
            <p class="hero-description">Grãos selecionados, torra artesanal e sabor incomparável. Experimente a excelência em cada xícara.</p>
            <a href="#produtos" class="cta-button">Ver Produtos</a>
        </div>
    </section>

    <!-- Categorias -->
    <section class="categories">
        <div class="container">
            <div class="section-title">
                <h2>Nossas Categorias</h2>
            </div>
            <div class="categories-grid">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-coffee"></i>
                    </div>
                    <h3>Café em Grãos</h3>
                    <p>Grãos selecionados para o melhor sabor</p>
                </div>
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-wine-bottle"></i>
                    </div>
                    <h3>Café Moído</h3>
                    <p>Pronto para preparo imediato</p>
                </div>
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3>Cápsulas</h3>
                    <p>Praticidade e qualidade</p>
                </div>
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-mug-hot"></i>
                    </div>
                    <h3>Acessórios</h3>
                    <p>Complete sua experiência</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Produtos em Destaque -->
    <section class="products-section" id="produtos">
        <div class="container">
            <div class="section-title">
                <h2>Nossos Produtos</h2>
            </div>
            
            <?php if ($result_products && $result_products->num_rows > 0): ?>
                <div class="products-grid" id="products-grid">
                    <?php while ($product = $result_products->fetch_assoc()): 
                        $isNew = $product['id_produto'] > 0;
                        $isOutOfStock = $product['estoque'] <= 0;
                    ?>
                        <div class="product-card" data-name="<?= htmlspecialchars($product['nome']) ?>" data-description="<?= htmlspecialchars($product['descricao'] ?? '') ?>">
                            <?php if ($isOutOfStock): ?>
                                <div class="product-badge out-of-stock">Esgotado</div>
                            <?php elseif ($isNew): ?>
                                <div class="product-badge new">Novo</div>
                            <?php endif; ?>
                            
                            <div class="product-image">
                                <?php if (!empty($product['imagem']) && file_exists('../' . $product['imagem'])): ?>
                                    <img src="<?= htmlspecialchars('../' . $product['imagem']) ?>" 
                                         alt="<?= htmlspecialchars($product['nome']) ?>"
                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhGOEY4Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRlIiBkeT0iLjNlbSI+SW1hZ2VtIG7Do28gY2FycmVnYWRhPC90ZXh0Pjwvc3ZnPg=='">
                                <?php else: ?>
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#999;flex-direction:column;gap:10px;">
                                        <i class="fas fa-image" style="font-size:48px;"></i>
                                        <small>Sem imagem</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-info">
                                <div class="product-category"><?= htmlspecialchars($product['categoria_nome'] ?? 'Geral') ?></div>
                                <h3 class="product-title"><?= htmlspecialchars($product['nome']) ?></h3>
                                
                                <?php if (!empty($product['descricao'])): ?>
                                    <p class="product-description">
                                        <?= htmlspecialchars(substr($product['descricao'], 0, 80)) . (strlen($product['descricao']) > 80 ? '...' : '') ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="product-price">
                                    <span class="current-price">R$ <?= number_format($product['preco'], 2, ',', '.') ?></span>
                                </div>
                                
                                <div class="product-details">
                                    <?php if ($product['peso']): ?>
                                        <div class="product-weight">
                                            <i class="fas fa-weight"></i> <?= number_format($product['peso'], 0, ',', '.') ?>g
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="product-stock">
                                        <i class="fas fa-box"></i>
                                        <span class="<?= $product['estoque'] > 0 ? 'stock-available' : 'stock-out' ?>">
                                            <?= $product['estoque'] > 0 ? $product['estoque'] . ' un' : 'Esgotado' ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <button class="add-to-cart" 
                                        data-product-id="<?= $product['id_produto'] ?>"
                                        data-product-name="<?= htmlspecialchars($product['nome']) ?>"
                                        data-product-price="<?= $product['preco'] ?>"
                                        <?= $isOutOfStock ? 'disabled' : '' ?>>
                                    <?= $isOutOfStock ? 'Esgotado' : 'Adicionar ao Carrinho' ?>
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div style="text-align: center; margin-top: 40px;">
                    <a href="produtos.php" class="cta-button">Ver Todos os Produtos</a>
                </div>
                
            <?php elseif ($error_message): ?>
                <div class="no-products">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Erro</h3>
                    <p><?= htmlspecialchars($error_message) ?></p>
                </div>
            <?php else: ?>
                <div class="no-products">
                    <i class="fas fa-box-open"></i>
                    <h3>Nenhum produto disponível no momento</h3>
                    <p>Estamos preparando novidades incríveis para você!</p>
                    <?php if (isset($_SESSION['admin_logged_in'])): ?>
                        <a href="admin_painel.php" class="admin-link">Cadastrar Produtos</a>
                    <?php else: ?>
                        <p>Volte em breve para conhecer nossos produtos.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Banner Promocional -->
    <section class="promo-banner">
        <div class="container promo-content">
            <h2 class="promo-title">Frete Grátis para Todo Brasil</h2>
            <p class="promo-subtitle">Em compras acima de R$ 150,00</p>
            <a href="#produtos" class="promo-button">Aproveitar Oferta</a>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter">
        <div class="container newsletter-content">
            <h3 class="newsletter-title">Receba Ofertas Exclusivas</h3>
            <p class="newsletter-subtitle">Cadastre-se e seja o primeiro a saber sobre novidades e promoções</p>
            <form class="newsletter-form">
                <input type="email" class="newsletter-input" placeholder="Seu melhor e-mail" required>
                <button type="submit" class="newsletter-button">Cadastrar</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Dev Coffee</h3>
                    <p>Há mais de 10 anos levando os melhores cafés do Brasil para todo o país. Qualidade, tradição e sabor em cada grão.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Institucional</h3>
                    <a href="#">Sobre nós</a>
                    <a href="#">Nossa história</a>
                    <a href="#">Trabalhe conosco</a>
                    <a href="#">Política de privacidade</a>
                </div>
                <div class="footer-column">
                    <h3>Atendimento</h3>
                    <a href="#">Central de ajuda</a>
                    <a href="#">Frete e entrega</a>
                    <a href="#">Trocas e devoluções</a>
                    <a href="#">Formas de pagamento</a>
                </div>
                <div class="footer-column">
                    <h3>Contato</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Rua do Café, 123 - São Paulo, SP</p>
                    <p><i class="fas fa-phone"></i> (11) 9999-9999</p>
                    <p><i class="fas fa-envelope"></i> contato@devcoffee.com.br</p>
                    <div class="payment-methods">
                        <i class="fab fa-cc-visa" style="font-size: 24px; margin-right: 10px;"></i>
                        <i class="fab fa-cc-mastercard" style="font-size: 24px; margin-right: 10px;"></i>
                        <i class="fab fa-cc-paypal" style="font-size: 24px; margin-right: 10px;"></i>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Dev Coffee. Todos os direitos reservados.</p>
                <p>CNPJ: 12.345.678/0001-90</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            const cartCount = document.querySelector('.cart-count');
            let cartItems = [];
            let count = 0;

            if (localStorage.getItem('cartItems')) {
                cartItems = JSON.parse(localStorage.getItem('cartItems'));
                count = cartItems.reduce((total, item) => total + item.quantity, 0);
                cartCount.textContent = count;
            }

            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (this.disabled) return;

                    const productId = this.getAttribute('data-product-id');
                    const productName = this.getAttribute('data-product-name');
                    const productPrice = parseFloat(this.getAttribute('data-product-price'));

                    const existingItem = cartItems.find(item => item.id === productId);
                    
                    if (existingItem) {
                        existingItem.quantity += 1;
                    } else {
                        cartItems.push({
                            id: productId,
                            name: productName,
                            price: productPrice,
                            quantity: 1
                        });
                    }

                    count++;
                    cartCount.textContent = count;
                    
                    localStorage.setItem('cartItems', JSON.stringify(cartItems));
                    
                    const originalText = this.textContent;
                    this.textContent = 'Adicionado!';
                    this.style.background = '#28a745';
                    
                    setTimeout(() => {
                        this.textContent = originalText;
                        this.style.background = '';
                    }, 2000);

                    showNotification(`${productName} adicionado ao carrinho!`);
                });
            });

            // Sistema de busca
            const searchBar = document.getElementById('search-bar');
            const productCards = document.querySelectorAll('.product-card');

            searchBar.addEventListener('input', function() {
                const searchTerm = this.value.trim().toLowerCase();

                productCards.forEach(card => {
                    const productName = card.getAttribute('data-name').toLowerCase();
                    const productDescription = card.getAttribute('data-description').toLowerCase();

                    if (searchTerm === '' || productName.includes(searchTerm) || productDescription.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });

            // Função de notificação
            function showNotification(message) {
                const notification = document.createElement('div');
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: var(--secondary);
                    color: white;
                    padding: 15px 20px;
                    border-radius: 4px;
                    z-index: 10000;
                    box-shadow: var(--shadow);
                    animation: slideIn 0.3s ease;
                `;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            // Menu mobile
            const mobileMenu = document.querySelector('.mobile-menu');
            const mainNav = document.querySelector('.main-nav');
            
            mobileMenu.addEventListener('click', function() {
                mainNav.classList.toggle('active');
            });

            document.addEventListener('click', function(event) {
                if (!mobileMenu.contains(event.target) && !mainNav.contains(event.target)) {
                    mainNav.classList.remove('active');
                }
            });
        });

        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>

<?php
if ($conn) $conn->close();
?>