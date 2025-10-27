<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cafeteria";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    $error_message = "Desculpe, estamos enfrentando problemas técnicos. Tente novamente mais tarde.";
}

$usuario_nome = isset($_SESSION['usuario_nome']) ? htmlspecialchars($_SESSION['usuario_nome']) : 'Visitante';

// Verificar se está editando um endereço
$endereco_editando = null;
$modo_edicao = false;

if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $endereco_id = $_GET['editar'];
    $modo_edicao = true;
    
    // Buscar dados do endereço
    $sql = "SELECT * FROM enderecos WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $endereco_id, $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $endereco_editando = $result->fetch_assoc();
    } else {
        $error_message = "Endereço não encontrado ou você não tem permissão para editá-lo.";
        $modo_edicao = false;
    }
    $stmt->close();
}

// Verificar mensagens de sucesso/erro
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Limpar as mensagens da sessão
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Coffee - Endereço</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2C1810;
            --secondary: #8B4513;
            --accent: #D2691E;
            --light: #F8F8F8;
            --white: #FFFFFF;
            --text: #333333;
            --gray: #666666;
            --border: #E5E5E5;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            --gradient-primary: linear-gradient(135deg, var(--primary), var(--secondary));
            --gradient-accent: linear-gradient(135deg, var(--secondary), var(--accent));
            --gradient-light: linear-gradient(135deg, #fff8e1 0%, #f5e6cc 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text);
            background: var(--light);
            overflow-x: hidden;
        }

        /* Hero Section */
        .address-hero {
            background: var(--gradient-primary);
            color: var(--white);
            padding: 120px 0 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .address-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,245,230,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,245,230,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .address-hero .container {
            position: relative;
            z-index: 2;
        }

        .address-hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            position: relative;
            display: inline-block;
        }

        .address-hero h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--accent);
            border-radius: 2px;
        }

        .address-hero p {
            font-size: 1.2rem;
            opacity: 0.95;
            max-width: 600px;
            margin: 2rem auto 0;
            line-height: 1.8;
        }

        /* Form Section */
        .address-form-section {
            padding: 100px 0;
            background: var(--gradient-light);
            position: relative;
        }

        .form-container {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 50px;
            align-items: start;
        }

        .form-wrapper {
            background: var(--white);
            border-radius: 24px;
            padding: 50px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
            animation: slideUp 0.8s ease-out;
            transition: transform 0.3s ease;
        }

        .form-wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .form-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--gradient-accent);
            background-size: 300% 300%;
            animation: gradientShift 3s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-wrapper h2 {
            color: var(--primary);
            font-size: 1.8rem;
            margin-bottom: 30px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
            transition: all 0.3s ease;
        }

        .form-group:focus-within {
            transform: translateY(-2px);
        }

        .form-group label {
            display: block;
            color: var(--secondary);
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            background: var(--white);
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(210, 105, 30, 0.1);
            transform: translateY(-2px);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-btn {
            background: var(--gradient-accent);
            color: var(--white);
            padding: 18px 40px;
            border: none;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(139, 69, 19, 0.3);
            width: 100%;
            position: relative;
            overflow: hidden;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(139, 69, 19, 0.4);
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        /* Address Info Sidebar */
        .address-info-sidebar {
            background: var(--white);
            border-radius: 24px;
            padding: 50px 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            height: fit-content;
            position: sticky;
            top: 20px;
            transition: transform 0.3s ease;
        }

        .address-info-sidebar:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .address-info-sidebar h3 {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .address-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding: 15px;
            background: rgba(139, 69, 19, 0.05);
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .address-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--gradient-accent);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .address-item:hover {
            transform: translateX(5px);
            background: rgba(139, 69, 19, 0.1);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .address-item:hover::before {
            transform: scaleY(1);
        }

        .address-item i {
            font-size: 1.5rem;
            color: var(--secondary);
            width: 40px;
            text-align: center;
            margin-right: 15px;
            transition: transform 0.3s ease;
        }

        .address-item:hover i {
            transform: scale(1.2);
            color: var(--accent);
        }

        .address-item div {
            flex: 1;
        }

        .address-item div p {
            margin: 0;
            color: var(--primary);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .address-item div small {
            color: var(--gray);
            font-size: 0.85rem;
        }

        /* Floating Elements */
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
            pointer-events: none;
        }

        .coffee-bean {
            position: absolute;
            width: 20px;
            height: 12px;
            background: radial-gradient(ellipse at center, var(--secondary) 0%, var(--primary) 70%);
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            animation: floatCoffee 6s ease-in-out infinite;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .coffee-bean::before {
            content: '';
            position: absolute;
            width: 8px;
            height: 8px;
            background: var(--primary);
            border-radius: 50%;
            top: 2px;
            left: 2px;
        }

        .coffee-bean:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
        .coffee-bean:nth-child(2) { top: 60%; right: 15%; animation-delay: -2s; animation-duration: 8s; }
        .coffee-bean:nth-child(3) { top: 80%; left: 20%; animation-delay: -1s; animation-duration: 7s; }

        @keyframes floatCoffee {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.6; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
        }

        /* Coffee Cup Animation */
        .coffee-cup {
            position: absolute;
            width: 40px;
            height: 30px;
            background: linear-gradient(to bottom, var(--secondary), var(--primary));
            border-radius: 0 0 20px 20px;
            animation: floatCoffee 8s ease-in-out infinite;
            z-index: 2;
        }

        .coffee-cup::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 0;
            width: 40px;
            height: 10px;
            background: var(--secondary);
            border-radius: 10px 10px 0 0;
        }

        .coffee-cup::after {
            content: '';
            position: absolute;
            top: -5px;
            right: -8px;
            width: 8px;
            height: 15px;
            background: var(--secondary);
            border-radius: 0 5px 5px 0;
        }

        .coffee-cup:nth-child(4) { top: 30%; right: 10%; animation-delay: -1s; }
        .coffee-cup:nth-child(5) { top: 70%; left: 15%; animation-delay: -3s; }

        /* Success Message */
        .success-message {
            background: linear-gradient(135deg, #4caf50, #66bb6a);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: none;
            animation: fadeIn 0.5s ease;
            text-align: center;
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }

        .error-message {
            background: linear-gradient(135deg, #f44336, #e57373);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            animation: fadeIn 0.5s ease;
            text-align: center;
            box-shadow: 0 5px 15px rgba(244, 67, 54, 0.3);
        }

        /* CEP Styles */
        .cep-loading {
            display: none;
            color: var(--secondary);
            font-size: 14px;
            margin-top: 5px;
            font-weight: 500;
        }

        .cep-error {
            color: #ff4444;
            font-size: 14px;
            margin-top: 5px;
            font-weight: 500;
        }

        .address-fields {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .address-fields.show {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Estilos para modo edição */
        .edit-badge {
            background: linear-gradient(135deg, #ff9800, #f57c00);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .btn-voltar {
            background: #6c757d;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
        }
        
        .btn-voltar:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        /* Estilos para o botão excluir */
        .btn-excluir {
            background: linear-gradient(135deg, #f44336, #e53935);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            margin-left: 10px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3);
        }

        .btn-excluir:hover {
            background: linear-gradient(135deg, #e53935, #c62828);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(244, 67, 54, 0.4);
        }

        .btn-excluir:active {
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .address-hero h1 { font-size: 2.5rem; }
            .form-container {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .form-wrapper { padding: 40px 30px; }
            .address-info-sidebar { order: -1; }
            .btn-excluir {
                margin-left: 0;
                margin-top: 10px;
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .form-wrapper, .address-info-sidebar { padding: 30px 20px; }
            .address-hero { padding: 100px 0 60px; }
            .address-form-section { padding: 60px 0; }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Hero Banner -->
    <section class="address-hero">
        <div class="floating-elements">
            <div class="coffee-bean"></div>
            <div class="coffee-bean"></div>
            <div class="coffee-bean"></div>
            <div class="coffee-cup"></div>
            <div class="coffee-cup"></div>
        </div>
        <div class="container">
            <h1><i class="fas fa-map-marker-alt"></i> 
                <?php echo $modo_edicao ? 'Editar Endereço' : 'Endereço'; ?>
            </h1>
            <p>
                <?php echo $modo_edicao 
                    ? 'Atualize os dados do seu endereço de entrega.' 
                    : 'Insira os dados do seu endereço de entrega e receba nosso café com todo cuidado!'; 
                ?>
            </p>
        </div>
    </section>

    <!-- Form Section -->
    <section class="address-form-section">
        <div class="container">
            <div class="form-container">
                <!-- Formulário -->
                <div class="form-wrapper">
                    <h2>
                        <i class="fas fa-edit"></i> 
                        <?php echo $modo_edicao ? 'Editar Endereço' : 'Cadastrar Endereço'; ?>
                    </h2>
                    
                    <?php if ($modo_edicao): ?>
                        <div class="edit-badge">
                            <i class="fas fa-edit"></i> Modo Edição
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success_message): ?>
                        <div class="success-message" id="successMessage">
                            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="error-message" id="errorMessage">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form id="addressForm" action="salvar_endereco.php" method="POST">
                        <?php if ($modo_edicao): ?>
                            <input type="hidden" name="endereco_id" value="<?php echo $endereco_editando['id']; ?>">
                            <input type="hidden" name="modo_edicao" value="1">
                        <?php endif; ?>
                        
                        <!-- Campo CEP -->
                        <div class="form-group">
                            <label for="cep"><i class="fas fa-map-pin"></i> CEP</label>
                            <input type="text" id="cep" name="cep" 
                                   placeholder="Digite o CEP (apenas números)" 
                                   maxlength="9" onkeyup="formatarCEP(this)" 
                                   value="<?php echo $modo_edicao ? htmlspecialchars($endereco_editando['cep']) : ''; ?>" 
                                   required>
                            <div class="cep-loading" id="cepLoading">
                                <i class="fas fa-spinner fa-spin"></i> Buscando endereço...
                            </div>
                            <div class="cep-error" id="cepError"></div>
                        </div>

                        <!-- Campos que serão preenchidos automaticamente -->
                        <div class="address-fields <?php echo $modo_edicao ? 'show' : ''; ?>" id="addressFields">
                            <div class="form-group">
                                <label for="endereco"><i class="fas fa-road"></i> Endereço</label>
                                <input type="text" id="endereco" name="endereco" 
                                       placeholder="Rua, Avenida, etc." 
                                       value="<?php echo $modo_edicao ? htmlspecialchars($endereco_editando['endereco']) : ''; ?>" 
                                       required>
                            </div>
                            
                            <div class="form-group">
                                <label for="numero"><i class="fas fa-hashtag"></i> Número</label>
                                <input type="text" id="numero" name="numero" 
                                       placeholder="Número" 
                                       value="<?php echo $modo_edicao ? htmlspecialchars($endereco_editando['numero']) : ''; ?>" 
                                       required>
                            </div>
                            
                            <div class="form-group">
                                <label for="complemento"><i class="fas fa-building"></i> Complemento</label>
                                <input type="text" id="complemento" name="complemento" 
                                       placeholder="Apto, Bloco, etc."
                                       value="<?php echo $modo_edicao ? htmlspecialchars($endereco_editando['complemento']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="bairro"><i class="fas fa-map"></i> Bairro</label>
                                <input type="text" id="bairro" name="bairro" 
                                       placeholder="Bairro" 
                                       value="<?php echo $modo_edicao ? htmlspecialchars($endereco_editando['bairro']) : ''; ?>" 
                                       required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cidade"><i class="fas fa-city"></i> Cidade</label>
                                <input type="text" id="cidade" name="cidade" 
                                       placeholder="Cidade" 
                                       value="<?php echo $modo_edicao ? htmlspecialchars($endereco_editando['cidade']) : ''; ?>" 
                                       required>
                            </div>
                            
                            <div class="form-group">
                                <label for="estado"><i class="fas fa-flag"></i> Estado</label>
                                <input type="text" id="estado" name="estado" 
                                       placeholder="UF" maxlength="2" 
                                       value="<?php echo $modo_edicao ? htmlspecialchars($endereco_editando['estado']) : ''; ?>" 
                                       required>
                            </div>
                        </div>

                        <!-- Campos adicionais -->
                        <div class="form-group">
                            <label for="referencia"><i class="fas fa-map-marker-alt"></i> Ponto de Referência</label>
                            <input type="text" id="referencia" name="referencia" 
                                   placeholder="Próximo a..."
                                   value="<?php echo $modo_edicao ? htmlspecialchars($endereco_editando['referencia']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo_endereco"><i class="fas fa-home"></i> Tipo de Endereço</label>
                            <select id="tipo_endereco" name="tipo_endereco" required>
                                <option value="">Selecione...</option>
                                <option value="casa" <?php echo ($modo_edicao && $endereco_editando['tipo_endereco'] == 'casa') ? 'selected' : ''; ?>>Casa</option>
                                <option value="apartamento" <?php echo ($modo_edicao && $endereco_editando['tipo_endereco'] == 'apartamento') ? 'selected' : ''; ?>>Apartamento</option>
                                <option value="trabalho" <?php echo ($modo_edicao && $endereco_editando['tipo_endereco'] == 'trabalho') ? 'selected' : ''; ?>>Trabalho</option>
                                <option value="outro" <?php echo ($modo_edicao && $endereco_editando['tipo_endereco'] == 'outro') ? 'selected' : ''; ?>>Outro</option>
                            </select>
                        </div>

                        <button type="submit" class="submit-btn">
                            <i class="fas fa-save"></i> 
                            <?php echo $modo_edicao ? 'Atualizar Endereço' : 'Salvar Endereço'; ?>
                        </button>
                    </form>
                    
                    <?php if ($modo_edicao): ?>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <a href="meus_dados.php" class="btn-voltar">
                                <i class="fas fa-arrow-left"></i> Voltar para Meus Dados
                            </a>
                            
                            <!-- Botão de Excluir -->
                            <button type="button" class="btn-excluir" onclick="confirmarExclusao(<?php echo $endereco_editando['id']; ?>)">
                                <i class="fas fa-trash"></i> Excluir Endereço
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Informações de Endereço -->
                <div class="address-info-sidebar">
                    <h3><i class="fas fa-info-circle"></i> Por que cadastrar?</h3>
                    
                    <div class="address-item">
                        <i class="fas fa-shipping-fast"></i>
                        <div>
                            <p><strong>Entrega Rápida</strong></p>
                            <small>Receba seu café mais rápido</small>
                        </div>
                    </div>
                    
                    <div class="address-item">
                        <i class="fas fa-map-marked-alt"></i>
                        <div>
                            <p><strong>Localização Precisa</strong></p>
                            <small>Entregamos no local exato</small>
                        </div>
                    </div>
                    
                    <div class="address-item">
                        <i class="fas fa-coffee"></i>
                        <div>
                            <p><strong>Café Fresquinho</strong></p>
                            <small>Seu café chega na temperatura ideal</small>
                        </div>
                    </div>
                    
                    <div class="address-item">
                        <i class="fas fa-user-check"></i>
                        <div>
                            <p><strong>Facilidade</strong></p>
                            <small>Salve para próximos pedidos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Função para formatar CEP
        function formatarCEP(input) {
            let cep = input.value.replace(/\D/g, '');
            
            if (cep.length > 5) {
                cep = cep.substring(0, 5) + '-' + cep.substring(5, 8);
            }
            
            input.value = cep;
            
            // Consulta automática quando o CEP estiver completo
            if (cep.length === 9) {
                consultarCEP(cep);
            } else {
                // Esconde os campos de endereço se o CEP não estiver completo
                document.getElementById('addressFields').classList.remove('show');
            }
        }

        // Função para consultar CEP
        async function consultarCEP(cep) {
            const cepLoading = document.getElementById('cepLoading');
            const cepError = document.getElementById('cepError');
            const addressFields = document.getElementById('addressFields');
            
            // Limpa erro anterior e mostra loading
            cepError.textContent = '';
            cepLoading.style.display = 'block';
            addressFields.classList.remove('show');

            try {
                // Remove o hífen para a consulta
                const cepNumerico = cep.replace('-', '');
                
                const response = await fetch(`https://viacep.com.br/ws/${cepNumerico}/json/`);
                const data = await response.json();

                cepLoading.style.display = 'none';

                if (data.erro) {
                    cepError.textContent = 'CEP não encontrado. Verifique e tente novamente.';
                    return;
                }

                // Preenche os campos automaticamente
                document.getElementById('endereco').value = data.logradouro || '';
                document.getElementById('bairro').value = data.bairro || '';
                document.getElementById('cidade').value = data.localidade || '';
                document.getElementById('estado').value = data.uf || '';

                // Mostra os campos de endereço com animação
                addressFields.classList.add('show');

                // Foca no campo número
                setTimeout(() => {
                    document.getElementById('numero').focus();
                }, 300);

            } catch (error) {
                cepLoading.style.display = 'none';
                cepError.textContent = 'Erro ao consultar CEP. Tente novamente.';
                console.error('Erro na consulta do CEP:', error);
            }
        }

        // Função para confirmar exclusão do endereço
        function confirmarExclusao(enderecoId) {
            if (confirm('Tem certeza que deseja excluir este endereço? Esta ação não pode ser desfeita.')) {
                // Redireciona para a página de exclusão
                window.location.href = 'excluir_endereco.php?id=' + enderecoId;
            }
        }

        // Animações do formulário
        document.querySelectorAll('.form-group input, .form-group textarea, .form-group select').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Criação dinâmica de grãos de café
        function createMoreCoffeeBeans() {
            const floatingContainer = document.querySelector('.floating-elements');
            for(let i = 4; i < 10; i++) {
                const bean = document.createElement('div');
                bean.className = 'coffee-bean';
                bean.style.left = Math.random() * 100 + '%';
                bean.style.top = Math.random() * 100 + '%';
                bean.style.animationDelay = Math.random() * 6 + 's';
                bean.style.animationDuration = (Math.random() * 3 + 5) + 's';
                floatingContainer.appendChild(bean);
            }
        }
        createMoreCoffeeBeans();

        // Validação do formulário
        document.getElementById('addressForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const cep = document.getElementById('cep').value;
            if (cep.length !== 9) {
                alert('Por favor, insira um CEP válido.');
                return;
            }

            // Simula o envio do formulário
            const submitBtn = this.querySelector('.submit-btn');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                // Envia o formulário
                this.submit();
            }, 1000);
        });

        // Efeito de digitação no placeholder do CEP
        const cepInput = document.getElementById('cep');
        const cepMessages = [
            "Digite o CEP...",
            "Exemplo: 12345-678",
            "Apenas números...",
            "Vamos buscar seu endereço!",
            "CEP de 8 dígitos..."
        ];
        
        let cepMessageIndex = 0;
        let cepCharIndex = 0;
        let cepIsDeleting = false;
        
        function typeCepMessage() {
            const currentMessage = cepMessages[cepMessageIndex];
            if (cepIsDeleting) {
                cepInput.placeholder = currentMessage.substring(0, cepCharIndex - 1);
                cepCharIndex--;
            } else {
                cepInput.placeholder = currentMessage.substring(0, cepCharIndex + 1);
                cepCharIndex++;
            }
            
            let typeSpeed = cepIsDeleting ? 50 : 100;
            setTimeout(typeCepMessage, typeSpeed);
            
            if (!cepIsDeleting && cepCharIndex === currentMessage.length) {
                setTimeout(() => { cepIsDeleting = true; }, 2000);
            } else if (cepIsDeleting && cepCharIndex === 0) {
                cepIsDeleting = false;
                cepMessageIndex = (cepMessageIndex + 1) % cepMessages.length;
            }
        }
        
        // Inicia a animação apenas se o campo estiver vazio
        cepInput.addEventListener('focus', function() {
            if (!this.value) {
                typeCepMessage();
            }
        });
        
        cepInput.addEventListener('blur', function() {
            cepInput.placeholder = "Digite o CEP (apenas números)";
        });

        // Auto-esconder mensagens após 5 segundos
        setTimeout(() => {
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            
            if (successMessage) successMessage.style.display = 'none';
            if (errorMessage) errorMessage.style.display = 'none';
        }, 5000);

        // Se estiver no modo edição, mostra os campos automaticamente
        <?php if ($modo_edicao): ?>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('addressFields').classList.add('show');
        });
        <?php endif; ?>
    </script>
</body>
</html>