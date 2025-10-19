<?php
// header.php - SEM session_start() aqui, apenas inclui o config
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Coffee - Café todo dia!</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    
    <!-- Header Principal -->
    <header class="main-header">
        <div class="container header-container">
            <div class="logo-container">
                <img src="../img/devcoffee_logo.png" alt="Dev Coffee Logo" class="logo">
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="home.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="produtos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'produtos.php' ? 'active' : ''; ?>">Produtos</a></li>
                    <li><a href="categorias.php">Categorias</a></li>
                    <li><a href="sobre.php">Sobre</a></li>
                    <li><a href="contato.php">Contato</a></li>
                </ul>
            </nav>
            
            <div class="header-actions">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-bar" placeholder="Pesquisar produtos..." id="search-bar">
                </div>
                <a href="carrinho.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </a>
                
                <div class="user-avatar">
                    <?php
                    // Lógica para exibir o avatar do usuário
                    $avatar_path = '';
                    if (isset($_SESSION['usuario_avatar']) && !empty($_SESSION['usuario_avatar'])) {
                        $avatar_path = $_SESSION['usuario_avatar'];
                    }
                    
                    // Se não tem avatar ou é o padrão, usa imagem aleatória
                    if (empty($avatar_path) || $avatar_path === 'default-avatar.jpg') {
                        $avatar_url = 'https://randomuser.me/api/portraits/men/32.jpg';
                    } else {
                        // Verifica se o caminho é absoluto ou relativo
                        if (strpos($avatar_path, 'http') === 0) {
                            $avatar_url = $avatar_path;
                        } else {
                            $avatar_url = '../' . $avatar_path;
                        }
                    }
                    ?>
                    <img src="<?php echo $avatar_url; ?>" 
                         alt="User Avatar" 
                         class="avatar-img" 
                         id="avatar"
                         onerror="this.src='https://randomuser.me/api/portraits/men/32.jpg'">
                    <div class="user-dropdown" id="dropdown">
                        <div class="dropdown-header">
                            <div class="user-name">Olá, <?php echo htmlspecialchars(explode(' ', $usuario_nome)[0]); ?></div>
                        </div>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="pedidos.php" class="dropdown-item">
                                    <span class="dropdown-icon">🧾</span>
                                    <span>Pedidos</span>
                                </a>
                            </li>
                            <li>
                                <a href="cupons.php" class="dropdown-item">
                                    <span class="dropdown-icon">🎟️</span>
                                    <span>Meus Cupons</span>
                                </a>
                            </li>
                            <li>
                                <a href="pagamento.php" class="dropdown-item">
                                    <span class="dropdown-icon">💳</span>
                                    <span>Pagamento</span>
                                </a>
                            </li>
                            <li>
                                <a href="ajuda.php" class="dropdown-item">
                                    <span class="dropdown-icon">❓</span>
                                    <span>Ajuda</span>
                                </a>
                            </li>
                            <li>
                                <a href="meus_dados.php" class="dropdown-item">
                                    <span class="dropdown-icon">⚙️</span>
                                    <span>Meus Dados</span>
                                </a>
                            </li>
                            <li>
                                <a href="logout.php" class="dropdown-item">
                                    <span class="dropdown-icon">🚪</span>
                                    <span>Sair</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="mobile-menu">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle do dropdown do usuário
            const avatar = document.getElementById('avatar');
            const dropdown = document.getElementById('dropdown');

            if (avatar && dropdown) {
                avatar.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('active');
                });

                // Fechar dropdown ao clicar fora
                document.addEventListener('click', function() {
                    dropdown.classList.remove('active');
                });

                // Prevenir que o clique no dropdown feche-o
                dropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // Menu mobile
            const mobileMenu = document.querySelector('.mobile-menu');
            const mainNav = document.querySelector('.main-nav');
            
            if (mobileMenu && mainNav) {
                mobileMenu.addEventListener('click', function() {
                    mainNav.classList.toggle('active');
                });

                document.addEventListener('click', function(event) {
                    if (!mobileMenu.contains(event.target) && !mainNav.contains(event.target)) {
                        mainNav.classList.remove('active');
                    }
                });
            }

            // Atualizar contador do carrinho
            function updateCartCount() {
                const cartCount = document.querySelector('.cart-count');
                let cartItems = [];
                
                if (localStorage.getItem('cartItems')) {
                    cartItems = JSON.parse(localStorage.getItem('cartItems'));
                    const count = cartItems.reduce((total, item) => total + item.quantity, 0);
                    if (cartCount) {
                        cartCount.textContent = count;
                    }
                }
            }

            updateCartCount();

            // Sistema de busca (se necessário)
            const searchBar = document.getElementById('search-bar');
            if (searchBar) {
                searchBar.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.05)';
                });

                searchBar.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            }
        });

        // Função para atualizar o avatar dinamicamente (pode ser chamada após upload)
        function atualizarAvatar(novoAvatarUrl) {
            const avatarImg = document.getElementById('avatar');
            if (avatarImg && novoAvatarUrl) {
                avatarImg.src = novoAvatarUrl;
            }
        }
    </script>
</body>
</html>