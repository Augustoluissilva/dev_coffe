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
    
    // Fechar a conexão IMEDIATAMENTE após obter os dados
    $conn->close();
}

if (!isset($_SESSION['usuario_id'])) {
    // header("Location: login.php"); // Descomentado apenas se login for obrigatório
    // exit();
}
$usuario_nome = isset($_SESSION['usuario_nome']) ? htmlspecialchars($_SESSION['usuario_nome']) : 'Visitante';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Coffee - Café todo dia!</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/home.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

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
            
            <?php if (isset($result_products) && $result_products && $result_products->num_rows > 0): ?>
                <div class="products-grid" id="products-grid">
                    <?php while ($product = $result_products->fetch_assoc()): 
                        $isNew = $product['id_produto'] > 0;
                        $isOutOfStock = $product['estoque'] <= 0;
                    ?>
                        <div class="product-card" data-name="<?= htmlspecialchars($product['nome']) ?>" data-description="<?= htmlspecialchars($product['descricao'] ?? '') ?>">
                            <?php if ($isOutOfStock): ?>
                                <div class="product-badge out-of-stock">Esgotado</div>
                            <?php elseif ($isNew): ?>
                          
                            <?php endif; ?>
                            
                            <div class="product-image">
                                <?php if (!empty($product['imagem']) && file_exists('../' . $product['imagem'])): ?>
                                    <img src="<?= htmlspecialchars('../' . $product['imagem']) ?>" 
                                         alt="<?= htmlspecialchars($product['nome']) ?>"
                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhGOEY4Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlbSBuw6NvIGNhcnJlZ2FkYTwvdGV4dD48L3N2Zz4='">
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
                    <a href="../includes/menu.php" class="cta-button">Ver Todos os Produtos</a>
                </div>
                
            <?php elseif (isset($error_message)): ?>
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
    <?php include '../includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sistema de busca
            const searchBar = document.getElementById('search-bar');
            const productCards = document.querySelectorAll('.product-card');

            if (searchBar) {
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
            } else {
                console.warn('Search bar not found. Ensure header.php includes an input with id="search-bar".');
            }

            // Adicionar produtos ao carrinho
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const productName = this.getAttribute('data-product-name');
                    const productPrice = this.getAttribute('data-product-price');
                    
                    // Aqui você pode adicionar a lógica para adicionar ao carrinho
                    console.log('Adicionar ao carrinho:', {
                        id: productId,
                        nome: productName,
                        preco: productPrice
                    });
                    
                    // Exemplo de feedback visual
                    const originalText = this.textContent;
                    this.textContent = 'Adicionado!';
                    this.style.background = '#28a745';
                    this.disabled = true;
                    
                    setTimeout(() => {
                        this.textContent = originalText;
                        this.style.background = '';
                        this.disabled = false;
                    }, 2000);
                });
            });
        });
    </script>
</body>
</html>