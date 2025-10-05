<?php
session_start();

// Verificar se usuário está logado
if(!isset($_SESSION['usuario_id'])){
    header("Location: login.php");
    exit();
}

$usuario_nome = $_SESSION['usuario_nome'];
$usuario_tipo = $_SESSION['usuario_tipo'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Dev Coffee</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
</head>
<body>
    <header class="header">
        <section>
            <a href="home.php" class="logo">
                <img src="img/logo_devcoffe.png" alt="logo">
            </a>
            <nav class="navbar">
                <a href="home.php">Home</a>
                <a href="index.php#menu">Menu</a>
                <a href="index.php#about">Sobre</a>
                <a href="index.php#review">Avaliações</a>
                <a href="index.php#address">Endereço</a>
            </nav>
            <div class="icons">
                <span>Olá, <?php echo $usuario_nome; ?></span>
                <a href="perfil.php">Perfil</a>
                <a href="logout.php">Sair</a>
            </div>
        </section>
    </header>

    <section class="profile-section">
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($usuario_nome, 0, 1)); ?>
                </div>
                <h1 class="profile-title">Meu Perfil</h1>
                <p class="profile-subtitle">Gerencie suas informações pessoais</p>
            </div>

            <div class="profile-info">
                <div class="info-item">
                    <span class="info-label">Nome:</span>
                    <span class="info-value"><?php echo $usuario_nome; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tipo de Conta:</span>
                    <span class="info-value"><?php echo $usuario_tipo == 'admin' ? 'Administrador' : 'Cliente'; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Membro desde:</span>
                    <span class="info-value"><?php echo date('d/m/Y'); ?></span>
                </div>
            </div>

            <div class="profile-actions">
                <a href="home.php" class="profile-btn primary">Voltar para Home</a>
                <a href="editar-perfil.php" class="profile-btn secondary">Editar Perfil</a>
            </div>
        </div>
    </section>
</body>
</html>