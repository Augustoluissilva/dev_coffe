document.addEventListener('DOMContentLoaded', function() {
    const categoryButtons = document.querySelectorAll('.category-btn');
    const productCards = document.querySelectorAll('.product-card');
    const cartModal = document.getElementById('cart-modal');
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalAmount = document.getElementById('cart-total-amount');
    const modalClose = document.querySelector('.modal-close');
    const clearCartBtn = document.querySelector('.clear-cart-button');

    let cart = []; // Array que armazenará os produtos do carrinho

    // ===== Filtro por categoria =====
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const selectedCategory = this.getAttribute('data-category');
            
            productCards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                card.style.display = (selectedCategory === 'todos' || cardCategory === selectedCategory) ? 'flex' : 'none';
            });
        });
    });

    // ===== Adicionar ao carrinho =====
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.disabled) return;

            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const productPrice = parseFloat(this.getAttribute('data-product-price'));

            // Verifica se o produto já está no carrinho
            const existingProduct = cart.find(item => item.id === productId);
            if (existingProduct) {
                existingProduct.quantity += 1;
            } else {
                cart.push({ id: productId, name: productName, price: productPrice, quantity: 1 });
            }

            updateCartModal();

            // Feedback visual
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check"></i> Adicionado!';
            this.style.background = 'linear-gradient(135deg, #28a745, #20c997)';

            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.background = '';
            }, 1500);

            // Abre o modal automaticamente
            cartModal.style.display = 'block';
        });
    });

    // ===== Atualiza o modal do carrinho =====
    function updateCartModal() {
        cartItemsContainer.innerHTML = ''; // Limpa o conteúdo do modal
        let total = 0;

        cart.forEach(item => {
            total += item.price * item.quantity;

            const itemDiv = document.createElement('div');
            itemDiv.classList.add('cart-item');
            itemDiv.innerHTML = `
                <span>${item.name} x${item.quantity}</span>
                <span>R$ ${ (item.price * item.quantity).toFixed(2).replace('.', ',') }</span>
            `;
            cartItemsContainer.appendChild(itemDiv);
        });

        cartTotalAmount.textContent = total.toFixed(2).replace('.', ',');
    }

    // ===== Limpar carrinho =====
    clearCartBtn.addEventListener('click', function() {
        cart = [];
        updateCartModal();
    });

    // ===== Fechar modal =====
    modalClose.addEventListener('click', function() {
        cartModal.style.display = 'none';
    });

    // ===== Fechar modal clicando fora =====
    window.addEventListener('click', function(event) {
        if (event.target === cartModal) {
            cartModal.style.display = 'none';
        }
    });
});
