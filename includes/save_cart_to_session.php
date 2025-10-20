<?php
session_start();
$input = json_decode(file_get_contents('php://input'), true);
$_SESSION['cart'] = $input['cart'] ?? [];
echo json_encode(['success' => true]);
?>