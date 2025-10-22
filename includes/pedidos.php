<?php
include '../includes/header.php';

require_once '../config/database.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM pedidos WHERE id_cliente = :id_cliente ORDER BY data_pedido DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(":id_cliente", $_SESSION['usuario_id']);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pedidos - Dev Coffee</title>
    <link rel="stylesheet" href="../css/pedidos.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <h1>Meus Pedidos</h1>
        <?php if (count($pedidos) === 0): ?>
            <p>Você ainda não fez nenhum pedido.</p>
        <?php else: ?>
            <div class="orders-table">
                <?php foreach ($pedidos as $pedido): ?>
                    <?php
                    $itens = json_decode($pedido['itens'], true);
                    $statusText = match ($pedido['status']) {
                        'pendente' => 'Aguardando Pagamento',
                        'preparando' => 'Em preparo',
                        'saiu_entrega' => 'Saiu para entrega',
                        'entregue' => 'Entregue',
                        default => ucfirst($pedido['status'])
                    };
                    $statusIcon = match ($pedido['status']) {
                        'pendente' => '🟡',
                        'preparando' => '🟠',
                        'saiu_entrega' => '🟣',
                        'entregue' => '🟢',
                        default => '⚪'
                    };
                    ?>
                    <div class="order-row">
                        <div class="col-product">
                            <h3>Pedido #<?php echo $pedido['id_pedido']; ?></h3>
                            <ul>
                                <?php foreach ($itens as $item): ?>
                                    <li><?php echo htmlspecialchars($item['title']) . " (x" . $item['quantity'] . ")"; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="col-total">
                            <strong>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></strong>
                        </div>
                        <div class="col-status">
                            <div class="status">
                                <span class="status-icon"><?php echo $statusIcon; ?></span>
                                <span class="status-text"><?php echo $statusText; ?></span>
                            </div>
                        </div>
                        <div class="col-action">
                            <?php if ($pedido['status'] === 'pendente'): ?>
                                <button class="btn-cancelar" data-id="<?php echo $pedido['id_pedido']; ?>">
                                    Cancelar Pedido
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="../js/pedidos.js"></script>
    <script>
        document.querySelectorAll('.btn-cancelar').forEach(btn => {
            btn.addEventListener('click', () => {
                const id_pedido = btn.getAttribute('data-id');

                Swal.fire({
                    title: 'Cancelar pedido?',
                    text: "Tem certeza que deseja cancelar este pedido? Esta ação não poderá ser desfeita.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, cancelar!',
                    cancelButtonText: 'Não, manter'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('cancelar_pedido.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id_pedido })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Cancelado!',
                                        text: data.message,
                                        icon: 'success',
                                        confirmButtonColor: '#6b4e31'
                                    }).then(() => location.reload());
                                } else {
                                    Swal.fire({
                                        title: 'Ops!',
                                        text: data.message,
                                        icon: 'info',
                                        confirmButtonColor: '#6b4e31'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Erro:', error);
                                Swal.fire({
                                    title: 'Erro!',
                                    text: 'Ocorreu um erro ao cancelar o pedido.',
                                    icon: 'error',
                                    confirmButtonColor: '#6b4e31'
                                });
                            });
                    }
                });
            });
        });
    </script>

</body>

</html>