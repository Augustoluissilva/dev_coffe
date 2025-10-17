<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nome;
    public $email;
    public $senha;
    public $telefone;
    public $cpf;
    public $data_nascimento;
    public $endereco;
    public $numero;
    public $complemento;
    public $bairro;
    public $cidade;
    public $estado;
    public $cep;
    public $tipo;
    public $google_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function cadastrar() {
        // Primeiro verificar se email já existe
        if ($this->emailExiste()) {
            return "email_existe";
        }

        // Verificar se CPF já existe
        if ($this->cpfExiste()) {
            return "cpf_existe";
        }

        // Remover caracteres especiais do CPF, telefone e CEP
        $cpf_limpo = preg_replace('/[^0-9]/', '', $this->cpf);
        $telefone_limpo = preg_replace('/[^0-9]/', '', $this->telefone);
        $cep_limpo = preg_replace('/[^0-9]/', '', $this->cep);

        // Query mais simples - usando apenas colunas básicas que sabemos que existem
        $query = "INSERT INTO " . $this->table_name . " 
                 SET nome_completo=:nome, email=:email, senha=:senha, telefone=:telefone,
                     cpf=:cpf";
        
        $stmt = $this->conn->prepare($query);

        // Limpar dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Hash da senha
        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);

        // Bind parameters
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":senha", $this->senha);
        $stmt->bindParam(":telefone", $telefone_limpo);
        $stmt->bindParam(":cpf", $cpf_limpo);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Método para cadastrar com Google
    public function cadastrarComGoogle() {
        try {
            // Verificar se email já existe
            if ($this->emailExiste()) {
                return "email_existe";
            }

            // Preparar query para cadastro com Google
            $query = "INSERT INTO " . $this->table_name . " 
                     SET nome_completo=:nome, email=:email, senha=:senha, 
                         google_id=:google_id, telefone=:telefone, cpf=:cpf";
            
            $stmt = $this->conn->prepare($query);

            // Limpar dados
            $this->nome = htmlspecialchars(strip_tags($this->nome));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->google_id = htmlspecialchars(strip_tags($this->google_id));

            // Hash da senha (senha aleatória para contas Google)
            if (empty($this->senha)) {
                $this->senha = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            }

            // Bind parameters
            $stmt->bindParam(":nome", $this->nome);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":senha", $this->senha);
            $stmt->bindParam(":google_id", $this->google_id);
            $stmt->bindParam(":telefone", $this->telefone);
            $stmt->bindParam(":cpf", $this->cpf);

            if($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
            
        } catch(PDOException $exception) {
            error_log("Erro ao cadastrar com Google: " . $exception->getMessage());
            return false;
        }
    }

    public function login() {
        // Query mais genérica - selecionar todas as colunas
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE email = :email AND ativo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar senha
            if(password_verify($this->senha, $row['senha'])) {
                // Encontrar a chave primária automaticamente
                $this->id = $this->encontrarChavePrimaria($row);
                $this->nome = $row['nome_completo'] ?? $row['nome'] ?? '';
                $this->tipo = $row['tipo'] ?? 'cliente';
                $this->google_id = $row['google_id'] ?? '';
                
                // Atualizar último login
                $this->atualizarUltimoLogin();
                
                return true;
            }
        }
        return false;
    }

    // Método para login com Google
    public function loginComGoogle($google_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE google_id = :google_id AND ativo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":google_id", $google_id);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Encontrar a chave primária automaticamente
            $this->id = $this->encontrarChavePrimaria($row);
            $this->nome = $row['nome_completo'] ?? $row['nome'] ?? '';
            $this->email = $row['email'] ?? '';
            $this->tipo = $row['tipo'] ?? 'cliente';
            $this->google_id = $row['google_id'] ?? '';
            
            // Atualizar último login
            $this->atualizarUltimoLogin();
            
            return true;
        }
        return false;
    }

    // Método para buscar usuário por email (incluindo Google ID)
    public function buscarPorEmail() {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE email = :email AND ativo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Encontrar a chave primária automaticamente
            $this->id = $this->encontrarChavePrimaria($row);
            $this->nome = $row['nome_completo'] ?? $row['nome'] ?? '';
            $this->email = $row['email'] ?? '';
            $this->senha = $row['senha'] ?? '';
            $this->telefone = $row['telefone'] ?? '';
            $this->cpf = $row['cpf'] ?? '';
            $this->tipo = $row['tipo'] ?? 'cliente';
            $this->google_id = $row['google_id'] ?? '';
            
            return true;
        }
        return false;
    }

    // Método para buscar usuário por Google ID
    public function buscarPorGoogleId($google_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE google_id = :google_id AND ativo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":google_id", $google_id);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Encontrar a chave primária automaticamente
            $this->id = $this->encontrarChavePrimaria($row);
            $this->nome = $row['nome_completo'] ?? $row['nome'] ?? '';
            $this->email = $row['email'] ?? '';
            $this->senha = $row['senha'] ?? '';
            $this->telefone = $row['telefone'] ?? '';
            $this->cpf = $row['cpf'] ?? '';
            $this->tipo = $row['tipo'] ?? 'cliente';
            $this->google_id = $row['google_id'] ?? '';
            
            return true;
        }
        return false;
    }

    // Método para vincular conta Google a usuário existente
    public function vincularGoogle($user_id, $google_id) {
        $query = "UPDATE " . $this->table_name . " 
                 SET google_id = :google_id 
                 WHERE id = :id OR usuario = :id OR user_id = :id OR codigo = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":google_id", $google_id);
        $stmt->bindParam(":id", $user_id);

        return $stmt->execute();
    }

    // Método para verificar se Google ID já existe
    public function googleIdExiste($google_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                 WHERE google_id = :google_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":google_id", $google_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    private function encontrarChavePrimaria($row) {
        // Tentar encontrar a chave primária nos nomes mais comuns
        $possible_keys = ['id', 'usuario', 'user_id', 'codigo', 'cd_usuario'];
        
        foreach ($possible_keys as $key) {
            if (isset($row[$key])) {
                return $row[$key];
            }
        }
        
        // Se não encontrar, retornar o primeiro valor do array
        return reset($row);
    }

    private function atualizarUltimoLogin() {
        if (!$this->id) return false;

        // Tentar diferentes nomes de chave primária
        $possible_keys = ['id', 'usuario', 'user_id', 'codigo', 'cd_usuario'];
        
        foreach ($possible_keys as $key) {
            try {
                $query = "UPDATE " . $this->table_name . " SET ultimo_login = NOW() WHERE " . $key . " = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":id", $this->id);
                if($stmt->execute()) {
                    return true;
                }
            } catch (PDOException $e) {
                // Continua para a próxima tentativa
                continue;
            }
        }
        return false;
    }

    public function emailExiste() {
        // Usar COUNT(*) que funciona em qualquer tabela
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    public function cpfExiste() {
        $cpf_limpo = preg_replace('/[^0-9]/', '', $this->cpf);
        
        // Usar COUNT(*) que funciona em qualquer tabela
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE cpf = :cpf";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cpf", $cpf_limpo);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    // Método para buscar usuário por ID
    public function buscarPorId($id) {
        // Buscar todas as colunas
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id OR usuario = :id OR user_id = :id OR codigo = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Preencher propriedades
            $this->id = $this->encontrarChavePrimaria($row);
            $this->nome = $row['nome_completo'] ?? $row['nome'] ?? '';
            $this->email = $row['email'] ?? '';
            $this->telefone = $row['telefone'] ?? '';
            $this->cpf = $row['cpf'] ?? '';
            $this->data_nascimento = $row['data_nascimento'] ?? '';
            $this->endereco = $row['endereco'] ?? '';
            $this->numero = $row['numero'] ?? '';
            $this->complemento = $row['complemento'] ?? '';
            $this->bairro = $row['bairro'] ?? '';
            $this->cidade = $row['cidade'] ?? '';
            $this->estado = $row['estado'] ?? '';
            $this->cep = $row['cep'] ?? '';
            $this->tipo = $row['tipo'] ?? 'cliente';
            $this->google_id = $row['google_id'] ?? '';
            
            return true;
        }
        return false;
    }

    // Método para atualizar perfil do usuário
    public function atualizar() {
        if (!$this->id) return false;

        // Remover caracteres especiais
        $cpf_limpo = preg_replace('/[^0-9]/', '', $this->cpf);
        $telefone_limpo = preg_replace('/[^0-9]/', '', $this->telefone);
        $cep_limpo = preg_replace('/[^0-9]/', '', $this->cep);

        // Query básica com campos essenciais
        $query = "UPDATE " . $this->table_name . " 
                 SET nome_completo=:nome, email=:email, telefone=:telefone,
                     cpf=:cpf
                 WHERE id = :id OR usuario = :id OR user_id = :id OR codigo = :id";
        
        $stmt = $this->conn->prepare($query);

        // Limpar dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Bind parameters
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telefone", $telefone_limpo);
        $stmt->bindParam(":cpf", $cpf_limpo);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Método para alterar senha
    public function alterarSenha($nova_senha) {
        if (!$this->id) return false;

        $query = "UPDATE " . $this->table_name . " SET senha = :senha WHERE id = :id OR usuario = :id OR user_id = :id OR codigo = :id";
        $stmt = $this->conn->prepare($query);
        
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        $stmt->bindParam(":senha", $senha_hash);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Método para listar todos os usuários (para administradores)
    public function listarTodos() {
        // Selecionar colunas básicas que provavelmente existem
        $query = "SELECT * FROM " . $this->table_name . " 
                 ORDER BY data_cadastro DESC, ultimo_login DESC, nome_completo ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // Método para descobrir a estrutura da tabela (para debug)
    public function debugEstrutura() {
        try {
            $query = "DESCRIBE " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Erro ao obter estrutura: " . $e->getMessage();
        }
    }
}
?>