<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEV COFFEE - Café Premium</title>
    <link rel="stylesheet" href="CSS/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <img src="logo.png" alt="Logo DEV COFFEE" class="logo">
            
            <nav>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#sobre">Sobre</a></li>
                    <li><a href="#menu">Menu</a></li>
                    <li><a href="#avaliacoes">Avaliações</a></li>
                </ul>
            </nav>
            
            <div class="auth-buttons">
                <a href="#">Login</a>
                <a href="#">Cadastro</a>
            </div>
            
            <div class="mobile-menu">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container hero-container">
            <div class="hero-content">
                <h2 class="hero-subtitle">O sabor que desperta suas ideias.</h2>
                <h1 class="hero-title">DEV <span>COFFEE</span></h1>
                <p class="hero-description">
                    Descubra o melhor café especial selecionado das melhores plantações do mundo. Cada xícara é uma experiência única que combina tradição e inovação.
                </p>
                <a href="#menu" class="cta-button">Ver Menu</a>
            </div>
        </div>
    </section>

    <!-- Sobre Section -->
    <section class="sobre" id="sobre">
        <div class="container">
            <div class="section-title">
                <h2>Sobre Nós</h2>
            </div>
            <div class="sobre-content">
                <div class="sobre-text">
                    <h3>Nossa História</h3>
                    <p>Desde 2025, a DEV COFFEE tem se dedicado a oferecer os melhores grãos de café especial, selecionados cuidadosamente das regiões mais prestigiadas do mundo.</p>
                    <p>Trabalhamos diretamente com produtores que compartilham nosso compromisso com a qualidade e sustentabilidade. Cada etapa do processo é supervisionada para garantir que você receba uma experiência excepcional em cada xícara.</p>
                    <a href="#contato" class="btn">Entre em Contato</a>
                </div>
                <div class="sobre-image">
                    <img src="https://images.unsplash.com/photo-1447933601403-0c6688de566e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="Sobre a DEV COFFEE">
                </div>
            </div>
        </div>
    </section>

    <!-- Produtos Section -->
    <section class="produtos" id="produtos">
        <div class="container">
            <div class="section-title">
                <h2>Nossos Produtos</h2>
            </div>
            <div class="produtos-grid">
                <?php
                // Array de produtos - pode vir do banco de dados
                $produtos = [
                    [
                        'imagem' => 'https://images.unsplash.com/photo-1610889556528-9a770e32642f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
                        'titulo' => 'Café Especial',
                        'descricao' => 'Grãos selecionados das melhores regiões produtoras, com notas sensoriais únicas e aroma incomparável.'
                    ],
                    [
                        'imagem' => 'https://images.unsplash.com/photo-1511537190424-bbbab87ac5eb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
                        'titulo' => 'Café Gourmet',
                        'descricao' => 'Para os paladares mais exigentes, nossa linha gourmet oferece uma experiência sensorial única e inesquecível.'
                    ],
                    [
                        'imagem' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
                        'titulo' => 'Café Orgânico',
                        'descricao' => 'Produzido sem agrotóxicos ou produtos químicos, respeitando o meio ambiente e a saúde dos consumidores.'
                    ]
                ];

                // Loop para exibir os produtos
                foreach ($produtos as $produto) {
                    echo '
                    <div class="produto-card">
                        <div class="produto-image">
                            <img src="' . $produto['imagem'] . '" alt="' . $produto['titulo'] . '">
                        </div>
                        <div class="produto-info">
                            <h3>' . $produto['titulo'] . '</h3>
                            <p>' . $produto['descricao'] . '</p>
                            <a href="#contato" class="btn">Saiba Mais</a>
                        </div>
                    </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Contato Section -->
    <section class="contato" id="contato">
        <div class="container">
            <div class="section-title">
                <h2>Contato</h2>
            </div>
            <div class="contato-container">
                <div class="contato-info">
                    <h3>Entre em Contato</h3>
                    <?php
                    // Informações de contato
                    $contato = [
                        'endereco' => 'Rua do Café, 123 - Centro, São Paulo - SP',
                        'telefone' => '(11) 9999-9999',
                        'email' => 'contato@devcoffee.com',
                        'horario' => 'Segunda a Sexta: 8h às 18h'
                    ];
                    ?>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo $contato['endereco']; ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo $contato['telefone']; ?></p>
                    <p><i class="fas fa-envelope"></i> <?php echo $contato['email']; ?></p>
                    <p><i class="fas fa-clock"></i> <?php echo $contato['horario']; ?></p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>DEV COFFEE</h3>
                    <p>Oferecendo os melhores cafés desde 2015. Qualidade, sabor e tradição em cada xícara.</p>
                </div>
                <div class="footer-column">
                    <h3>Links Rápidos</h3>
                    <a href="#home">Home</a>
                    <a href="#sobre">Sobre</a>
                    <a href="#produtos">Produtos</a>
                    <a href="#contato">Contato</a>
                </div>
                <div class="footer-column">
                    <h3>Produtos</h3>
                    <a href="#">Café Especial</a>
                    <a href="#">Café Gourmet</a>
                    <a href="#">Café Orgânico</a>
                    <a href="#">Acessórios</a>
                </div>
                <div class="footer-column">
                    <h3>Contato</h3>
                    <?php
                    // Reutilizando as informações de contato no footer
                    echo '
                    <p>' . $contato['endereco'] . '</p>
                    <p>' . $contato['telefone'] . '</p>
                    <p>' . $contato['email'] . '</p>';
                    ?>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> DEV COFFEE. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenu = document.querySelector('.mobile-menu');
    const nav = document.querySelector('nav ul');
    
    mobileMenu.addEventListener('click', function() {
        nav.style.display = nav.style.display === 'flex' ? 'none' : 'flex';
        
        // Change icon based on menu state
        const icon = this.querySelector('i');
        if (nav.style.display === 'flex') {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
    
    // Smooth scroll para links internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Obrigado pelo seu contato! Em breve retornaremos.');
        this.reset();
    });
    
    // Close mobile menu when clicking on a link
    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                nav.style.display = 'none';
                const icon = mobileMenu.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    });
});
    </script>


</body>
</html>