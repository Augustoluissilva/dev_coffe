<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'cart_count' => 0];

if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = $data['product_id'] ?? null;
    $quantity = $data['quantity'] ?? 1;

    if ($product_id && $quantity > 0) {
        // Check stock
        $sql = "SELECT estoque FROM produtos WHERE id_produto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product && $product['estoque'] >= $quantity) {
            // Check if item exists in cart
            $sql = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $usuario_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Update quantity
                $row = $result->fetch_assoc();
                $new_quantity = $row['quantity'] + $quantity;
                $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iii", $new_quantity, $usuario_id, $product_id);
            } else {
                // Insert new item
                $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iii", $usuario_id, $product_id, $quantity);
            }

            if ($stmt->execute()) {
                // Get cart count
                $sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $usuario_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $cart_count = $result->fetch_assoc()['total'] ?? 0;
                $response = ['success' => true, 'cart_count' => $cart_count];
            }
        }
        $stmt->close();
    }
} else {
    $response['success'] = true; // Guest cart handled client-side
}

echo json_encode($response);
$conn->close();
?>