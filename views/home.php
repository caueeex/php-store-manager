<?php include 'header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Bem-vindo ao <?= SITE_NAME ?></h1>
        <div class="card">
            <div class="card-body">
                <p class="lead">Sistema de gerenciamento de pedidos, produtos e estoque.</p>
                
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Produtos</h5>
                                <p class="card-text">Gerencie seu catálogo de produtos</p>
                                <a href="?page=products" class="btn btn-light">Acessar</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Pedidos</h5>
                                <p class="card-text">Visualize e gerencie pedidos</p>
                                <a href="?page=orders" class="btn btn-light">Acessar</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Cupons</h5>
                                <p class="card-text">Crie e gerencie cupons de desconto</p>
                                <a href="?page=coupons" class="btn btn-light">Acessar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>