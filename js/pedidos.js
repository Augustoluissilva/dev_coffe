document.addEventListener('DOMContentLoaded', function() {
    // Simulação de atualização de status (apenas visual)
    simulateStatusUpdates();
    
    // Adicionar eventos aos botões "Comprar novamente"
    const buyAgainButtons = document.querySelectorAll('.btn-buy-again');
    buyAgainButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productName = this.closest('.order-row').querySelector('.product-name').textContent;
            showNotification(`Produto "${productName}" adicionado ao carrinho!`);
        });
    });
});

function simulateStatusUpdates() {
    // Esta função simula mudanças de status apenas para demonstração
    // Em um sistema real, isso viria do backend
    
    const statusElements = {
        preparing: document.getElementById('status-preparing'),
        delivery: document.getElementById('status-delivery')
    };

    // Simular mudança de status após 10 segundos
    setTimeout(() => {
        if (statusElements.preparing) {
            const statusText = statusElements.preparing.querySelector('.status-text');
            statusText.textContent = 'Pronto para entrega';
            
            // Atualizar classes para novo status
            statusElements.preparing.className = 'status delivery';
            statusElements.preparing.id = 'status-delivery-new';
            
            // Reiniciar animação
            const statusIcon = statusElements.preparing.querySelector('.status-icon');
            statusIcon.style.animation = 'move 3s infinite';
        }
    }, 10000);

    // Simular entrega após 20 segundos
    setTimeout(() => {
        if (statusElements.delivery) {
            const statusText = statusElements.delivery.querySelector('.status-text');
            statusText.textContent = 'Entregue';
            
            // Atualizar classes para status entregue
            statusElements.delivery.className = 'status delivered';
            
            // Remover animação
            const statusIcon = statusElements.delivery.querySelector('.status-icon');
            statusIcon.style.animation = 'none';
            
            // Adicionar botão "Comprar novamente"
            const statusCol = statusElements.delivery.parentElement;
            const buyAgainBtn = document.createElement('button');
            buyAgainBtn.className = 'btn-buy-again';
            buyAgainBtn.textContent = 'Comprar novamente';
            buyAgainBtn.addEventListener('click', function() {
                const productName = this.closest('.order-row').querySelector('.product-name').textContent;
                showNotification(`Produto "${productName}" adicionado ao carrinho!`);
            });
            statusCol.appendChild(buyAgainBtn);
        }
    }, 20000);
}

function showNotification(message) {
    // Criar elemento de notificação
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #44D26A;
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        font-weight: 500;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animação de entrada
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remover após 3 segundos
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}