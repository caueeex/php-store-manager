<?php
$pageTitle = 'Pedidos';
include './views/header.php';

// Mapeamento de status para textos em português
$statusLabels = [
    'pending' => 'Pendente',
    'processing' => 'Em Processamento',
    'shipped' => 'Enviado',
    'delivered' => 'Entregue',
    'cancelled' => 'Cancelado'
];

// Mapeamento de status para classes de cores
$statusColors = [
    'pending' => 'bg-warning',
    'processing' => 'bg-info',
    'shipped' => 'bg-primary',
    'delivered' => 'bg-success',
    'cancelled' => 'bg-danger'
];

// Mapeamento de status para ícones
$statusIcons = [
    'pending' => 'bi-hourglass-split',
    'processing' => 'bi-gear',
    'shipped' => 'bi-truck',
    'delivered' => 'bi-check-circle',
    'cancelled' => 'bi-x-circle'
];
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-cart-check"></i> Lista de Pedidos
            <?php if (isset($_GET['status'])): ?>
                <small class="text-muted">
                    - <?= $statusLabels[$_GET['status']] ?>
                </small>
            <?php endif; ?>
        </h2>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-funnel"></i> Filtrar por Status
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item <?= !isset($_GET['status']) ? 'active' : '' ?>" 
                       href="?page=orders">
                        <i class="bi bi-grid"></i> Todos
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <?php foreach ($statusLabels as $value => $label): ?>
                <li>
                    <a class="dropdown-item <?= (isset($_GET['status']) && $_GET['status'] === $value) ? 'active' : '' ?>" 
                       href="?page=orders&status=<?= $value ?>">
                        <i class="bi <?= $statusIcons[$value] ?>"></i> <?= $label ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <?php if (empty($orders)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Nenhum pedido encontrado.
    </div>
    <?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="fw-bold">#<?= $order['id'] ?></td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium"><?= $order['customer_name'] ?></span>
                                    <small class="text-muted"><?= $order['customer_email'] ?></small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span><?= date('d/m/Y', strtotime($order['created_at'])) ?></span>
                                    <small class="text-muted"><?= date('H:i', strtotime($order['created_at'])) ?></small>
                                </div>
                            </td>
                            <td class="fw-bold"><?= formatPrice($order['total']) ?></td>
                            <td>
                                <span class="badge <?= $statusColors[$order['status']] ?>">
                                    <i class="bi <?= $statusIcons[$order['status']] ?>"></i>
                                    <?= $statusLabels[$order['status']] ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="?page=orders&action=detail&id=<?= $order['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Ver Detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                    
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <?php foreach ($statusLabels as $value => $label): ?>
                                            <?php if ($order['status'] !== $value): ?>
                                            <li>
                                                <a class="dropdown-item" 
                                                   href="?page=orders&action=update_status&id=<?= $order['id'] ?>&status=<?= $value ?>">
                                                    <i class="bi <?= $statusIcons[$value] ?>"></i> 
                                                    <?= $label ?>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include './views/footer.php'; ?>