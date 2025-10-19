function gerarCupom() {
    if (confirm('Deseja gerar um novo cupom aleatório?')) {
        // Criar um formulário temporário para enviar via POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../pages/ganhar_cupom.php';
        
        // Adicionar campo CSRF (opcional, mas recomendado)
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = 'csrf_token';
        csrfField.value = '<?php echo $_SESSION["csrf_token"] ?? ""; ?>';
        form.appendChild(csrfField);
        
        // Adicionar ao body e submeter
        document.body.appendChild(form);
        form.submit();
    }
}

// Efeitos visuais adicionais
document.addEventListener('DOMContentLoaded', function() {
    const cupomCards = document.querySelectorAll('.cupom-card');
    
    cupomCards.forEach((card, index) => {
        // Animação de entrada
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Feedback visual ao aplicar cupom
    const forms = document.querySelectorAll('.cupom-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('.btn-ver-produtos');
            button.innerHTML = 'Aplicando...';
            button.disabled = true;
        });
    });
    
    // Mostrar mensagens de sessão (se houver)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('mensagem')) {
        const mensagem = urlParams.get('mensagem');
        const tipo = urlParams.get('tipo') || 'info';
        mostrarMensagem(mensagem, tipo);
    }
});

function mostrarMensagem(mensagem, tipo = 'info') {
    const div = document.createElement('div');
    div.className = `mensagem-flutuante mensagem-${tipo}`;
    div.textContent = mensagem;
    div.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    if (tipo === 'success') {
        div.style.background = '#27ae60';
    } else if (tipo === 'error') {
        div.style.background = '#e74c3c';
    } else {
        div.style.background = '#3498db';
    }
    
    document.body.appendChild(div);
    
    setTimeout(() => {
        div.remove();
    }, 5000);
}