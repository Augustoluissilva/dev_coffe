<?php
// process_payment.php - Integração PIX Mercado Pago
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Credenciais Mercado Pago
$accessToken = 'SEU_ACCESS_TOKEN_AQUI'; // Substitua pelo Access Token de teste ou produção
$apiUrl = 'https://api.mercadopago.com/v1/payments';
$fallbackLink = 'https://mpago.la/2WaWo1s'; // Link fornecido para testes

if (empty($accessToken)) {
    echo json_encode(['success' => false, 'message' => 'Access Token não configurado.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$check = $_GET['check'] ?? false;
$paymentId = $_GET['id'] ?? '';

if ($check && $paymentId) {
    // Verificar status do pagamento
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$apiUrl/$paymentId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Testes locais
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);
    echo json_encode([
        'success' => $httpCode === 200,
        'status' => $data['status'] ?? 'pending'
    ]);
    exit;
}

$customerName = $input['customerName'] ?? '';
$customerEmail = $input['customerEmail'] ?? '';
$customerCpf = $input['customerCpf'] ?? '';
$total = $input['total'] ?? 0;
$items = $input['items'] ?? [];

if (empty($customerName) || empty($customerEmail) || empty($customerCpf) || $total <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    exit;
}

// Validar CPF
if (!preg_match('/^\d{11}$/', $customerCpf)) {
    echo json_encode(['success' => false, 'message' => 'CPF inválido.']);
    exit;
}

// Criar pagamento PIX
$requestBody = [
    'transaction_amount' => $total,
    'description' => 'Pedido Dev Coffee',
    'payment_method_id' => 'pix',
    'payer' => [
        'email' => $customerEmail,
        'first_name' => explode(' ', $customerName)[0],
        'last_name' => implode(' ', array_slice(explode(' ', $customerName), 1)),
        'identification' => [
            'type' => 'CPF',
            'number' => $customerCpf
        ]
    ],
    'notification_url' => 'https://seusite.com/webhook-mercadopago', // Use ngrok para testes
    'external_reference' => uniqid('pedido_'),
    'items' => $items
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Testes locais
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    error_log("cURL Error: $curlError");
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com Mercado Pago.']);
    exit;
}

$data = json_decode($response, true);

if ($httpCode === 201 && isset($data['id'])) {
    $_SESSION['payment_id'] = $data['id'];
    $pixData = $data['point_of_interaction']['transaction_data'] ?? [];
    echo json_encode([
        'success' => true,
        'paymentId' => $data['id'],
        'pixCode' => $pixData['qr_code'] ?? $fallbackLink, // Usa link fornecido como fallback
        'qrCode' => $pixData['qr_code_base64'] ? 'data:image/png;base64,' . $pixData['qr_code_base64'] : ''
    ]);
} else {
    $errorMessage = $data['message'] ?? 'Erro HTTP ' . $httpCode;
    error_log("Mercado Pago Error: $errorMessage - Response: " . $response);
    echo json_encode(['success' => false, 'message' => $errorMessage]);
}
?>