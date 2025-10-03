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
    public $ativo;
    public $data_cadastro;
    public $ultimo_login;
    public $avatar;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Cadastrar usuário
    public function cadastrar() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET nome=:nome, email=:email, senha=:senha, telefone=:telefone,
                     cpf=:cpf, data_nascimento=:data_nascimento, endereco=:endereco,
                     numero=:numero, complemento=:complemento, bairro=:bairro,
                     cidade=:cidade, estado=:estado, cep=:cep";
        
        $stmt = $this->conn->prepare($query);

        // Limpar e validar dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        $this->cpf = htmlspecialchars(strip_tags($this->cpf));
        $this->endereco = htmlspecialchars(strip_tags($this->endereco));
        $this->numero = htmlspecialchars(strip_tags($this->numero));
        $this->complemento = htmlspecialchars(strip_tags($this->complemento));
        $this->bairro = htmlspecialchars(strip_tags($this->bairro));
        $this->cidade = htmlspecialchars(strip_tags($this->cidade));
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->cep = htmlspecialchars(strip_tags($this->cep));

        // Hash da senha
        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);

        // Bind parameters
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":senha", $this->senha);
        $stmt->bindParam(":telefone", $this->telefone);
        $stmt->bindParam(":cpf", $this->cpf);
        $stmt->bindParam(":data_nascimento", $this->data_nascimento);
        $stmt->bindParam(":endereco", $this->endereco);
        $stmt->bindParam(":numero", $this->numero);
        $stmt->bindParam(":complemento", $this->complemento);
        $stmt->bindParam(":bairro", $this->bairro);
        $stmt->bindParam(":cidade", $this->cidade);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":cep", $this->cep);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Login do usuário
    public function login() {
        $query = "SELECT id, nome, email, senha, tipo, avatar FROM " . $this->table_name . " 
                 WHERE email = :email AND ativo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar senha
            if(password_verify($this->senha, $row['senha'])) {
                $this->id = $row['id'];
                $this->nome = $row['nome'];
                $this->tipo = $row['tipo'];
                $this->avatar = $row['avatar'];
                
                // Atualizar último login
                $this->atualizarUltimoLogin();
                
                return true;
            }
        }
        return false;
    }

    // Atualizar último login
    private function atualizarUltimoLogin() {
        $query = "UPDATE " . $this->table_name . " SET ultimo_login = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
    }

    // Verificar se email existe
    public function emailExiste() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Verificar se CPF existe
    public function cpfExiste() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE cpf = :cpf";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cpf", $this->cpf);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Buscar usuário por ID
    public function buscarPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->nome = $row['nome'];
            $this->email = $row['email'];
            $this->telefone = $row['telefone'];
            $this->cpf = $row['cpf'];
            $this->data_nascimento = $row['data_nascimento'];
            $this->endereco = $row['endereco'];
            $this->numero = $row['numero'];
            $this->complemento = $row['complemento'];
            $this->bairro = $row['bairro'];
            $this->cidade = $row['cidade'];
            $this->estado = $row['estado'];
            $this->cep = $row['cep'];
            $this->tipo = $row['tipo'];
            $this->avatar = $row['avatar'];
            return true;
        }
        return false;
    }
}
?>