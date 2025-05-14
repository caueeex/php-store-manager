<?php
$pageTitle = 'Pedido Confirmado';
include './views/header.php';

$orderId = $_GET['id'] ?? $_SESSION['order_success'] ?? null;
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="card-title">Pedido Confirmado!</h2>
                    <p class="card-text">Seu pedido foi recebido e está sendo processado.</p>
                    
                    <div class="alert alert-info my-4">
                        <h5>Número do Pedido: #<?= $orderId ?></h5>
                        <p>Enviamos os detalhes para o seu e-mail.</p>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="?page=orders&action=detail&id=<?= $orderId ?>" class="btn btn-primary">
                            <i class="bi bi-receipt"></i> Ver Detalhes
                        </a>
                        <a href="?page=products" class="btn btn-outline-secondary">
                            <i class="bi bi-bag"></i> Continuar Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
unset($_SESSION['order_success']);
include './views/footer.php'; 
?>