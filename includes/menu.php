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
    $result_categories = null;
} else {
    // Consulta para obter todas as categorias
    $sql_categories = "SELECT * FROM categorias";
    $result_categories = $conn->query($sql_categories);

    // Consulta para obter todos os produtos disponíveis
    $sql_products = "SELECT p.*, c.nome as categoria_nome, t.nome as tipo_cafe_nome, m.nome as marca_cafe_nome 
                     FROM produtos p 
                     LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                     LEFT JOIN tipos_cafe t ON p.id_tipo_cafe = t.id_tipo
                     LEFT JOIN marcas_cafe m ON p.id_marca_cafe = m.id_marca
                     WHERE p.disponivel = 1 
                     ORDER BY p.id_produto DESC";
    $result_products = $conn->query($sql_products);
}

$usuario_nome = isset($_SESSION['usuario_nome']) ? htmlspecialchars($_SESSION['usuario_nome']) : 'Visitante';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Coffee - Nosso Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/menu.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Hero Banner do Menu -->
    <section class="menu-hero">
        <div class="container">
            <h1>Nosso Cardápio!</h1>
            <p>Explore nossa seleção de cafés, bebidas e delícias</p>
        </div>
    </section>

    <!-- Categorias -->
    <section class="categories-menu">
        <div class="container">
            <ul class="categories-list">
                <li><button class="category-btn active" data-category="todos">Todos</button></li>
                <?php if ($result_categories && $result_categories->num_rows > 0): ?>
                    <?php while ($category = $result_categories->fetch_assoc()): ?>
                        <li>
                            <button class="category-btn" data-category="<?= htmlspecialchars($category['nome']) ?>">
                                <?= htmlspecialchars($category['nome']) ?>
                            </button>
                        </li>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>
        </div>
    </section>

    <!-- Produtos -->
    <section class="products-section" id="produtos">
        <div class="container">
            <?php if ($result_products && $result_products->num_rows > 0): ?>
                <div class="products-grid" id="products-grid">
                    <?php while ($product = $result_products->fetch_assoc()): 
                        $isOutOfStock = $product['estoque'] <= 0;
                    ?>
                        <div class="product-card" 
                             data-name="<?= htmlspecialchars($product['nome']) ?>" 
                             data-description="<?= htmlspecialchars($product['descricao'] ?? '') ?>" 
                             data-category="<?= htmlspecialchars($product['categoria_nome'] ?? 'Geral') ?>">
                            
                            <?php if ($isOutOfStock): ?>
                                <div class="out-of-stock-badge">Esgotado</div>
                            <?php endif; ?>
                            
                            <div class="product-image">
                                <?php if (!empty($product['imagem']) && file_exists('../' . $product['imagem'])): ?>
                                    <img src="<?= htmlspecialchars('../' . $product['imagem']) ?>" 
                                         alt="<?= htmlspecialchars($product['nome']) ?>">
                                <?php else: ?>
                                    <div class="no-image-placeholder">
                                        <i class="fas fa-coffee"></i>
                                        <small>Sem imagem</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-info">
                                <div class="product-category"><?= htmlspecialchars($product['categoria_nome'] ?? 'Geral') ?></div>
                                <h3 class="product-title"><?= htmlspecialchars($product['nome']) ?></h3>
                                
                                <?php if (!empty($product['descricao'])): ?>
                                    <p class="product-description">
                                        <?= htmlspecialchars($product['descricao']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="product-price">
                                    R$ <?= number_format($product['preco'], 2, ',', '.') ?>
                                </div>
                                
                                <button class="add-to-cart" 
                                        data-product-id="<?= $product['id_produto'] ?>"
                                        data-product-name="<?= htmlspecialchars($product['nome']) ?>"
                                        data-product-price="<?= $product['preco'] ?>"
                                        <?= $isOutOfStock ? 'disabled' : '' ?>>
                                    <i class="fas fa-shopping-cart"></i>
                                    <?= $isOutOfStock ? 'Esgotado' : 'Adicionar ao Carrinho' ?>
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-products">
                    <i class="fas fa-box-open"></i>
                    <h3>Nenhum produto disponível no momento</h3>
                    <p>Estamos preparando novidades incríveis para você!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="../js/menu.js"></script>
</body>
</html>
