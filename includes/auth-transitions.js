// auth-transitions.js - Controle das transições entre login e cadastro
class AuthTransitions {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.detectInitialState();
    }

    setupEventListeners() {
        // Links de transição
        const registerLinks = document.querySelectorAll('a[href*="cadastro"], a[href*="register"]');
        const loginLinks = document.querySelectorAll('a[href*="login"]');

        registerLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                if (this.shouldAnimate(link)) {
                    e.preventDefault();
                    this.slideToRegister();
                    setTimeout(() => {
                        window.location.href = link.href;
                    }, 600);
                }
            });
        });

        loginLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                if (this.shouldAnimate(link)) {
                    e.preventDefault();
                    this.slideToLogin();
                    setTimeout(() => {
                        window.location.href = link.href;
                    }, 600);
                }
            });
        });

        // Prevenir animação em navegadores antigos
        this.checkAnimationSupport();
    }

    shouldAnimate(link) {
        // Não animar se for o mesmo página ou se não suportar animações
        if (!this.supportsAnimations) return false;
        
        const currentPage = window.location.pathname;
        const targetPage = new URL(link.href).pathname;
        
        return currentPage !== targetPage;
    }

    slideToRegister() {
        const container = document.querySelector('.auth-container');
        if (container) {
            container.classList.remove('slide-left');
            container.classList.add('slide-right');
        }
    }

    slideToLogin() {
        const container = document.querySelector('.auth-container');
        if (container) {
            container.classList.remove('slide-right');
            container.classList.add('slide-left');
        }
    }

    detectInitialState() {
        // Adiciona classe baseada na página atual
        const isRegisterPage = window.location.pathname.includes('cadastro') || 
                              window.location.pathname.includes('register');
        
        if (isRegisterPage) {
            document.body.classList.add('register-page');
        } else {
            document.body.classList.add('login-page');
        }
    }

    checkAnimationSupport() {
        this.supportsAnimations = 
            'transition' in document.documentElement.style &&
            'transform' in document.documentElement.style;
        
        if (!this.supportsAnimations) {
            document.body.classList.add('no-animations');
        }
    }
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    new AuthTransitions();
});

// Fallback para navegadores antigos
if (!Element.prototype.matches) {
    Element.prototype.matches = 
        Element.prototype.msMatchesSelector || 
        Element.prototype.webkitMatchesSelector;
}

if (!NodeList.prototype.forEach) {
    NodeList.prototype.forEach = Array.prototype.forEach;
}