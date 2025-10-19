<?php
session_start();
include_once '../config/database.php';

// Criar instância do Database e obter conexão
$database = new Database();
$conn = $database->getConnection();

// Buscar cupons
$cupons = [];
$error = null;

if ($conn) {
    try {
        // Query corrigida - ordenar por id_cupom
        $query = "SELECT * FROM cupons WHERE ativo = 1 AND (validade IS NULL OR validade >= CURDATE()) ORDER BY id_cupom DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $cupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // DEBUG: Verificar quantos cupons foram encontrados
        error_log("Cupons encontrados: " . count($cupons));
        
    } catch (PDOException $e) {
        $error = 'Erro ao carregar cupons: ' . $e->getMessage();
        error_log("Erro na query: " . $e->getMessage());
    }
} else {
    $error = 'Erro de conexão com o banco de dados.';
    error_log("Conexão com banco falhou");
}

// Verificar se há mensagem de sucesso
$mensagem = null;
if (isset($_SESSION['mensagem_sucesso'])) {
    $mensagem = $_SESSION['mensagem_sucesso'];
    unset($_SESSION['mensagem_sucesso']);
}

// DEBUG: Verificar dados
error_log("Total de cupons para exibir: " . count($cupons));
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cupons - Dev Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/cupons.css">
</head>
<body>
    <?php include_once "../includes/header.php"; ?>

    <!-- Conteúdo Principal -->
    <main class="main-content">
        <!-- Banner Superior -->
        <section class="banner-section">
            <div class="container">
                <h1 class="banner-text">Aproveite todos os cupons de desconto disponíveis!</h1>
            </div>
        </section>

        <!-- Seção de Cupons -->
        <section class="cupons-section">
            <div class="container">
                <h2 class="section-title">Cupons</h2>
                
                <!-- Botão para Ganhar Cupom -->
                <div class="ganhar-cupom-section">
                    <form action="ganhar_cupom.php" method="POST" class="ganhar-cupom-form">
                        <button type="submit" class="btn-ganhar-cupom">
                            🎁 Ganhar Cupom Surpresa
                        </button>
                        <p class="ganhar-cupom-desc">Clique aqui para ganhar um cupom de desconto surpresa!</p>
                    </form>
                </div>

                <!-- Mensagem de Sucesso/Erro -->
                <?php if ($mensagem): ?>
                    <div class="mensagem-alerta mensagem-<?php echo $mensagem['tipo']; ?>">
                        <?php echo $mensagem['texto']; ?>
                    </div>
                <?php endif; ?>

                <!-- DEBUG: Mostrar informações -->
                <div style="display: none;"> <!-- Esconder em produção -->
                    <p>DEBUG: <?php echo count($cupons); ?> cupons encontrados</p>
                    <?php foreach ($cupons as $cupom): ?>
                        <p>DEBUG Cupom: <?php echo $cupom['codigo'] . ' - ' . $cupom['descricao']; ?></p>
                    <?php endforeach; ?>
                </div>

                <!-- Grid de Cupons -->
                <div class="cupons-grid">
                    <?php if (!empty($error)): ?>
                        <p class="error-message"><?php echo $error; ?></p>
                    <?php elseif (count($cupons) > 0): ?>
                        <?php foreach ($cupons as $cupom): ?>
                            <div class="cupom-card">
                                <div class="cupom-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="#4E342E">
                                        <path d="M20 6H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-5 3c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zM4 9h3c0 1.1-.9 2-2 2s-2-.9-2-2zm0 4h3c0 1.1-.9 2-2 2s-2-.9-2-2zm0 4h3c0 1.1-.9 2-2 2s-2-.9-2-2zm16 0h-3c0-1.1.9-2 2-2s2 .9 2 2zm0-4h-3c0-1.1.9-2 2-2s2 .9 2 2zm0-4h-3c0-1.1.9-2 2-2s2 .9 2 2z"/>
                                    </svg>
                                </div>
                                
                                <div class="cupom-content">
                                    <h3 class="cupom-title">
                                        <?php 
                                        // Extrair o título da descrição (parte antes do traço)
                                        $descricao = $cupom['descricao'];
                                        $partes = explode('-', $descricao);
                                        echo htmlspecialchars(trim($partes[0]));
                                        ?>
                                    </h3>
                                    
                                    <p class="cupom-description">
                                        <?php 
                                        // Extrair a descrição (parte após o traço)
                                        if (count($partes) > 1) {
                                            echo htmlspecialchars(trim($partes[1]));
                                        } else {
                                            echo htmlspecialchars($descricao);
                                        }
                                        ?>
                                    </p>

                                    <!-- Informações adicionais do cupom -->
                                    <div class="cupom-info">
                                        <span class="cupom-codigo">Código: <strong><?php echo $cupom['codigo']; ?></strong></span>
                                        <?php if (!empty($cupom['validade'])): ?>
                                            <span class="cupom-validade">Válido até: <?php echo date('d/m/Y', strtotime($cupom['validade'])); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <form action="../actions/aplicar_cupom.php" method="POST" class="cupom-form">
                                        <input type="hidden" name="cupom_id" value="<?php echo $cupom['id_cupom']; ?>">
                                        <input type="hidden" name="cupom_codigo" value="<?php echo $cupom['codigo']; ?>">
                                        <input type="hidden" name="desconto_percentual" value="<?php echo $cupom['desconto_percentual']; ?>">
                                        <input type="hidden" name="desconto_fixo" value="<?php echo $cupom['desconto_fixo']; ?>">
                                        <button type="submit" class="btn-ver-produtos">
                                            Ver produtos
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-cupons">
                            <p>Nenhum cupom disponível no momento.</p>
                            <p>Clique em "Ganhar Cupom Surpresa" para obter seu primeiro desconto!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
</body>
</html>