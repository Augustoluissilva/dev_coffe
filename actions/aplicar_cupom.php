<?php
session_start();
include_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cupom_id = $_POST['cupom_id'] ?? null;
    $cupom_codigo = $_POST['cupom_codigo'] ?? null;
    $desconto_percentual = $_POST['desconto_percentual'] ?? null;
    $desconto_fixo = $_POST['desconto_fixo'] ?? null;
    
    if ($cupom_id && $cupom_codigo) {
        // Verificar se o cupom ainda está válido
        $database = new Database();
        $conn = $database->getConnection();
        
        if ($conn) {
            try {
                $query = "SELECT * FROM cupons WHERE id_cupom = :id AND ativo = 1 AND (validade IS NULL OR validade >= CURDATE())";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':id', $cupom_id);
                $stmt->execute();
                $cupom_valido = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($cupom_valido) {
                    $_SESSION['cupom_aplicado'] = [
                        'id_cupom' => $cupom_id,
                        'codigo' => $cupom_codigo,
                        'desconto_percentual' => $desconto_percentual ? (float)$desconto_percentual : null,
                        'desconto_fixo' => $desconto_fixo ? (float)$desconto_fixo : null,
                        'descricao' => $cupom_valido['descricao']
                    ];
                    
                    $_SESSION['mensagem_sucesso'] = [
                        'tipo' => 'success',
                        'texto' => '🎉 Cupom aplicado com sucesso! Agora é só escolher seus produtos.'
                    ];
                } else {
                    $_SESSION['mensagem_sucesso'] = [
                        'tipo' => 'error',
                        'texto' => '❌ Cupom expirado ou inválido.'
                    ];
                }
            } catch (PDOException $e) {
                $_SESSION['mensagem_sucesso'] = [
                    'tipo' => 'error',
                    'texto' => 'Erro ao validar cupom: ' . $e->getMessage()
                ];
            }
        }
    }
    
    header('Location: ../pages/menu.php');
    exit();
} else {
    header('Location: ../pages/cupons.php');
    exit();
}
?>