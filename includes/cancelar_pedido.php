<?php
require_once '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Verifica se veio o ID
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id_pedido = $data['id_pedido'] ?? null;
    $id_cliente = $_SESSION['usuario_id'];

    if (!$id_pedido) {
        echo json_encode(['success' => false, 'message' => 'Pedido inválido.']);
        exit();
    }

    // Verifica se o pedido pertence ao usuário e está pendente
    $checkQuery = "SELECT status FROM pedidos WHERE id_pedido = :id_pedido AND id_cliente = :id_cliente";
    $stmt = $db->prepare($checkQuery);
    $stmt->bindParam(":id_pedido", $id_pedido);
    $stmt->bindParam(":id_cliente", $id_cliente);
    $stmt->execute();
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        echo json_encode(['success' => false, 'message' => 'Pedido não encontrado.']);
        exit();
    }

    if ($pedido['status'] !== 'pendente') {
        echo json_encode(['success' => false, 'message' => 'Este pedido não pode mais ser cancelado.']);
        exit();
    }

    // Atualiza o status para cancelado
    $update = "UPDATE pedidos SET status = 'cancelado' WHERE id_pedido = :id_pedido";
    $stmt = $db->prepare($update);
    $stmt->bindParam(":id_pedido", $id_pedido);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Pedido cancelado com sucesso.']);
    exit();
}
?>