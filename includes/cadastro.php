<?php
session_start();

// Verificar se usuário já está logado
if(isset($_SESSION['usuario_id'])){
    header("Location: index.php");
    exit();
}

// Incluir arquivos com caminhos corrigidos
include('../config/database.php');
include('../models/Usuario.php');

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

$mensagem = "";
$tipo_mensagem = ""; // success ou error

if($_POST){
    $usuario->nome = $_POST['nome'];
    $usuario->email = $_POST['email'];
    $usuario->senha = $_POST['senha'];
    $usuario->telefone = $_POST['telefone'];
    $usuario->cpf = $_POST['cpf'];
    $usuario->data_nascimento = $_POST['data_nascimento'];
    $usuario->endereco = $_POST['endereco'];
    $usuario->numero = $_POST['numero'];
    $usuario->complemento = $_POST['complemento'];
    $usuario->bairro = $_POST['bairro'];
    $usuario->cidade = $_POST['cidade'];
    $usuario->estado = $_POST['estado'];
    $usuario->cep = $_POST['cep'];

    // Validar dados
    if($usuario->emailExiste()){
        $mensagem = "Este email já está cadastrado!";
        $tipo_mensagem = "error";
    } else if($usuario->cpfExiste()){
        $mensagem = "Este CPF já está cadastrado!";
        $tipo_mensagem = "error";
    } else if(strlen($_POST['senha']) < 6){
        $mensagem = "A senha deve ter pelo menos 6 caracteres!";
        $tipo_mensagem = "error";
    } else if($_POST['senha'] != $_POST['confirmar_senha']){
        $mensagem = "As senhas não coincidem!";
        $tipo_mensagem = "error";
    } else {
        // Tentar cadastrar
        if($usuario->cadastrar()){
            $mensagem = "Cadastro realizado com sucesso! Faça login para continuar.";
            $tipo_mensagem = "success";
            $_POST = array(); // Limpar formulário
        } else {
            $mensagem = "Erro ao cadastrar. Tente novamente.";
            $tipo_mensagem = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Dev Coffee</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        .auth-container {
            background: var(--black);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .auth-form {
            background: var(--bg);
            padding: 3rem;
            border-radius: 1rem;
            border: var(--border);
            width: 100%;
            max-width: 500px;
        }

        .auth-form h2 {
            color: var(--main-color);
            font-size: 3rem;
            text-align: center;
            margin-bottom: 2rem;
            text-transform: uppercase;
        }

        .form-group {
            margin-bottom: 2rem;
        }

        .form-group label {
            display: block;
            color: #fff;
            font-size: 1.6rem;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 1rem;
            background: var(--black);
            border: var(--border);
            border-radius: 0.5rem;
            color: #fff;
            font-size: 1.6rem;
        }

        .form-group input:focus {
            border-color: var(--main-color);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .auth-links {
            text-align: center;
            margin-top: 2rem;
        }

        .auth-links a {
            color: var(--main-color);
            font-size: 1.6rem;
            text-decoration: none;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .message {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 1.6rem;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <form class="auth-form" method="POST" action="">
            <h2>Criar Conta</h2>

            <?php if($mensagem): ?>
                <div class="message <?php echo $tipo_mensagem; ?>">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="nome">Nome Completo *</label>
                <input type="text" id="nome" name="nome" value="<?php echo isset($_POST['nome']) ? $_POST['nome'] : ''; ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone *</label>
                    <input type="tel" id="telefone" name="telefone" value="<?php echo isset($_POST['telefone']) ? $_POST['telefone'] : ''; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="cpf">CPF *</label>
                    <input type="text" id="cpf" name="cpf" value="<?php echo isset($_POST['cpf']) ? $_POST['cpf'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="data_nascimento">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo isset($_POST['data_nascimento']) ? $_POST['data_nascimento'] : ''; ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="senha">Senha *</label>
                    <input type="password" id="senha" name="senha" required>
                </div>

                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Senha *</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                </div>
            </div>

            <div class="form-group">
                <label for="cep">CEP *</label>
                <input type="text" id="cep" name="cep" value="<?php echo isset($_POST['cep']) ? $_POST['cep'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="endereco">Endereço *</label>
                <input type="text" id="endereco" name="endereco" value="<?php echo isset($_POST['endereco']) ? $_POST['endereco'] : ''; ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="numero">Número *</label>
                    <input type="text" id="numero" name="numero" value="<?php echo isset($_POST['numero']) ? $_POST['numero'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="complemento">Complemento</label>
                    <input type="text" id="complemento" name="complemento" value="<?php echo isset($_POST['complemento']) ? $_POST['complemento'] : ''; ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="bairro">Bairro *</label>
                    <input type="text" id="bairro" name="bairro" value="<?php echo isset($_POST['bairro']) ? $_POST['bairro'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="cidade">Cidade *</label>
                    <input type="text" id="cidade" name="cidade" value="<?php echo isset($_POST['cidade']) ? $_POST['cidade'] : ''; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="estado">Estado *</label>
                <select id="estado" name="estado" required>
                    <option value="">Selecione</option>
                    <option value="AC" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'AC') ? 'selected' : ''; ?>>Acre</option>
                    <option value="AL" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'AL') ? 'selected' : ''; ?>>Alagoas</option>
                    <option value="AP" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'AP') ? 'selected' : ''; ?>>Amapá</option>
                    <option value="AM" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'AM') ? 'selected' : ''; ?>>Amazonas</option>
                    <option value="BA" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'BA') ? 'selected' : ''; ?>>Bahia</option>
                    <option value="CE" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'CE') ? 'selected' : ''; ?>>Ceará</option>
                    <option value="DF" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'DF') ? 'selected' : ''; ?>>Distrito Federal</option>
                    <option value="ES" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'ES') ? 'selected' : ''; ?>>Espírito Santo</option>
                    <option value="GO" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'GO') ? 'selected' : ''; ?>>Goiás</option>
                    <option value="MA" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'MA') ? 'selected' : ''; ?>>Maranhão</option>
                    <option value="MT" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'MT') ? 'selected' : ''; ?>>Mato Grosso</option>
                    <option value="MS" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'MS') ? 'selected' : ''; ?>>Mato Grosso do Sul</option>
                    <option value="MG" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'MG') ? 'selected' : ''; ?>>Minas Gerais</option>
                    <option value="PA" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PA') ? 'selected' : ''; ?>>Pará</option>
                    <option value="PB" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PB') ? 'selected' : ''; ?>>Paraíba</option>
                    <option value="PR" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PR') ? 'selected' : ''; ?>>Paraná</option>
                    <option value="PE" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PE') ? 'selected' : ''; ?>>Pernambuco</option>
                    <option value="PI" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PI') ? 'selected' : ''; ?>>Piauí</option>
                    <option value="RJ" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RJ') ? 'selected' : ''; ?>>Rio de Janeiro</option>
                    <option value="RN" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RN') ? 'selected' : ''; ?>>Rio Grande do Norte</option>
                    <option value="RS" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RS') ? 'selected' : ''; ?>>Rio Grande do Sul</option>
                    <option value="RO" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RO') ? 'selected' : ''; ?>>Rondônia</option>
                    <option value="RR" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RR') ? 'selected' : ''; ?>>Roraima</option>
                    <option value="SC" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'SC') ? 'selected' : ''; ?>>Santa Catarina</option>
                    <option value="SP" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'SP') ? 'selected' : ''; ?>>São Paulo</option>
                    <option value="SE" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'SE') ? 'selected' : ''; ?>>Sergipe</option>
                    <option value="TO" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'TO') ? 'selected' : ''; ?>>Tocantins</option>
                </select>
            </div>

            <button type="submit" class="btn" style="width: 100%;">Cadastrar</button>

            <div class="auth-links">
                <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
            </div>
        </form>
    </div>

    <script>
        // Máscara para CPF
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }
            e.target.value = value;
        });

        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            e.target.value = value;
        });

        // Máscara para CEP
        document.getElementById('cep').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 8) {
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            e.target.value = value;
        });
    </script>
</body>
</html>