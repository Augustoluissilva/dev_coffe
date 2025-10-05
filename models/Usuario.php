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

    public function __construct($db) {
        $this->conn = $db;
    }

    public function cadastrar() {
        // Remover caracteres especiais do CPF
        $cpf_limpo = preg_replace('/[^0-9]/', '', $this->cpf);
        $telefone_limpo = preg_replace('/[^0-9]/', '', $this->telefone);
        $cep_limpo = preg_replace('/[^0-9]/', '', $this->cep);

        $query = "INSERT INTO " . $this->table_name . " 
                 SET nome=:nome, email=:email, senha=:senha, telefone=:telefone,
                     cpf=:cpf, data_nascimento=:data_nascimento, endereco=:endereco,
                     numero=:numero, complemento=:complemento, bairro=:bairro,
                     cidade=:cidade, estado=:estado, cep=:cep";
        
        $stmt = $this->conn->prepare($query);

        // Limpar dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->endereco = htmlspecialchars(strip_tags($this->endereco));
        $this->numero = htmlspecialchars(strip_tags($this->numero));
        $this->complemento = htmlspecialchars(strip_tags($this->complemento));
        $this->bairro = htmlspecialchars(strip_tags($this->bairro));
        $this->cidade = htmlspecialchars(strip_tags($this->cidade));
        $this->estado = htmlspecialchars(strip_tags($this->estado));

        // Hash da senha
        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);

        // Bind parameters
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":senha", $this->senha);
        $stmt->bindParam(":telefone", $telefone_limpo);
        $stmt->bindParam(":cpf", $cpf_limpo);
        $stmt->bindParam(":data_nascimento", $this->data_nascimento);
        $stmt->bindParam(":endereco", $this->endereco);
        $stmt->bindParam(":numero", $this->numero);
        $stmt->bindParam(":complemento", $this->complemento);
        $stmt->bindParam(":bairro", $this->bairro);
        $stmt->bindParam(":cidade", $this->cidade);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":cep", $cep_limpo);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function login() {
        $query = "SELECT id, nome, email, senha, tipo FROM " . $this->table_name . " 
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
                
                // Atualizar último login
                $this->atualizarUltimoLogin();
                
                return true;
            }
        }
        return false;
    }

    private function atualizarUltimoLogin() {
        $query = "UPDATE " . $this->table_name . " SET ultimo_login = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
    }

    public function emailExiste() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function cpfExiste() {
        $cpf_limpo = preg_replace('/[^0-9]/', '', $this->cpf);
        $query = "SELECT id FROM " . $this->table_name . " WHERE cpf = :cpf";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cpf", $cpf_limpo);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>