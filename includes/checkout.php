<?php
// checkout.php
require_once '../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Coffee - Finalizar Pagamento</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/header.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

   

        h1 {
            color: #6b4e31;
            text-align: center;
        }

        .checkout-summary {
            margin-bottom: 20px;
        }

        .checkout-summary h2 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .summary-total {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: right;
            padding: 10px 0;
        }

        .payment-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .payment-form label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .payment-form input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .payment-form input:focus {
            outline: none;
            border-color: #6b4e31;
        }

        .payment-button {
            background-color: #6b4e31;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .payment-button:hover {
            background-color: #5a3f28;
        }

        .pix-info {
            display: none;
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .pix-key {
            font-family: monospace;
            background: white;
            padding: 10px;
            border-radius: 4px;
            word-break: break-all;
            margin-bottom: 10px;
        }

        .qr-code {
            text-align: center;
            margin-top: 10px;
        }

        .qr-code img {
            max-width: 200px;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            z-index: 2000;
            display: none;
        }

        @media (max-width: 600px) {
            .container {
                margin: 10px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1>Finalizar Pagamento</h1>

        <!-- Resumo do carrinho -->
        <div class="checkout-summary">
            <h2>Resumo do Pedido</h2>
            <div id="checkout-items"></div>
            <div class="summary-total">
                Total: R$ <span id="checkout-total-amount">0,00</span>
            </div>
        </div>

        <!-- Formulário de pagamento -->
        <form id="payment-form" class="payment-form">
            <label for="customer-name">Nome do Cliente</label>
            <input type="text" id="customer-name" placeholder="Nome Completo" required>
            <label for="customer-email">E-mail do Cliente</label>
            <input type="email" id="customer-email" placeholder="email@exemplo.com" required>
            <label for="customer-cpf">CPF</label>
            <input type="text" id="customer-cpf" placeholder="123.456.789-09" maxlength="14" required>
            <button type="submit" class="payment-button">Gerar PIX via Mercado Pago</button>
        </form>

        <!-- Informações do PIX -->
        <div id="pix-info" class="pix-info">
            <h3>Pague com PIX</h3>
            <p>Copie o código abaixo ou escaneie o QR Code no seu app bancário.</p>
            <div class="pix-key" id="pix-key"></div>
            <div class="qr-code">
               
            </div>
            <p id="pix-status">Aguardando pagamento...</p>
            <button id="check-payment" class="payment-button">Verificar Pagamento</button>
            <p><a href="https://mpago.la/2WaWo1s" id="fallback-link" target="_blank">Pagar via link do Mercado Pago (teste)</a></p>
        </div>
    </div>

    <div class="notification" id="notification"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkoutItemsContainer = document.getElementById('checkout-items');
            const checkoutTotalAmount = document.getElementById('checkout-total-amount');
            const paymentForm = document.getElementById('payment-form');
            const pixInfo = document.getElementById('pix-info');
            const pixKey = document.getElementById('pix-key');
            const qrImage = document.getElementById('qr-image');
            const pixStatus = document.getElementById('pix-status');
            const checkButton = document.getElementById('check-payment');
            const notification = document.getElementById('notification');

            let cartItems = [];
            let paymentId = null;

            // Carregar carrinho
            if (localStorage.getItem('cartItems')) {
                cartItems = JSON.parse(localStorage.getItem('cartItems'));
                updateCheckoutSummary();
            } else {
                checkoutItemsContainer.innerHTML = '<p>Seu carrinho está vazio.</p>';
                checkoutTotalAmount.textContent = '0,00';
                document.querySelector('.payment-button').disabled = true;
            }

            function updateCheckoutSummary() {
                checkoutItemsContainer.innerHTML = '';
                let total = 0;
                cartItems.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;
                    const summaryItem = document.createElement('div');
                    summaryItem.classList.add('summary-item');
                    summaryItem.innerHTML = `
                        <span>${item.name} (x${item.quantity})</span>
                        <span>R$ ${(itemTotal).toFixed(2).replace('.', ',')}</span>
                    `;
                    checkoutItemsContainer.appendChild(summaryItem);
                });
                checkoutTotalAmount.textContent = total.toFixed(2).replace('.', ',');
            }

            // Formatar CPF
            const cpfInput = document.getElementById('customer-cpf');
            cpfInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
                value = value.replace(/(\d{3})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3-$4');
                e.target.value = value;
            });

            // Enviar formulário
            paymentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const customerName = document.getElementById('customer-name').value;
                const customerEmail = document.getElementById('customer-email').value;
                const customerCpf = document.getElementById('customer-cpf').value.replace(/\D/g, '');
                const total = parseFloat(checkoutTotalAmount.textContent.replace(',', '.'));

                if (!customerName || !customerEmail || !customerCpf || customerCpf.length !== 11) {
                    showNotification('Preencha todos os campos corretamente.');
                    return;
                }

                const paymentButton = document.querySelector('.payment-button');
                paymentButton.disabled = true;
                paymentButton.textContent = 'Gerando PIX...';

                const items = cartItems.map(item => ({
                    title: item.name,
                    unit_price: item.price,
                    quantity: item.quantity
                }));

                fetch('process_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        customerName,
                        customerEmail,
                        customerCpf,
                        total,
                        items
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        paymentId = data.paymentId;
                        pixKey.textContent = data.pixCode;
                        qrImage.src = data.qrCode;
                        pixInfo.style.display = 'block';
                        paymentForm.style.display = 'none';
                        showNotification('PIX gerado! Escaneie ou copie o código.');
                    } else {
                        // Mostrar link estático como fallback
                        pixKey.textContent = 'https://mpago.la/2WaWo1s';
                        pixInfo.style.display = 'block';
                        paymentForm.style.display = 'none';
                        showNotification('Erro ao gerar PIX. Use o link alternativo: https://mpago.la/2WaWo1s');
                    }
                    paymentButton.disabled = false;
                    paymentButton.textContent = 'Gerar PIX via Mercado Pago';
                })
                .catch(error => {
                    showNotification('Erro ao processar. Use o link alternativo.');
                    console.error('Erro:', error);
                    pixKey.textContent = 'https://mpago.la/2WaWo1s';
                    pixInfo.style.display = 'block';
                    paymentForm.style.display = 'none';
                    paymentButton.disabled = false;
                    paymentButton.textContent = 'Gerar PIX via Mercado Pago';
                });
            });

            // Verificar status
            checkButton.addEventListener('click', function() {
                if (!paymentId) {
                    showNotification('Nenhum pagamento gerado.');
                    return;
                }
                fetch(`process_payment.php?check=1&id=${paymentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.status === 'approved') {
                        pixStatus.textContent = 'Pagamento confirmado!';
                        localStorage.removeItem('cartItems');
                        showNotification('Pagamento realizado com sucesso!');
                        setTimeout(() => window.location.href = 'pedidos.php', 2000);
                    } else {
                        pixStatus.textContent = 'Pagamento pendente...';
                    }
                })
                .catch(error => {
                    console.error('Erro ao verificar:', error);
                    showNotification('Erro ao verificar pagamento.');
                });
            });

            function showNotification(message) {
                notification.textContent = message;
                notification.style.display = 'block';
                setTimeout(() => notification.style.display = 'none', 3000);
            }
        });
    </script>
</body>
</html>