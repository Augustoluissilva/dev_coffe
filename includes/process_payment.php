<?php
require_once '../config/database.php';
session_start();

$database = new Database();
$db = $database->getConnection();

// ============================
// SIMULAÇÃO DE PAGAMENTO PIX
// ============================

// Gerar pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $id_cliente = $_SESSION['usuario_id'] ?? null;
    $itens = json_encode($data['items']);
    $valor_total = $data['total'] ?? 0;
    $forma_pagamento = 'pix';
    $endereco_entrega = 'Retirada no balcão';
    $taxa_entrega = 0.00;

    if (!$id_cliente) {
        echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
        exit;
    }

    $query = "INSERT INTO pedidos 
        (id_cliente, itens, status, valor_total, taxa_entrega, forma_pagamento, endereco_entrega) 
        VALUES (:id_cliente, :itens, 'pendente', :valor_total, :taxa_entrega, :forma_pagamento, :endereco_entrega)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id_cliente", $id_cliente);
    $stmt->bindParam(":itens", $itens);
    $stmt->bindParam(":valor_total", $valor_total);
    $stmt->bindParam(":taxa_entrega", $taxa_entrega);
    $stmt->bindParam(":forma_pagamento", $forma_pagamento);
    $stmt->bindParam(":endereco_entrega", $endereco_entrega);

    if ($stmt->execute()) {
        $id_pedido = $db->lastInsertId();

        echo json_encode([
            'success' => true,
            'paymentId' => $id_pedido,
            'pixCode' => '000201BR.GOV.BCB.PIX0114PIXFAKEDEVCOFFEE' . rand(1000, 9999),
            'qrCode' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=Pagamento+Simulado+DevCoffee'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao criar pedido.']);
    }
    exit;
}

// ============================
// SIMULAÇÃO DE VERIFICAÇÃO PIX
// ============================
if (isset($_GET['check']) && isset($_GET['id'])) {
    $id_pedido = $_GET['id'];

    // Atualiza o pedido como "entregue"
    $query = "UPDATE pedidos SET status='entregue' WHERE id_pedido=:id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $id_pedido);
    $stmt->execute();

    echo json_encode(['success' => true, 'status' => 'approved']);
    exit;
}
?>