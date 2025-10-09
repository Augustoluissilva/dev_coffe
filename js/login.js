document.addEventListener('DOMContentLoaded', function() {
    // Fallback para imagem de background
    const authRight = document.querySelector('.auth-right');
    const testImage = new Image();
    testImage.src = '../img/login_cadas.jpeg';
    
    testImage.onerror = function() {
        authRight.classList.add('no-bg-image');
        console.log('Imagem de background não encontrada. Usando fallback CSS.');
    };

    // Adiciona loading state no botão de login
    const loginForm = document.querySelector('.auth-form');
    const loginBtn = document.querySelector('.auth-btn-primary');
    
    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function(e) {
            // Validação básica do cliente
            const email = loginForm.querySelector('input[name="email"]');
            const senha = loginForm.querySelector('input[name="senha"]');
            
            if (!email.value || !senha.value) {
                e.preventDefault();
                return;
            }
            
            loginBtn.classList.add('loading');
            loginBtn.disabled = true;
            loginBtn.innerHTML = 'ENTRANDO...';
            
            // Timeout para evitar loading infinito
            setTimeout(function() {
                loginBtn.classList.remove('loading');
                loginBtn.disabled = false;
                loginBtn.innerHTML = 'ENTRAR';
            }, 10000); // 10 segundos timeout
        });
    }

    // Mostrar links adicionais em mobile
    function checkMobile() {
        const mobileLinks = document.querySelector('.mobile-links');
        if (window.innerWidth <= 768) {
            mobileLinks.style.display = 'block';
        } else {
            mobileLinks.style.display = 'none';
        }
    }

    // Verificar na carga inicial e no redimensionamento
    checkMobile();
    window.addEventListener('resize', checkMobile);

    // Efeitos de interação nos inputs
    const formInputs = document.querySelectorAll('.form-input');
    formInputs.forEach(input => {
        // Adiciona classe quando o input tem valor
        input.addEventListener('input', function() {
            if (this.value) {
                this.classList.add('has-value');
            } else {
                this.classList.remove('has-value');
            }
        });

        // Verifica estado inicial
        if (input.value) {
            input.classList.add('has-value');
        }

        // Efeito de foco melhorado
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });

    // Prevenir múltiplos cliques nos botões sociais
    const socialButtons = document.querySelectorAll('.social-btn');
    socialButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Feedback visual
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);

            // Aqui você pode adicionar a lógica de login social
            const socialType = this.classList.contains('facebook') ? 'Facebook' : 'Google';
            console.log(`Login com ${socialType} clicado - Integre com sua API aqui`);
        });
    });

    // Melhorar acessibilidade do teclado
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const focused = document.activeElement;
            if (focused.classList.contains('form-input') || 
                focused.classList.contains('social-btn') ||
                focused.classList.contains('auth-btn-primary') ||
                focused.classList.contains('auth-btn-outline')) {
                focused.blur();
            }
        }
    });

    // Suporte para preferência de redução de movimento
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.documentElement.style.setProperty('--animation-duration', '0.01ms');
    }
});

// Polyfill para navegadores antigos
if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = Array.prototype.forEach;
}