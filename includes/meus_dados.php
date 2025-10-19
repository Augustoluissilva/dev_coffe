<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    // header("Location: login.php");
    // exit();
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cafeteria";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Buscar informações do usuário
$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT nome_completo, cpf, email, telefone, data_cadastro, avatar FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$usuario_info = array();
if ($result->num_rows > 0) {
    $usuario_info = $result->fetch_assoc();
}

// Processar upload de imagem
$mensagem_upload = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $upload_dir = '../uploads/usuarios/';
    
    // Criar diretório se não existir
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $arquivo = $_FILES['avatar'];
    $nome_arquivo = 'usuario_' . $usuario_id . '_' . time() . '.' . pathinfo($arquivo['name'], PATHINFO_EXTENSION);
    $caminho_completo = $upload_dir . $nome_arquivo;
    
    // Validar tipo de arquivo
    $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $tipo_arquivo = mime_content_type($arquivo['tmp_name']);
    
    if (in_array($tipo_arquivo, $tipos_permitidos)) {
        // Validar tamanho (máximo 2MB)
        if ($arquivo['size'] <= 2 * 1024 * 1024) {
            if (move_uploaded_file($arquivo['tmp_name'], $caminho_completo)) {
                // Atualizar no banco de dados - campo avatar VARCHAR
                $sql_update = "UPDATE usuarios SET avatar = ? WHERE id_usuario = ?";
                $stmt_update = $conn->prepare($sql_update);
                $caminho_relativo = 'uploads/usuarios/' . $nome_arquivo;
                $stmt_update->bind_param("si", $caminho_relativo, $usuario_id);
                
                if ($stmt_update->execute()) {
                    $mensagem_upload = '<div class="mensagem-sucesso">Foto de perfil atualizada com sucesso!</div>';
                    $usuario_info['avatar'] = $caminho_relativo;
                    $_SESSION['usuario_avatar'] = $caminho_relativo; // Atualizar na sessão
                } else {
                    $mensagem_upload = '<div class="mensagem-erro">Erro ao atualizar no banco de dados.</div>';
                }
                $stmt_update->close();
            } else {
                $mensagem_upload = '<div class="mensagem-erro">Erro ao fazer upload do arquivo.</div>';
            }
        } else {
            $mensagem_upload = '<div class="mensagem-erro">Arquivo muito grande. Tamanho máximo: 2MB.</div>';
        }
    } else {
        $mensagem_upload = '<div class="mensagem-erro">Tipo de arquivo não permitido. Use JPG, PNG ou GIF.</div>';
    }
}

$usuario_nome = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Visitante';

