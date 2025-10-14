<?php
session_start();

// Verificar se o usuário está logado como administrador
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cafeteria";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// VERIFICAR E CRIAR CATEGORIAS SE NECESSÁRIO
$check_categories = $conn->query("SELECT COUNT(*) as total FROM categorias");
$categories_count = $check_categories->fetch_assoc()['total'];

if ($categories_count == 0) {
    // Inserir categorias padrão se a tabela estiver vazia
    $default_categories = [
        'Café em Grãos',
        'Café Moído', 
        'Cápsulas',
        'Acessórios',
        'Kits',
        'Cafés Especiais'
    ];
    
    foreach ($default_categories as $category) {
        $insert_category = $conn->prepare("INSERT INTO categorias (nome) VALUES (?)");
        $insert_category->bind_param("s", $category);
        $insert_category->execute();
    }
    
    // Mensagem informativa
    $_SESSION['admin_message'] = "✅ Categorias padrão criadas automaticamente!";
}

// VERIFICAR E CRIAR TIPOS DE CAFÉ SE NECESSÁRIO
$check_tipos = $conn->query("SELECT COUNT(*) as total FROM tipos_cafe");
if ($check_tipos->fetch_assoc()['total'] == 0) {
    $default_tipos = [
        'Arábica',
        'Robusta',
        'Libérica',
        'Excelsa',
        'Blend'
    ];
    
    foreach ($default_tipos as $tipo) {
        $insert_tipo = $conn->prepare("INSERT INTO tipos_cafe (nome) VALUES (?)");
        $insert_tipo->bind_param("s", $tipo);
        $insert_tipo->execute();
    }
}

// VERIFICAR E CRIAR MARCAS SE NECESSÁRIO
$check_marcas = $conn->query("SELECT COUNT(*) as total FROM marcas_cafe");
if ($check_marcas->fetch_assoc()['total'] == 0) {
    $default_marcas = [
        'Dev Coffee',
        'Café do Sítio',
        'Santa Clara',
        'Pilão',
        'Melitta',
        'Três Corações'
    ];
    
    foreach ($default_marcas as $marca) {
        $insert_marca = $conn->prepare("INSERT INTO marcas_cafe (nome) VALUES (?)");
        $insert_marca->bind_param("s", $marca);
        $insert_marca->execute();
    }
}

// Configurações de upload
$upload_dir = "../uploads/produtos/";
$allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$max_size = 5 * 1024 * 1024; // 5MB

