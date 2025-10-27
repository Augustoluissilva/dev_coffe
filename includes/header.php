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
    <style>
        /* Estilos do modal (integrados diretamente no header.php para simplificação) */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            width: 90%;
            max-width: 600px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: slideInModal 0.3s ease;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #6b4e31;
            color: white;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .modal-close {
            font-size: 1.5rem;
            cursor: pointer;
        }

        .modal-body {
            padding: 20px;
            max-height: 400px;
            overflow-y: auto;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .cart-item-price {
            color: #555;
        }

        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cart-item-quantity button {
            background: #ddd;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        .cart-item-quantity button:hover {
            background: #ccc;
        }

        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-total {
            font-size: 1.2rem;
        }

        .modal-button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        .checkout-button {
            background-color: #6b4e31;
            color: white;
        }

        .checkout-button:hover {
            background-color: #5a3f28;
        }

        .clear-cart-button {
            background-color: #dc3545;
            color: white;
        }

        .clear-cart-button:hover {
            background-color: #c82333;
        }

        /* Animação para o modal */
        @keyframes slideInModal {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
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
                    <li><a href="menu.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>">Menu</a></li>
                    <li><a href="sobre.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'sobre.php' ? 'active' : ''; ?>">Sobre</a></li>
                    <li><a href="contato.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'contato.php' ? 'active' : ''; ?>">Contato</a></li>
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

        <!-- Cart Modal (integrado dentro do header) -->
        <div id="cart-modal" class="modal" role="dialog" aria-labelledby="cart-modal-title">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="cart-modal-title">Seu Carrinho</h2>
                    <span class="modal-close" aria-label="Fechar carrinho">&times;</span>
                </div>
                <div class="modal-body" id="cart-items">
                    <!-- Cart items will be dynamically inserted here -->
                </div>
                <div class="modal-footer">
                    <div class="cart-total">
                        <strong>Total: R$ <span id="cart-total-amount">0,00</span></strong>
                    </div>
                    <button class="modal-button checkout-button">Finalizar Compra</button>
                    <button class="modal-button clear-cart-button">Limpar Carrinho</button>
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

            // Função para atualizar o avatar dinamicamente (pode ser chamada após upload)
            function atualizarAvatar(novoAvatarUrl) {
                const avatarImg = document.getElementById('avatar');
                if (avatarImg && novoAvatarUrl) {
                    avatarImg.src = novoAvatarUrl;
                }
            }

            // Lógica do carrinho modal
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            const cartCount = document.querySelector('.cart-count');
            const cartModal = document.getElementById('cart-modal');
            const cartItemsContainer = document.getElementById('cart-items');
            const cartTotalAmount = document.getElementById('cart-total-amount');
            const modalClose = document.querySelector('.modal-close');
            const clearCartButton = document.querySelector('.clear-cart-button');
            const checkoutButton = document.querySelector('.checkout-button');
            let cartItems = [];
            let count = 0;

            // Carregar carrinho do localStorage
            if (localStorage.getItem('cartItems')) {
                cartItems = JSON.parse(localStorage.getItem('cartItems'));
                count = cartItems.reduce((total, item) => total + item.quantity, 0);
                cartCount.textContent = count;
                updateCartModal();
            }

            // Abrir modal do carrinho ao clicar no ícone
            const cartIcon = document.querySelector('.cart-icon');
            if (cartIcon) {
                cartIcon.addEventListener('click', function(e) {
                    e.preventDefault();
                    cartModal.style.display = 'flex';
                    updateCartModal();
                });
            } else {
                console.warn('Cart icon not found. Ensure header.php includes an element with class="cart-icon".');
            }

            // Fechar modal
            modalClose.addEventListener('click', function() {
                cartModal.style.display = 'none';
            });

            // Fechar modal ao clicar fora
            cartModal.addEventListener('click', function(e) {
                if (e.target === cartModal) {
                    cartModal.style.display = 'none';
                }
            });

            // Adicionar ao carrinho
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
                    updateCartModal();
                    
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

            // Limpar carrinho
            clearCartButton.addEventListener('click', function() {
                cartItems = [];
                count = 0;
                cartCount.textContent = count;
                localStorage.setItem('cartItems', JSON.stringify(cartItems));
                updateCartModal();
                showNotification('Carrinho limpo!');
            });

            // Botão de checkout
            checkoutButton.addEventListener('click', function() {
                if (cartItems.length === 0) {
                    showNotification('Seu carrinho está vazio!');
                    return;
                }
                // Redirecionar para a página de checkout
                window.location.href = 'checkout.php';
            });

            // Atualizar conteúdo do modal do carrinho
            function updateCartModal() {
                cartItemsContainer.innerHTML = '';
                let total = 0;

                if (cartItems.length === 0) {
                    cartItemsContainer.innerHTML = '<p>Seu carrinho está vazio.</p>';
                } else {
                    cartItems.forEach((item, index) => {
                        const itemTotal = item.price * item.quantity;
                        total += itemTotal;

                        const cartItem = document.createElement('div');
                        cartItem.classList.add('cart-item');
                        cartItem.innerHTML = `
                            <div class="cart-item-info">
                                <div class="cart-item-name">${item.name}</div>
                                <div class="cart-item-price">R$ ${item.price.toFixed(2).replace('.', ',')}</div>
                            </div>
                            <div class="cart-item-quantity">
                                <button class="decrease-quantity" data-index="${index}">-</button>
                                <span>${item.quantity}</span>
                                <button class="increase-quantity" data-index="${index}">+</button>
                                <button class="remove-item" data-index="${index}"><i class="fas fa-trash"></i></button>
                            </div>
                        `;
                        cartItemsContainer.appendChild(cartItem);
                    });
                }

                cartTotalAmount.textContent = total.toFixed(2).replace('.', ',');
                
                // Adicionar eventos para botões de quantidade
                document.querySelectorAll('.increase-quantity').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = this.getAttribute('data-index');
                        cartItems[index].quantity += 1;
                        count += 1;
                        cartCount.textContent = count;
                        localStorage.setItem('cartItems', JSON.stringify(cartItems));
                        updateCartModal();
                    });
                });

                document.querySelectorAll('.decrease-quantity').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = this.getAttribute('data-index');
                        if (cartItems[index].quantity > 1) {
                            cartItems[index].quantity -= 1;
                            count -= 1;
                        } else {
                            cartItems.splice(index, 1);
                            count -= 1;
                        }
                        cartCount.textContent = count;
                        localStorage.setItem('cartItems', JSON.stringify(cartItems));
                        updateCartModal();
                    });
                });

                document.querySelectorAll('.remove-item').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = this.getAttribute('data-index');
                        count -= cartItems[index].quantity;
                        cartItems.splice(index, 1);
                        cartCount.textContent = count;
                        localStorage.setItem('cartItems', JSON.stringify(cartItems));
                        updateCartModal();
                        showNotification('Item removido do carrinho!');
                    });
                });
            }

            // Função de notificação (se não estiver definida em outro lugar)
            function showNotification(message) {
                const notification = document.createElement('div');
                notification.className = 'notification';
                notification.textContent = message;
                notification.style.position = 'fixed';
                notification.style.top = '20px';
                notification.style.right = '20px';
                notification.style.background = '#28a745';
                notification.style.color = 'white';
                notification.style.padding = '10px 20px';
                notification.style.borderRadius = '4px';
                notification.style.zIndex = '2000';
                document.body.appendChild(notification);
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        });
    </script>
</body>
</html>