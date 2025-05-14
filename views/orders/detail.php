<?php
$pageTitle = 'Detalhes do Pedido';
include './views/header.php';

$statusBadgeClass = [
    'pending' => 'bg-warning',
    'processing' => 'bg-info',
    'shipped' => 'bg-primary',
    'delivered' => 'bg-success',
    'cancelled' => 'bg-danger'
];
?>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Pedido #<?= $order['id'] ?></h4>
            <span class="badge <?= $statusBadgeClass[$order['status']] ?>">
                <?= ucfirst($order['status']) ?>
            </span>
        </div>
        
        <hr>
        
        <div class="row">
            <div class="col-md-6">
                <h5>Informações do Cliente</h5>
                <p>
                    <strong>Nome:</strong> <?= $order['customer_name'] ?><br>
                    <strong>E-mail:</strong> <?= $order['customer_email'] ?><br>
                    <strong>Telefone:</strong> <?= $order['customer_phone'] ?>
                </p>
            </div>
            
            <div class="col-md-6">
                <h5>Endereço de Entrega</h5>
                <p>
                    <?= $order['customer_address'] ?><br>
                    CEP: <?= $order['customer_zipcode'] ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Itens do Pedido</h5>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço Unitário</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td><?= $item['name'] ?></td>
                        <td><?= formatPrice($item['price']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= formatPrice($item['price'] * $item['quantity']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 offset-md-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Subtotal</strong>
                        <span><?= formatPrice($order['subtotal']) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Frete</strong>
                        <span><?= formatPrice($order['shipping']) ?></span>
                    </li>
                    
                    <?php if ($order['discount'] > 0): ?>
                    <li class="list-group-item d-flex justify-content-between bg-light">
                        <strong>Desconto</strong>
                        <span class="text-danger">-<?= formatPrice($order['discount']) ?></span>
                    </li>
                    <?php endif; ?>
                    
                    <li class="list-group-item d-flex justify-content-between bg-primary text-white">
                        <strong>Total</strong>
                        <strong><?= formatPrice($order['total']) ?></strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="?page=orders" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Voltar para Pedidos
    </a>
</div>

<?php include './views/footer.php'; ?>