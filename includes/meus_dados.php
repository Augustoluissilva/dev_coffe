<?php
session_start();

// Verificar se usuário está logado (opcional)
if (!isset($_SESSION['usuario_id'])) {
    // header("Location: login.php");
    // exit();
}

$usuario_nome = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Visitante';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Dados - Dev Coffee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3E2723;
            --accent: #D4A574;
            --white: #FFFFFF;
            --light-gray: #F8F8F8;
            --border-color: #E0E0E0;
            --text-dark: #222222;
            --text-gray: #666666;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--white);
            color: var(--text-dark);
            line-height: 1.6;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Estilos da página Meus Dados */
        .dados-container {
            margin-top: 60px;
            padding: 40px 0;
        }

        .dados-title {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 40px;
            color: var(--text-dark);
        }

        .dados-section {
            background: var(--white);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .dados-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
            cursor: pointer;
        }

        .dados-item:last-child {
            border-bottom: none;
        }

        .dados-item:hover {
            background-color: var(--light-gray);
        }

        .dados-content {
            flex: 1;
        }

        .dados-item-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text-dark);
        }

        .dados-item-description {
            font-size: 14px;
            color: var(--text-gray);
        }

        .dados-arrow {
            color: var(--text-gray);
            font-size: 16px;
            margin-left: 15px;
            transition: var(--transition);
        }

        .dados-item:hover .dados-arrow {
            transform: translateX(3px);
            color: var(--accent);
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .dados-container {
                margin-top: 40px;
                padding: 20px 0;
            }

            .dados-title {
                font-size: 28px;
                margin-bottom: 30px;
            }

            .dados-item {
                padding: 16px;
            }
        }

        @media (max-width: 480px) {
            .dados-title {
                font-size: 24px;
            }

            .dados-item-title {
                font-size: 16px;
            }

            .dados-item-description {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="dados-container">
            <h1 class="dados-title">Meus dados</h1>
            
            <div class="dados-section">
                <div class="dados-item" onclick="window.location.href='informacoes_pessoais.php'">
                    <div class="dados-content">
                        <div class="dados-item-title">Informações pessoais</div>
                        <div class="dados-item-description">Nome completo e CPF</div>
                    </div>
                    <div class="dados-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
                
                <div class="dados-item" onclick="window.location.href='dados_contato.php'">
                    <div class="dados-content">
                        <div class="dados-item-title">Dados de contato</div>
                        <div class="dados-item-description">E-mail e telefone de contato</div>
                    </div>
                    <div class="dados-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
                
                <div class="dados-item" onclick="window.location.href='credenciais.php'">
                    <div class="dados-content">
                        <div class="dados-item-title">Credenciais</div>
                        <div class="dados-item-description">Meios de acesso à minha conta</div>
                    </div>
                    <div class="dados-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
                
                <div class="dados-item" onclick="window.location.href='enderecos.php'">
                    <div class="dados-content">
                        <div class="dados-item-title">Endereços</div>
                        <div class="dados-item-description">Gerenciar meus endereços</div>
                    </div>
                    <div class="dados-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Adicionar efeitos de interação aos itens
            const dadosItems = document.querySelectorAll('.dados-item');
            
            dadosItems.forEach(item => {
                // Efeito de clique
                item.addEventListener('click', function() {
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
                
                // Adicionar acessibilidade - permitir navegação por teclado
                item.setAttribute('tabindex', '0');
                item.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });
        });
    </script>
</body>
</html>