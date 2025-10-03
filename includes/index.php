<?php
session_start();

$usuario_logado = false;
$usuario_nome = '';
$usuario_tipo = '';

if(isset($_SESSION['usuario_id'])){
    $usuario_logado = true;
    $usuario_nome = $_SESSION['usuario_nome'];
    $usuario_tipo = $_SESSION['usuario_tipo'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Dev Coffe</title>
</head>
<body>
    <header class="header">
        <section>
            <a href="#" class="logo">
                <img src="../img/logo_devcoffe.png" alt="logo">
            </a>
            <nav class="navbar">
                <a href="#home">Home</a>
                <a href="#about">Sobre</a>
                <a href="#menu">Menu</a>
                <a href="#review">Avaliações</a>
                <a href="#address">Endereço</a>
            </nav>
            <div class="icons">
                <?php if($usuario_logado): ?>
                    <span style="color: #fff; font-size: 1.6rem; margin-right: 1rem;">
                        Olá, <?php echo $usuario_nome; ?>
                    </span>
                    <a href="logout.php" style="color: #fff; font-size: 1.6rem; margin-right: 1rem;">Sair</a>
                <?php else: ?>
                    <a href="login.php" style="color: #fff; font-size: 1.6rem; margin-right: 1rem;">Login</a>
                    <a href="cadastro.php" style="color: #fff; font-size: 1.6rem;">Cadastrar</a>
                <?php endif; ?>
                <img width="30" height="30" src="https://img.icons8.com/ios-filled/30/ffffff/search--v2.png" alt="search--v2" />
                <img width="30" height="30" src="https://img.icons8.com/ios-glyphs/30/ffffff/shopping-cart--v1.png" alt="shopping-cart--v1" />
            </div>
        </section>
    </header>

    <div class="home-container">
        <section id="home">
            <div class="content">
                <h3>CODE COM O MELHOR CAFÉ DA REGIÃO</h3>
                <p>Venha experimentar o sabor inigualável do nosso café, preparado com grãos selecionados e torrados na medida certa para proporcionar uma experiência única a cada gole.</p>
                <a href="#" class="btn">Pegue o seu Agora!</a>
            </div>
        </section>
    </div>

    <section class="about" id="about">
        <h2 class="title">Sobre <span>Nós</span></h2>
        <div class="row">
            <div class="container-image">
                <img src="../img/about-img.jpg" alt="sobre-nos">
            </div>
            <div class="content">
                <h3>O Que Faz Nosso Café Especial</h3>
                <p>Nosso café é especial porque utilizamos grãos de alta qualidade, cultivados em regiões
                    renomadas e colhidos no ponto ideal de maturação. Além disso, nossa equipe de baristas é
                    altamente treinada para preparar cada xícara com precisão e cuidado, garantindo um sabor
                    excepcional a cada gole.</p>
                <a href="#" class="btn">Saiba mais</a>
            </div>
        </div>
    </section>

    <section class="menu" id="menu">
        <h2 class="title">Nosso <span>Menu</span></h2>

        <div class="box-container">
            <?php
            $menu_items = array(
                array("menu-1.png", "Café coado"),
                array("menu-2.png", "Café coado"),
                array("menu-3.png", "Café coado"),
                array("menu-4.png", "Café coado"),
                array("menu-5.png", "Café coado"),
                array("menu-6.png", "Café coado")
            );
            
            foreach ($menu_items as $item) {
                echo '
                <div class="box">
                    <img src="img/' . $item[0] . '" alt="' . $item[1] . '">
                    <h3>' . $item[1] . '</h3>
                    <div class="price">R$ 15,99 <span>R$ 20,99</span></div>
                    <a href="#" class="btn">Adicione ao Carrinho</a>
                </div>';
            }
            ?>
        </div>
    </section>

    <section class="review" id="review">
        <h2 class="title">Nossos <span>Clientes</span></h2>
        <div class="box-container">
            <?php
            $reviews = array(
                array("pic-1.png", "Matheus da Silva", "Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed ut esse, quas consectetur quam vero deleniti, animi repudiandae eum error fugiat molestias. Quis, odit placeat perferendis modi officia ut architecto!"),
                array("pic-2.png", "Ana Santos", "Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed ut esse, quas consectetur quam vero deleniti, animi repudiandae eum error fugiat molestias. Quis, odit placeat perferendis modi officia ut architecto!"),
                array("pic-3.png", "Lucas Montanno", "Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed ut esse, quas consectetur quam vero deleniti, animi repudiandae eum error fugiat molestias. Quis, odit placeat perferendis modi officia ut architecto!")
            );
            
            foreach ($reviews as $review) {
                echo '
                <div class="box">
                    <img src="img/quote-img.png" alt="comentario">
                    <p>' . $review[2] . '</p>
                    <img src="img/' . $review[0] . '" alt="foto-cliente">
                    <h3>' . $review[1] . '</h3>
                    <div class="stars">
                        <img width="30" height="30" src="https://img.icons8.com/ios-filled/30/ffffff/star--v1.png" alt="star--v1" />
                        <img width="30" height="30" src="https://img.icons8.com/ios-filled/30/ffffff/star--v1.png" alt="star--v1" />
                        <img width="30" height="30" src="https://img.icons8.com/ios-filled/30/ffffff/star--v1.png" alt="star--v1" />
                        <img width="30" height="30" src="https://img.icons8.com/ios-filled/30/ffffff/star--v1.png" alt="star--v1" />
                        <img width="30" height="30" src="https://img.icons8.com/ios-filled/30/ffffff/star-half-empty.png" alt="star-half-empty"/>    
                    </div>
                </div>';
            }
            ?>
        </div>
    </section>

    <section class="address" id="address">
        <h2 class="title">Nosso <span>Endereço</span></h2>
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3671.6408423096022!2d-45.58253872504075!3d-23.03695584253536!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ccf8ed628b4757%3A0x65641c38ea0424cc!2sEscola%20e%20Faculdade%20SENAI%20Taubat%C3%A9%20F%C3%A9lix%20Guisard!5e0!3m2!1spt-BR!2sbr!4v1759268106086!5m2!1spt-BR!2sbr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </section>

    <section class="footer">
    </section>
</body>
</html>