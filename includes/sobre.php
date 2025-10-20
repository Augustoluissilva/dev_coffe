<?php include '../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre - Dev Coffee</title>
    <link rel="stylesheet" href="../css/sobre.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- 1️⃣ Seção Principal -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    <span class="hero-subtitle">O sabor que desperta suas ideias.</span>
                    DEV COFFEE
                </h1>
                <p class="hero-description">"Mais que café — uma experiência de sabor e conexão."</p>
                <p class="hero-intro">Na Dev Coffee, acreditamos que cada xícara conta uma história. Nascemos da paixão por tecnologia e café — dois elementos que inspiram criatividade, energia e boas conversas.</p>
                <button class="btn-know-more" onclick="scrollToSection('discover-section')">Saiba mais</button>
            </div>
            <div class="hero-image">
                <img src="../img/login_cadas.jpeg" alt="Grãos de Café Dev Coffee">
            </div>
        </div>
    </section>

    <!-- 2️⃣ Seção Descubra o Melhor Café -->
    <section id="discover-section" class="discover-section">
        <div class="container">
            <div class="discover-content">
                <div class="discover-text">
                    <h2 class="section-title">Descubra o melhor café</h2>
                    <p class="section-description">
                        Somos mais do que uma cafeteria: somos um ponto de encontro entre ideias e pessoas.
                        Cada detalhe do nosso cardápio foi pensado para proporcionar uma experiência única — do espresso artesanal ao donut colorido que acompanha seu café da tarde.
                    </p>
                    <button class="btn-about-small">Sobre</button>
                </div>
                <div class="discover-image">
                    <img src="../img/xicara-removebg-preview.png" alt="Xícara feita de grãos de café">
                </div>
            </div>
        </div>
    </section>

    <!-- 3️⃣ Seção Nossa História -->
    <section class="history-section">
        <div class="container">
            <div class="history-content">
                <div class="history-text">
                    <h2 class="section-title">Nossa História</h2>
                    <p class="history-description">
                        Desde 2020, a Dev Coffee vem transformando o simples ato de tomar café em um momento de inspiração.
                        O projeto nasceu entre amigos apaixonados por programação, que decidiram criar um espaço onde ideias e sabores se encontram.
                    </p>
                </div>
                <div class="history-image">
                    <img src="../img/jovem-tomandocafe.png" alt="Pessoa tomando café">
                </div>
            </div>
        </div>
    </section>

    <!-- 4️⃣ Seção Missão, Visão e Valores -->
    <section class="values-section">
        <div class="container">
            <h2 class="section-title center">Missão, Visão e Valores</h2>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">🎯</div>
                    <h3 class="value-title">Missão</h3>
                    <p class="value-description">Oferecer cafés de alta qualidade e experiências únicas que despertem a criatividade.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">👁️</div>
                    <h3 class="value-title">Visão</h3>
                    <p class="value-description">Ser referência em cafeterias modernas que unem tecnologia e sustentabilidade.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">❤️</div>
                    <h3 class="value-title">Valores</h3>
                    <p class="value-description">Paixão, inovação, respeito e conexão humana.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 5️⃣ Seção Ambiente e Experiência -->
    <section class="environment-section">
        <div class="container">
            <div class="environment-content">
                <h2 class="section-title center">Ambiente e Experiência</h2>
                <p class="environment-description">
                    Espaços aconchegantes, Wi-Fi rápido e playlists relaxantes.
                    A Dev Coffee é o lugar ideal para quem busca produtividade, inspiração e sabor — tudo no mesmo ambiente.
                </p>
                <button class="btn-see-more" onclick="scrollToSection('discover-section')">Veja mais!</button>
                
                <div class="environment-grid">
                    <div class="environment-item">
                        <img src="images/xicara-ambiente.png" alt="Xícara de café">
                    </div>
                    <div class="environment-item">
                        <img src="images/croissant-ambiente.png" alt="Croissant">
                    </div>
                    <div class="environment-item">
                        <img src="images/cafe-laptop.png" alt="Café com laptop">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6️⃣ Rodapé -->
    <?php include '../includes/footer.php'; ?>s

    <script src="../js/sobre.js"></script>
</body>
</html>