// Criar diretório se não existir
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Processar ações POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ação de cadastrar novo produto
    if (isset($_POST['add_product'])) {
        $nome = trim($_POST['nome']);
        $descricao = trim($_POST['descricao']);
        $preco = floatval($_POST['preco']);
        $peso = !empty($_POST['peso']) ? floatval($_POST['peso']) : NULL;
        $id_categoria = (int)$_POST['id_categoria'];
        $id_tipo_cafe = !empty($_POST['id_tipo_cafe']) ? (int)$_POST['id_tipo_cafe'] : NULL;
        $id_marca_cafe = !empty($_POST['id_marca_cafe']) ? (int)$_POST['id_marca_cafe'] : NULL;
        $estoque = (int)$_POST['estoque'];
        
        $imagem_url = '';
        $imagem_nome = '';
        
        // Verificar qual método de imagem foi usado
        $metodo_imagem = $_POST['metodo_imagem'];
        
        if ($metodo_imagem === 'upload') {
            // Processar upload da imagem
            if (isset($_FILES['imagem_upload']) && $_FILES['imagem_upload']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['imagem_upload'];
                $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $file_size = $file['size'];
                
                // Debug: verificar informações do arquivo
                error_log("Upload attempt - File: " . $file['name'] . ", Size: " . $file_size . ", Temp: " . $file['tmp_name']);
                
                // Validar tipo de arquivo
                if (!in_array($file_extension, $allowed_types)) {
                    $_SESSION['admin_message'] = "❌ Erro: Apenas arquivos JPG, JPEG, PNG, GIF e WEBP são permitidos.";
                    header("Location: ".$_SERVER['PHP_SELF']);
                    exit();
                }
                
                // Validar tamanho do arquivo
                if ($file_size > $max_size) {
                    $_SESSION['admin_message'] = "❌ Erro: O arquivo deve ter no máximo 5MB.";
                    header("Location: ".$_SERVER['PHP_SELF']);
                    exit();
                }
                
                // Gerar nome único para o arquivo
                $imagem_nome = 'produto_' . uniqid() . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $imagem_nome;
                
                // Debug: verificar caminho
                error_log("Upload path: " . $upload_path);
                
                // Verificar e criar diretório com permissões
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                if (!is_writable($upload_dir)) {
                    $_SESSION['admin_message'] = "❌ Erro: O diretório de upload não tem permissões de escrita.";
                    header("Location: ".$_SERVER['PHP_SELF']);
                    exit();
                }

                // Mover arquivo
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $imagem_url = 'uploads/produtos/' . $imagem_nome;
                    error_log("Upload successful: " . $imagem_url);
                    
                    // Verificar se o arquivo realmente existe
                    if (file_exists($upload_path)) {
                        error_log("File exists after upload: " . $upload_path);
                    } else {
                        error_log("ERROR: File does not exist after upload: " . $upload_path);
                        $_SESSION['admin_message'] = "❌ Erro: Arquivo não foi salvo corretamente no servidor.";
                        header("Location: ".$_SERVER['PHP_SELF']);
                        exit();
                    }
                } else {
                    $upload_error = error_get_last();
                    $_SESSION['admin_message'] = "❌ Erro ao fazer upload da imagem: " . ($upload_error['message'] ?? 'Erro desconhecido');
                    error_log("Upload failed: " . ($upload_error['message'] ?? 'Unknown error'));
                    header("Location: ".$_SERVER['PHP_SELF']);
                    exit();
                }
            } else {
                $upload_error = $_FILES['imagem_upload']['error'] ?? 'No file';
                $error_messages = [
                    UPLOAD_ERR_INI_SIZE => 'O arquivo excede o tamanho máximo permitido no servidor',
                    UPLOAD_ERR_FORM_SIZE => 'O arquivo excede o tamanho máximo permitido no formulário',
                    UPLOAD_ERR_PARTIAL => 'O upload do arquivo foi feito parcialmente',
                    UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi selecionado',
                    UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária não encontrada',
                    UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever o arquivo no disco',
                    UPLOAD_ERR_EXTENSION => 'Uma extensão do PHP interrompeu o upload'
                ];
                $error_message = $error_messages[$upload_error] ?? "Erro de upload (Código: $upload_error)";
                $_SESSION['admin_message'] = "❌ Erro: $error_message";
                error_log("No file uploaded or upload error: " . $upload_error);
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }
        } else {
            // Usar URL externa
            $imagem_url = trim($_POST['imagem_url']);
            if (empty($imagem_url)) {
                $_SESSION['admin_message'] = "❌ Erro: Por favor, informe uma URL para a imagem.";
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }
            
            // Validar URL da imagem
            if (!filter_var($imagem_url, FILTER_VALIDATE_URL)) {
                $_SESSION['admin_message'] = "❌ Erro: Por favor, informe uma URL válida para a imagem.";
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }
        }
        
        // Inserir o produto
        $insert_produto = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, peso, imagem, imagem_nome, id_categoria, id_tipo_cafe, id_marca_cafe, estoque, disponivel) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $insert_produto->bind_param("ssddssiiii", $nome, $descricao, $preco, $peso, $imagem_url, $imagem_nome, $id_categoria, $id_tipo_cafe, $id_marca_cafe, $estoque);
        
        if ($insert_produto->execute()) {
            $id_produto = $conn->insert_id;
            $_SESSION['admin_message'] = "✅ Produto cadastrado com sucesso! ID: $id_produto";
        } else {
            $_SESSION['admin_message'] = "❌ Erro ao cadastrar produto: " . $conn->error;
        }
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    
    // Ação de deletar produto
    if (isset($_POST['delete_product'])) {
        $id_produto = (int)$_POST['id_produto'];
        
        // Buscar informações do produto para deletar a imagem
        $select_produto = $conn->prepare("SELECT imagem_nome FROM produtos WHERE id_produto = ?");
        $select_produto->bind_param("i", $id_produto);
        $select_produto->execute();
        $result = $select_produto->get_result();
        
        if ($result->num_rows > 0) {
            $produto = $result->fetch_assoc();
            // Deletar arquivo de imagem se existir
            if (!empty($produto['imagem_nome'])) {
                $file_path = $upload_dir . $produto['imagem_nome'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
        
        $delete = $conn->prepare("DELETE FROM produtos WHERE id_produto = ?");
        $delete->bind_param("i", $id_produto);
        
        if ($delete->execute()) {
            $_SESSION['admin_message'] = "✅ Produto deletado com sucesso!";
        } else {
            $_SESSION['admin_message'] = "❌ Erro ao deletar produto: " . $conn->error;
        }
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Obter mensagem de feedback se existir
$admin_message = "";
if (isset($_SESSION['admin_message'])) {
    $admin_message = $_SESSION['admin_message'];
    unset($_SESSION['admin_message']);
}

// Consulta de produtos para exibição
$sql_products = "SELECT p.*, c.nome as categoria_nome, tc.nome as tipo_cafe, mc.nome as marca_cafe
                FROM produtos p
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                LEFT JOIN tipos_cafe tc ON p.id_tipo_cafe = tc.id_tipo
                LEFT JOIN marcas_cafe mc ON p.id_marca_cafe = mc.id_marca
                ORDER BY p.id_produto DESC";
$result_products = $conn->query($sql_products);

// Obter categorias para o formulário
$sql_categorias = "SELECT id_categoria, nome FROM categorias ORDER BY nome ASC";
$result_categorias = $conn->query($sql_categorias);

// Obter tipos de café
$sql_tipos_cafe = "SELECT id_tipo, nome FROM tipos_cafe ORDER BY nome ASC";
$result_tipos_cafe = $conn->query($sql_tipos_cafe);

// Obter marcas de café
$sql_marcas_cafe = "SELECT id_marca, nome FROM marcas_cafe ORDER BY nome ASC";
$result_marcas_cafe = $conn->query($sql_marcas_cafe);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Dev Coffee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

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
        }

        body {
            background-color: var(--light);
            color: var(--text);
        }

        .admin-header {
            background: var(--primary);
            color: var(--white);
            padding: 20px 0;
            box-shadow: var(--shadow);
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            font-size: 24px;
            font-weight: 700;
        }

        .logo span {
            color: var(--accent);
        }

        .admin-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .admin-section {
            background: var(--white);
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .admin-section h2 {
            margin-bottom: 20px;
            color: var(--primary);
            border-bottom: 2px solid var(--accent);
            padding-bottom: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--text);
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--secondary);
        }

        .file-upload {
            border: 2px dashed var(--border);
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            background: var(--light);
        }

        .file-upload:hover {
            border-color: var(--secondary);
            background: #f0f0f0;
        }

        .file-upload.dragover {
            border-color: var(--accent);
            background-color: rgba(210, 105, 30, 0.1);
        }

        .file-upload i {
            font-size: 48px;
            color: var(--gray);
            margin-bottom: 15px;
        }

        .file-upload input {
            display: none;
        }

        .image-preview {
            margin-top: 15px;
            text-align: center;
        }

        .image-preview img {
            max-width: 200px;
            max-height: 150px;
            border-radius: 4px;
            box-shadow: var(--shadow);
        }

        .upload-info {
            font-size: 12px;
            color: var(--gray);
            margin-top: 10px;
        }

        .btn {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 12px 25px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn:hover {
            background: var(--secondary);
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-success {
            background: #28a745;
        }

        .btn-success:hover {
            background: #218838;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .product-card {
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
        }

        .product-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }

        .product-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-info {
            padding: 15px;
        }

        .product-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--primary);
        }

        .product-price {
            font-size: 18px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 8px;
        }

        .product-details {
            font-size: 12px;
            color: var(--gray);
            margin-bottom: 10px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: 500;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--accent);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--gray);
            font-size: 14px;
        }

        .tab-container {
            margin-bottom: 20px;
        }

        .tabs {
            display: flex;
            border-bottom: 1px solid var(--border);
            margin-bottom: 20px;
        }

        .tab {
            padding: 12px 24px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s;
        }

        .tab.active {
            border-bottom-color: var(--accent);
            color: var(--accent);
            font-weight: 600;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .debug-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            font-size: 12px;
            color: #856404;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-actions {
                flex-direction: column;
                gap: 10px;
            }
            
            .tabs {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="header-container">
            <div class="logo">
                <h1>Dev <span>Coffee</span> Admin</h1>
            </div>
            <div class="admin-actions">
                <span>Bem-vindo, <?= htmlspecialchars($_SESSION['admin_nome']) ?></span>
                <a href="../index.php" class="btn btn-success">Ver Site</a>
                <a href="admin_logout.php" class="btn btn-danger">Sair</a>
            </div>
        </div>
    </header>

    <div class="admin-container">
        <?php if ($admin_message): ?>
            <div class="message <?= strpos($admin_message, '❌') !== false ? 'error' : 'success' ?>">
                <?= $admin_message ?>
            </div>
        <?php endif; ?>

        <!-- Debug info para verificar permissões -->
        <div class="debug-info" style="display: none;">
            <strong>Informações de Debug:</strong><br>
            Diretório de Upload: <?= realpath($upload_dir) ?: $upload_dir ?><br>
            Permissões: <?= substr(sprintf('%o', fileperms($upload_dir)), -4) ?><br>
            Existe: <?= file_exists($upload_dir) ? 'Sim' : 'Não' ?><br>
            É gravável: <?= is_writable($upload_dir) ? 'Sim' : 'Não' ?>
        </div>

        <!-- Estatísticas -->
        <div class="stats-grid">
            <?php
            // Obter estatísticas
            $total_produtos = $conn->query("SELECT COUNT(*) as total FROM produtos")->fetch_assoc()['total'];
            $total_pedidos = $conn->query("SELECT COUNT(*) as total FROM pedidos")->fetch_assoc()['total'];
            $total_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
            $produtos_sem_estoque = $conn->query("SELECT COUNT(*) as total FROM produtos WHERE estoque = 0")->fetch_assoc()['total'];
            ?>
            
            <div class="stat-card">
                <div class="stat-number"><?= $total_produtos ?></div>
                <div class="stat-label">Total de Produtos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $total_pedidos ?></div>
                <div class="stat-label">Pedidos Realizados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $total_usuarios ?></div>
                <div class="stat-label">Usuários Cadastrados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $produtos_sem_estoque ?></div>
                <div class="stat-label">Produtos Sem Estoque</div>
            </div>
        </div>

        <!-- Formulário de Cadastro -->
        <div class="admin-section">
            <h2><i class="fas fa-plus-circle"></i> Cadastrar Novo Produto</h2>
            
            <div class="tab-container">
                <div class="tabs">
                    <div class="tab active" onclick="switchTab('upload')">
                        <i class="fas fa-upload"></i> Upload de Imagem
                    </div>
                    <div class="tab" onclick="switchTab('url')">
                        <i class="fas fa-link"></i> URL Externa
                    </div>
                </div>
                
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="metodo_imagem" id="metodo_imagem" value="upload">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nome"><i class="fas fa-tag"></i> Nome do Produto:</label>
                            <input type="text" id="nome" name="nome" required placeholder="Ex: Café Frutado Premium">
                        </div>
                        
                        <div class="form-group">
                            <label for="preco"><i class="fas fa-dollar-sign"></i> Preço (R$):</label>
                            <input type="number" id="preco" name="preco" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        
                        <div class="form-group">
                            <label for="peso"><i class="fas fa-weight"></i> Peso (g):</label>
                            <input type="number" id="peso" name="peso" step="0.01" min="0" placeholder="Opcional">
                        </div>
                        
                        <div class="form-group">
                            <label for="estoque"><i class="fas fa-boxes"></i> Estoque:</label>
                            <input type="number" id="estoque" name="estoque" min="0" value="0" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao"><i class="fas fa-align-left"></i> Descrição:</label>
                        <textarea id="descricao" name="descricao" rows="3" placeholder="Descreva o produto..."></textarea>
                    </div>
                    
                    <!-- Upload de Imagem -->
                    <div class="tab-content active" id="upload-tab">
                        <div class="form-group">
                            <label><i class="fas fa-image"></i> Upload da Imagem:</label>
                            <div class="file-upload" id="fileUpload">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Clique para selecionar ou arraste uma imagem</p>
                                <input type="file" id="imagem_upload" name="imagem_upload" accept=".jpg,.jpeg,.png,.gif,.webp">
                                <div class="upload-info">
                                    <i class="fas fa-info-circle"></i> Formatos: JPG, JPEG, PNG, GIF, WEBP | Máx: 5MB
                                </div>
                            </div>
                            <div class="image-preview" id="imagePreview"></div>
                        </div>
                    </div>
                    
                    <!-- URL Externa -->
                    <div class="tab-content" id="url-tab">
                        <div class="form-group">
                            <label for="imagem_url"><i class="fas fa-link"></i> URL da Imagem:</label>
                            <input type="text" id="imagem_url" name="imagem_url" placeholder="https://exemplo.com/imagem.jpg">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="id_categoria"><i class="fas fa-list"></i> Categoria:</label>
                            <select id="id_categoria" name="id_categoria" required>
                                <option value="">Selecione uma categoria</option>
                                <?php 
                                if ($result_categorias->num_rows > 0): 
                                    while ($row = $result_categorias->fetch_assoc()): 
                                ?>
                                    <option value="<?= $row['id_categoria'] ?>"><?= htmlspecialchars($row['nome']) ?></option>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <option value="">Nenhuma categoria encontrada</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="id_tipo_cafe"><i class="fas fa-coffee"></i> Tipo de Café:</label>
                            <select id="id_tipo_cafe" name="id_tipo_cafe">
                                <option value="">Selecione o tipo</option>
                                <?php 
                                if ($result_tipos_cafe->num_rows > 0): 
                                    while ($row = $result_tipos_cafe->fetch_assoc()): 
                                ?>
                                    <option value="<?= $row['id_tipo'] ?>"><?= htmlspecialchars($row['nome']) ?></option>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <option value="">Nenhum tipo encontrado</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="id_marca_cafe"><i class="fas fa-trademark"></i> Marca:</label>
                            <select id="id_marca_cafe" name="id_marca_cafe">
                                <option value="">Selecione a marca</option>
                                <?php 
                                if ($result_marcas_cafe->num_rows > 0): 
                                    while ($row = $result_marcas_cafe->fetch_assoc()): 
                                ?>
                                    <option value="<?= $row['id_marca'] ?>"><?= htmlspecialchars($row['nome']) ?></option>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <option value="">Nenhuma marca encontrada</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" name="add_product" class="btn" style="font-size: 16px; padding: 15px 30px;">
                        <i class="fas fa-save"></i> Cadastrar Produto
                    </button>
                </form>
            </div>
        </div>

        <!-- Lista de Produtos -->
        <div class="admin-section">
            <h2><i class="fas fa-boxes"></i> Produtos Cadastrados (<?= $result_products->num_rows ?>)</h2>
            
            <?php if ($result_products->num_rows > 0): ?>
                <div class="products-grid">
                    <?php while ($product = $result_products->fetch_assoc()): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if (!empty($product['imagem']) && file_exists('../' . $product['imagem'])): ?>
                                    <img src="<?= htmlspecialchars('../' . $product['imagem']) ?>" alt="<?= htmlspecialchars($product['nome']) ?>"
                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhGOEY4Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlbSBuw6NvIGNhcnJlZ2FkYTwvdGV4dD48L3N2Zz4='">
                                <?php else: ?>
                                    <div style="width:100%;height:200px;background:#f8f8f8;display:flex;align-items:center;justify-content:center;color:#999;flex-direction:column;gap:10px;">
                                        <i class="fas fa-image" style="font-size:48px;"></i>
                                        <small>Sem imagem</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-info">
                                <h3 class="product-title"><?= htmlspecialchars($product['nome']) ?></h3>
                                <div class="product-price">R$ <?= number_format($product['preco'], 2, ',', '.') ?></div>
                                
                                <div class="product-details">
                                    <p><i class="fas fa-list"></i> <strong>Categoria:</strong> <?= htmlspecialchars($product['categoria_nome'] ?? 'N/A') ?></p>
                                    <?php if ($product['tipo_cafe']): ?>
                                        <p><i class="fas fa-coffee"></i> <strong>Tipo:</strong> <?= htmlspecialchars($product['tipo_cafe']) ?></p>
                                    <?php endif; ?>
                                    <?php if ($product['marca_cafe']): ?>
                                        <p><i class="fas fa-tag"></i> <strong>Marca:</strong> <?= htmlspecialchars($product['marca_cafe']) ?></p>
                                    <?php endif; ?>
                                    <?php if ($product['peso']): ?>
                                        <p><i class="fas fa-weight"></i> <strong>Peso:</strong> <?= number_format($product['peso'], 0, ',', '.') ?>g</p>
                                    <?php endif; ?>
                                    <p><i class="fas fa-boxes"></i> <strong>Estoque:</strong> 
                                        <span style="color: <?= $product['estoque'] > 0 ? 'green' : 'red' ?>; font-weight: bold;">
                                            <?= $product['estoque'] ?>
                                        </span>
                                    </p>
                                    <p><i class="fas fa-id-card"></i> <strong>ID:</strong> <?= $product['id_produto'] ?></p>
                                    <?php if ($product['imagem_nome']): ?>
                                        <p><i class="fas fa-file-image"></i> <strong>Arquivo:</strong> <?= htmlspecialchars($product['imagem_nome']) ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-actions">
                                    <form method="POST" style="flex: 1;">
                                        <input type="hidden" name="id_produto" value="<?= $product['id_produto'] ?>">
                                        <button type="submit" name="delete_product" class="btn btn-danger" 
                                                onclick="return confirm('Tem certeza que deseja deletar o produto <?= htmlspecialchars($product['nome']) ?>?')">
                                            <i class="fas fa-trash"></i> Deletar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: var(--gray);">
                    <i class="fas fa-box-open" style="font-size: 64px; margin-bottom: 20px;"></i>
                    <h3>Nenhum produto cadastrado</h3>
                    <p>Comece cadastrando seu primeiro produto usando o formulário acima.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Sistema de tabs
        function switchTab(tabName) {
            // Esconder todas as tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Mostrar tab selecionada
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('active');
            
            // Atualizar método de imagem
            document.getElementById('metodo_imagem').value = tabName;
        }

        // Sistema de upload de arquivos
        const fileUpload = document.getElementById('fileUpload');
        const fileInput = document.getElementById('imagem_upload');
        const imagePreview = document.getElementById('imagePreview');

        // Clique na área de upload
        fileUpload.addEventListener('click', () => {
            fileInput.click();
        });

        // Drag and drop
        fileUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUpload.classList.add('dragover');
        });

        fileUpload.addEventListener('dragleave', () => {
            fileUpload.classList.remove('dragover');
        });

        fileUpload.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUpload.classList.remove('dragover');
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                previewImage(e.dataTransfer.files[0]);
            }
        });

        // Seleção de arquivo via input
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length) {
                previewImage(e.target.files[0]);
            }
        });

        // Preview da imagem
        function previewImage(file) {
            // Validar tipo de arquivo
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('❌ Por favor, selecione apenas imagens (JPG, JPEG, PNG, GIF, WEBP)');
                fileInput.value = '';
                return;
            }

            // Validar tamanho (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('❌ A imagem deve ter no máximo 5MB');
                fileInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <p><i class="fas fa-check-circle" style="color: green;"></i> ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</p>
                `;
            };
            reader.readAsDataURL(file);
        }

        // Validação do formulário
        document.querySelector('form').addEventListener('submit', (e) => {
            const metodoImagem = document.getElementById('metodo_imagem').value;
            const fileInput = document.getElementById('imagem_upload');
            const urlInput = document.getElementById('imagem_url');
            
            if (metodoImagem === 'upload' && !fileInput.files.length) {
                e.preventDefault();
                alert('❌ Por favor, selecione uma imagem para upload.');
                return;
            }
            
            if (metodoImagem === 'url' && !urlInput.value.trim()) {
                e.preventDefault();
                alert('❌ Por favor, informe uma URL para a imagem.');
                return;
            }
        });

        // Limpar preview quando mudar de tab
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('active');
            document.getElementById('metodo_imagem').value = tabName;

            // Limpar preview quando mudar para URL
            if (tabName === 'url') {
                imagePreview.innerHTML = '';
                fileInput.value = '';
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>```