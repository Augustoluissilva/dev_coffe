<?php
// webhook-mercadopago.php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$input = json_decode(file_get_contents('php://input'), true);
$type = $_GET['type'] ?? '';
$paymentId = $input['data']['id'] ?? '';

if ($type === 'payment' && $paymentId) {
    // Aqui você pode consultar a API para detalhes ou atualizar o banco
    $_SESSION['payment_status'] = 'updated'; // Exemplo
    error_log("Notificação recebida para pagamento: $paymentId");
    // Exemplo: $db->query("UPDATE orders SET status = 'approved' WHERE payment_id = '$paymentId'");
}

http_response_code(200);
echo json_encode(['status' => 'received']);
?>