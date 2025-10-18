<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id_usuario;
    public $nome_completo;
    public $email;
    public $senha;
    public $telefone;
    public $cpf;
    public $tipo;
    public $ativo;
    public $avatar;
    public $data_cadastro;
    public $ultimo_login;
    public $token_senha;
    public $token_expiracao;
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

        // Remover caracteres especiais do CPF e telefone
        $cpf_limpo = preg_replace('/[^0-9]/', '', $this->cpf);
        $telefone_limpo = preg_replace('/[^0-9]/', '', $this->telefone);

        $query = "INSERT INTO " . $this->table_name . " 
                 SET nome_completo=:nome_completo, email=:email, senha=:senha, 
                     telefone=:telefone, cpf=:cpf, tipo='cliente', ativo=1";
        
        $stmt = $this->conn->prepare($query);

        // Limpar dados
        $this->nome_completo = htmlspecialchars(strip_tags($this->nome_completo));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Hash da senha
        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);

        // Bind parameters
        $stmt->bindParam(":nome_completo", $this->nome_completo);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":senha", $this->senha);
        $stmt->bindParam(":telefone", $telefone_limpo);
        $stmt->bindParam(":cpf", $cpf_limpo);

        if($stmt->execute()) {
            $this->id_usuario = $this->conn->lastInsertId();
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

            $query = "INSERT INTO " . $this->table_name . " 
                     SET nome_completo=:nome_completo, email=:email, senha=:senha, 
                         google_id=:google_id, telefone=:telefone, cpf=:cpf, 
                         tipo='cliente', ativo=1, data_cadastro=NOW()";
            
            $stmt = $this->conn->prepare($query);

            // Limpar dados
            $this->nome_completo = htmlspecialchars(strip_tags($this->nome_completo));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->google_id = htmlspecialchars(strip_tags($this->google_id));

            // Hash da senha (senha aleatória para contas Google)
            if (empty($this->senha)) {
                $this->senha = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            }

            // Valores padrão para campos obrigatórios
            if (empty($this->telefone)) {
                $this->telefone = 'Não informado';
            }
            if (empty($this->cpf)) {
                $this->cpf = '000.000.000-00';
            }

            // Bind parameters
            $stmt->bindParam(":nome_completo", $this->nome_completo);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":senha", $this->senha);
            $stmt->bindParam(":google_id", $this->google_id);
            $stmt->bindParam(":telefone", $this->telefone);
            $stmt->bindParam(":cpf", $this->cpf);

            if($stmt->execute()) {
                $this->id_usuario = $this->conn->lastInsertId();
                return true;
            }
            return false;
            
        } catch(PDOException $exception) {
            error_log("Erro ao cadastrar com Google: " . $exception->getMessage());
            return false;
        }
    }

    public function login() {
        $query = "SELECT id_usuario, nome_completo, email, senha, tipo, google_id, ativo 
                 FROM " . $this->table_name . " 
                 WHERE email = :email AND ativo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar senha
            if(password_verify($this->senha, $row['senha'])) {
                $this->id_usuario = $row['id_usuario'];
                $this->nome_completo = $row['nome_completo'];
                $this->tipo = $row['tipo'];
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
        $query = "SELECT id_usuario, nome_completo, email, tipo, google_id 
                 FROM " . $this->table_name . " 
                 WHERE google_id = :google_id AND ativo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":google_id", $google_id);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id_usuario = $row['id_usuario'];
            $this->nome_completo = $row['nome_completo'];
            $this->email = $row['email'];
            $this->tipo = $row['tipo'];
            $this->google_id = $row['google_id'];
            
            // Atualizar último login
            $this->atualizarUltimoLogin();
            
            return true;
        }
        return false;
    }

    // Método para buscar usuário por email
    public function buscarPorEmail() {
        $query = "SELECT id_usuario, nome_completo, email, senha, telefone, cpf, tipo, google_id 
                 FROM " . $this->table_name . " 
                 WHERE email = :email AND ativo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id_usuario = $row['id_usuario'];
            $this->nome_completo = $row['nome_completo'];
            $this->email = $row['email'];
            $this->senha = $row['senha'];
            $this->telefone = $row['telefone'];
            $this->cpf = $row['cpf'];
            $this->tipo = $row['tipo'];
            $this->google_id = $row['google_id'] ?? '';
            
            return true;
        }
        return false;
    }

    // Método para buscar usuário por Google ID
    public function buscarPorGoogleId($google_id) {
        $query = "SELECT id_usuario, nome_completo, email, senha, telefone, cpf, tipo, google_id 
                 FROM " . $this->table_name . " 
                 WHERE google_id = :google_id AND ativo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":google_id", $google_id);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id_usuario = $row['id_usuario'];
            $this->nome_completo = $row['nome_completo'];
            $this->email = $row['email'];
            $this->senha = $row['senha'];
            $this->telefone = $row['telefone'];
            $this->cpf = $row['cpf'];
            $this->tipo = $row['tipo'];
            $this->google_id = $row['google_id'];
            
            return true;
        }
        return false;
    }

    // Método para vincular conta Google a usuário existente
    public function vincularGoogle($id_usuario, $google_id) {
        $query = "UPDATE " . $this->table_name . " 
                 SET google_id = :google_id 
                 WHERE id_usuario = :id_usuario";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":google_id", $google_id);
        $stmt->bindParam(":id_usuario", $id_usuario);

        return $stmt->execute();
    }

    // Método para verificar se Google ID já existe
    public function googleIdExiste($google_id = null) {
        $google_id_to_check = $google_id ?: $this->google_id;
        
        if (empty($google_id_to_check)) {
            return false;
        }
        
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                 WHERE google_id = :google_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":google_id", $google_id_to_check);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    // Método para verificar se usuário existe por Google ID e retornar dados
    public function googleIdExisteComDados($google_id) {
        $query = "SELECT id_usuario, nome_completo, email FROM " . $this->table_name . " 
                 WHERE google_id = :google_id AND ativo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":google_id", $google_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_usuario = $row['id_usuario'];
            $this->nome_completo = $row['nome_completo'];
            $this->email = $row['email'];
            return true;
        }
        return false;
    }

    private function atualizarUltimoLogin() {
        if (!$this->id_usuario) return false;

        $query = "UPDATE " . $this->table_name . " 
                 SET ultimo_login = NOW() 
                 WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_usuario", $this->id_usuario);
        return $stmt->execute();
    }

    public function emailExiste() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                 WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    // Método para buscar usuário por email e retornar dados
    public function emailExisteComDados() {
        $query = "SELECT id_usuario, nome_completo, google_id FROM " . $this->table_name . " 
                 WHERE email = :email AND ativo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_usuario = $row['id_usuario'];
            $this->nome_completo = $row['nome_completo'];
            $this->google_id = $row['google_id'] ?? '';
            return true;
        }
        return false;
    }

    public function cpfExiste() {
        $cpf_limpo = preg_replace('/[^0-9]/', '', $this->cpf);
        
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                 WHERE cpf = :cpf";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cpf", $cpf_limpo);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    // Método para buscar usuário por ID
    public function buscarPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_usuario", $id);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id_usuario = $row['id_usuario'];
            $this->nome_completo = $row['nome_completo'];
            $this->email = $row['email'];
            $this->telefone = $row['telefone'];
            $this->cpf = $row['cpf'];
            $this->tipo = $row['tipo'];
            $this->ativo = $row['ativo'];
            $this->avatar = $row['avatar'];
            $this->data_cadastro = $row['data_cadastro'];
            $this->ultimo_login = $row['ultimo_login'];
            $this->google_id = $row['google_id'] ?? '';
            
            return true;
        }
        return false;
    }

    // Método para atualizar perfil do usuário
    public function atualizar() {
        if (!$this->id_usuario) return false;

        // Remover caracteres especiais
        $cpf_limpo = preg_replace('/[^0-9]/', '', $this->cpf);
        $telefone_limpo = preg_replace('/[^0-9]/', '', $this->telefone);

        $query = "UPDATE " . $this->table_name . " 
                 SET nome_completo=:nome_completo, email=:email, telefone=:telefone,
                     cpf=:cpf
                 WHERE id_usuario = :id_usuario";
        
        $stmt = $this->conn->prepare($query);

        // Limpar dados
        $this->nome_completo = htmlspecialchars(strip_tags($this->nome_completo));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Bind parameters
        $stmt->bindParam(":nome_completo", $this->nome_completo);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telefone", $telefone_limpo);
        $stmt->bindParam(":cpf", $cpf_limpo);
        $stmt->bindParam(":id_usuario", $this->id_usuario);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Método para alterar senha
    public function alterarSenha($nova_senha) {
        if (!$this->id_usuario) return false;

        $query = "UPDATE " . $this->table_name . " 
                 SET senha = :senha 
                 WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        $stmt->bindParam(":senha", $senha_hash);
        $stmt->bindParam(":id_usuario", $this->id_usuario);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Método para listar todos os usuários (para administradores)
    public function listarTodos() {
        $query = "SELECT * FROM " . $this->table_name . " 
                 ORDER BY data_cadastro DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // Método para processar login/cadastro com Google
    public function processarGoogleAuth($google_id, $email, $nome) {
        // Primeiro verificar se já existe usuário com este Google ID
        if ($this->googleIdExisteComDados($google_id)) {
            // Usuário existe - fazer login
            if ($this->loginComGoogle($google_id)) {
                return [
                    'success' => true,
                    'action' => 'login',
                    'message' => 'Login realizado com sucesso!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao fazer login com Google.'
                ];
            }
        }

        // Verificar se email já existe (usuário cadastrado normalmente)
        $this->email = $email;
        if ($this->emailExisteComDados()) {
            // Email existe - vincular Google ID
            if ($this->vincularGoogle($this->id_usuario, $google_id)) {
                // Fazer login após vincular
                if ($this->loginComGoogle($google_id)) {
                    return [
                        'success' => true,
                        'action' => 'link',
                        'message' => 'Conta vinculada com Google com sucesso!'
                    ];
                }
            }
            return [
                'success' => false,
                'message' => 'Erro ao vincular conta Google.'
            ];
        }

        // Cadastro novo com Google
        $this->nome_completo = $nome;
        $this->email = $email;
        $this->google_id = $google_id;
        $this->telefone = 'Não informado';
        $this->cpf = '000.000.000-00';

        if ($this->cadastrarComGoogle()) {
            return [
                'success' => true,
                'action' => 'signup',
                'message' => 'Cadastro realizado com sucesso!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erro ao cadastrar com Google.'
            ];
        }
    }
}
?>