<?php include '../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pedidos - Dev Coffee</title>
    <link rel="stylesheet" href="../css/pedidos.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="page-title">Meus Pedidos</h1>
        
        <div class="orders-table">
            <div class="table-header">
                <div class="col-product">Produto</div>
                <div class="col-order-number">Número do Pedido</div>
                <div class="col-total">Total</div>
                <div class="col-status">Status</div>
            </div>

            <div class="order-row">
                <div class="col-product">
                    <div class="product-info">
                        <img src="images/cafe-preto.png" alt="Café Preto Sem Açúcar" class="product-image">
                        <div class="product-details">
                            <h3 class="product-name">Café Preto Sem Açúcar 200ml</h3>
                            <p class="product-description">Café especial torra média</p>
                        </div>
                    </div>
                </div>
                <div class="col-order-number">#1111111</div>
                <div class="col-total">R$ 15,00</div>
                <div class="col-status">
                    <div class="status delivered">
                        <span class="status-icon">🟢</span>
                        <span class="status-text">Entregue</span>
                    </div>
                    <button class="btn-buy-again">Comprar novamente</button>
                </div>
            </div>

            <div class="order-row">
                <div class="col-product">
                    <div class="product-info">
                        <img src="images/donuts.png" alt="Donuts" class="product-image">
                        <div class="product-details">
                            <h3 class="product-name">Donuts 1 Unidade</h3>
                            <p class="product-description">Donuts com cobertura de chocolate</p>
                        </div>
                    </div>
                </div>
                <div class="col-order-number">#1111112</div>
                <div class="col-total">R$ 10,00</div>
                <div class="col-status">
                    <div class="status delivered">
                        <span class="status-icon">🟢</span>
                        <span class="status-text">Entregue</span>
                    </div>
                    <button class="btn-buy-again">Comprar novamente</button>
                </div>
            </div>

            <div class="order-row">
                <div class="col-product">
                    <div class="product-info">
                        <img src="images/cappuccino.png" alt="Cappuccino" class="product-image">
                        <div class="product-details">
                            <h3 class="product-name">Cappuccino 300ml</h3>
                            <p class="product-description">Cappuccino cremoso com canela</p>
                        </div>
                    </div>
                </div>
                <div class="col-order-number">#1111113</div>
                <div class="col-total">R$ 18,00</div>
                <div class="col-status">
                    <div class="status preparing" id="status-preparing">
                        <span class="status-icon">🟡</span>
                        <span class="status-text">Em preparo</span>
                    </div>
                </div>
            </div>

            <div class="order-row">
                <div class="col-product">
                    <div class="product-info">
                        <img src="images/croissant.png" alt="Croissant" class="product-image">
                        <div class="product-details">
                            <h3 class="product-name">Croissant 1 Unidade</h3>
                            <p class="product-description">Croissant de manteiga</p>
                        </div>
                    </div>
                </div>
                <div class="col-order-number">#1111114</div>
                <div class="col-total">R$ 12,00</div>
                <div class="col-status">
                    <div class="status delivery" id="status-delivery">
                        <span class="status-icon">🟠</span>
                        <span class="status-text">Motoboy a caminho</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/pedidos.js"></script>
</body>
</html>