$conn->close();
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

        .dados-item.active {
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

        .dados-item.active .dados-arrow {
            transform: rotate(180deg);
            color: var(--accent);
        }

        .dados-item:hover .dados-arrow {
            color: var(--accent);
        }

        /* Área de informações */
        .info-area {
            background: var(--white);
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: var(--transition);
            border-bottom: 1px solid var(--border-color);
        }

        .info-area.active {
            padding: 25px;
            max-height: 800px;
        }

        .info-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-dark);
            border-bottom: 2px solid var(--accent);
            padding-bottom: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 500;
            color: var(--text-dark);
        }

        .info-value {
            color: var(--text-gray);
        }

        /* Upload de Imagem */
        .upload-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            margin: 25px 0;
            padding: 25px;
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            background: var(--light-gray);
            transition: var(--transition);
        }

        .upload-area:hover {
            border-color: var(--accent);
            background: #f0f0f0;
        }

        .foto-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent);
            box-shadow: var(--shadow);
        }

        .foto-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-gray);
            font-size: 40px;
            border: 3px dashed var(--text-gray);
        }

        .upload-btn {
            background: var(--accent);
            color: var(--white);
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .upload-btn:hover {
            background: #c49565;
            transform: translateY(-2px);
        }

        .file-input {
            display: none;
        }

        .upload-text {
            text-align: center;
            color: var(--text-gray);
        }

        .upload-text h4 {
            margin-bottom: 8px;
            color: var(--text-dark);
        }

        .upload-text p {
            font-size: 14px;
            margin-bottom: 15px;
        }

        /* Mensagens */
        .mensagem-sucesso {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            margin: 15px 0;
        }

        .mensagem-erro {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
            margin: 15px 0;
        }

        .empty-state {
            text-align: center;
            padding: 30px 20px;
            color: var(--text-gray);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: var(--border-color);
        }

        .btn-editar {
            background: var(--accent);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            margin-top: 20px;
        }

        .btn-editar:hover {
            background: #c49565;
            transform: translateY(-2px);
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

            .info-area.active {
                padding: 20px;
            }

            .info-title {
                font-size: 16px;
            }

            .info-item {
                flex-direction: column;
                gap: 5px;
            }

            .upload-area {
                padding: 20px;
            }

            .foto-preview,
            .foto-placeholder {
                width: 100px;
                height: 100px;
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
                <!-- Informações Pessoais -->
                <div class="dados-item" data-target="info-pessoais">
                    <div class="dados-content">
                        <div class="dados-item-title">Informações pessoais</div>
                        <div class="dados-item-description">Nome completo, CPF e foto de perfil</div>
                    </div>
                    <div class="dados-arrow">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                
                <div class="info-area" id="info-pessoais">
                    <h3 class="info-title">Informações Pessoais</h3>
                    
                    <!-- Upload de Avatar -->
                    <div class="upload-area">
                        <div class="upload-text">
                            <h4>Foto de Perfil</h4>
                            <p>Adicione uma foto para personalizar seu perfil</p>
                        </div>
                        
                        <?php 
                        $avatar_path = '';
                        if (!empty($usuario_info['avatar']) && $usuario_info['avatar'] !== 'default-avatar.jpg') {
                            $avatar_path = '../' . $usuario_info['avatar'];
                        }
                        
                        if (!empty($avatar_path) && file_exists($avatar_path)): ?>
                            <img src="<?php echo htmlspecialchars($avatar_path); ?>" 
                                 alt="Foto de perfil" 
                                 class="foto-preview"
                                 id="fotoPreview">
                        <?php else: ?>
                            <div class="foto-placeholder" id="fotoPlaceholder">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" id="uploadForm">
                            <input type="file" name="avatar" id="avatarInput" class="file-input" accept="image/jpeg,image/jpg,image/png,image/gif">
                            <button type="button" class="upload-btn" onclick="document.getElementById('avatarInput').click()">
                                <i class="fas fa-camera"></i>
                                Escolher Foto
                            </button>
                            <button type="submit" class="upload-btn" style="background: #28a745;">
                                <i class="fas fa-upload"></i>
                                Enviar Foto
                            </button>
                        </form>
                        
                        <?php echo $mensagem_upload; ?>
                    </div>

                    <?php if (!empty($usuario_info)): ?>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Nome completo:</span>
                                <span class="info-value"><?php echo htmlspecialchars($usuario_info['nome_completo'] ?? 'Não informado'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">CPF:</span>
                                <span class="info-value"><?php 
                                    $cpf = $usuario_info['cpf'] ?? '';
                                    if (!empty($cpf)) {
                                        echo preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
                                    } else {
                                        echo 'Não informado';
                                    }
                                ?></span>
                            </div>
                        </div>
                        <button class="btn-editar">Editar Informações</button>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-user-circle"></i>
                            <p>Nenhuma informação pessoal cadastrada</p>
                            <button class="btn-editar">Cadastrar Informações</button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Restante das seções -->
                <div class="dados-item" data-target="dados-contato">
                    <div class="dados-content">
                        <div class="dados-item-title">Dados de contato</div>
                        <div class="dados-item-description">E-mail e telefone de contato</div>
                    </div>
                    <div class="dados-arrow">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                
                <div class="info-area" id="dados-contato">
                    <h3 class="info-title">Dados de Contato</h3>
                    <?php if (!empty($usuario_info)): ?>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">E-mail:</span>
                                <span class="info-value"><?php echo htmlspecialchars($usuario_info['email'] ?? 'Não informado'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Telefone:</span>
                                <span class="info-value"><?php 
                                    $telefone = $usuario_info['telefone'] ?? '';
                                    if (!empty($telefone)) {
                                        echo preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
                                    } else {
                                        echo 'Não informado';
                                    }
                                ?></span>
                            </div>
                        </div>
                        <button class="btn-editar">Editar Contato</button>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-envelope"></i>
                            <p>Nenhum dado de contato cadastrado</p>
                            <button class="btn-editar">Cadastrar Contato</button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="dados-item" data-target="credenciais">
                    <div class="dados-content">
                        <div class="dados-item-title">Credenciais</div>
                        <div class="dados-item-description">Meios de acesso à minha conta</div>
                    </div>
                    <div class="dados-arrow">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                
                <div class="info-area" id="credenciais">
                    <h3 class="info-title">Credenciais de Acesso</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Data de cadastro:</span>
                            <span class="info-value"><?php 
                                if (!empty($usuario_info['data_cadastro'])) {
                                    echo date('d/m/Y', strtotime($usuario_info['data_cadastro']));
                                } else {
                                    echo 'Não disponível';
                                }
                            ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status da conta:</span>
                            <span class="info-value" style="color: #28a745;">Ativa</span>
                        </div>
                    </div>
                    <button class="btn-editar">Alterar Senha</button>
                </div>

                <div class="dados-item" data-target="enderecos">
                    <div class="dados-content">
                        <div class="dados-item-title">Endereços</div>
                        <div class="dados-item-description">Gerenciar meus endereços</div>
                    </div>
                    <div class="dados-arrow">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                
                <div class="info-area" id="enderecos">
                    <h3 class="info-title">Meus Endereços</h3>
                    <div class="empty-state">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>Nenhum endereço cadastrado</p>
                        <button class="btn-editar">Cadastrar Endereço</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dadosItems = document.querySelectorAll('.dados-item');
            let activeItem = null;

            // Configurar eventos de clique
            dadosItems.forEach(item => {
                item.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const targetArea = document.getElementById(targetId);

                    // Se clicar no mesmo item, fecha a área
                    if (activeItem === this) {
                        this.classList.remove('active');
                        targetArea.classList.remove('active');
                        activeItem = null;
                        return;
                    }

                    // Fecha a área ativa anterior (se houver)
                    if (activeItem) {
                        const previousTargetId = activeItem.getAttribute('data-target');
                        const previousArea = document.getElementById(previousTargetId);
                        activeItem.classList.remove('active');
                        previousArea.classList.remove('active');
                    }

                    // Abre a nova área
                    this.classList.add('active');
                    targetArea.classList.add('active');
                    activeItem = this;

                    // Efeito visual de clique
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });

                // Acessibilidade - navegação por teclado
                item.setAttribute('tabindex', '0');
                item.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            // Preview da imagem antes do upload
            const avatarInput = document.getElementById('avatarInput');
            if (avatarInput) {
                avatarInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // Remove o placeholder se existir
                            const placeholder = document.getElementById('fotoPlaceholder');
                            if (placeholder) {
                                placeholder.style.display = 'none';
                            }
                            
                            // Cria ou atualiza a preview
                            let preview = document.getElementById('fotoPreview');
                            if (!preview) {
                                preview = document.createElement('img');
                                preview.id = 'fotoPreview';
                                preview.className = 'foto-preview';
                                document.querySelector('.upload-area').insertBefore(preview, document.querySelector('.upload-text').nextSibling);
                            }
                            
                            preview.src = e.target.result;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Eventos para botões de editar
            document.querySelectorAll('.btn-editar').forEach(btn => {
                btn.addEventListener('click', function() {
                    const section = this.closest('.info-area').id;
                    alert(`Função de edição para ${section} será implementada aqui!`);
                });
            });
        });
    </script>
</body>
</html>