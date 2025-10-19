```php
<?php
session_start();
require_once '../config/database.php'; // Ensure this path is correct

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Get cart items from POST
$cartItems = json_decode($_POST['cartItems'] ?? '[]', true);
if (empty($cartItems)) {
    echo json_encode(['status' => 'error', 'message' => 'Carrinho vazio']);
    exit();
}

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Insert into pedidos
$sql = "INSERT INTO pedidos (id_cliente, status, valor_total, forma_pagamento, data_pedido) 
        VALUES (?, 'pendente', ?, 'pix', NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("id", $usuario_id, $total);
$stmt->execute();
$pedido_id = $conn->insert_id;

// Insert into itens_pedido
foreach ($cartItems as $item) {
    $sql = "INSERT INTO itens_pedido (id_pedido, id_produto, quantidade, preco_unitario) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiid", $pedido_id, $item['id'], $item['quantity'], $item['price']);
    $stmt->execute();
}

// Clear cart
echo json_encode(['status' => 'success', 'pedido_id' => $pedido_id]);
$conn->close();
?>
```