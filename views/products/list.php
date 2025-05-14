<?php
$pageTitle = 'Produtos';
include './views/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Lista de Produtos</h2>
    <a href="?page=products&action=create" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Adicionar Produto
    </a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <?php $stock = $this->stockModel->getByProductId($product['id']); ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= formatPrice($product['price']) ?></td>
                        <td><?= $stock['quantity'] ?? 0 ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="?page=products&action=edit&id=<?= $product['id'] ?>" 
                                   class="btn btn-sm btn-warning"
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                <a href="?page=products&action=delete&id=<?= $product['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   title="Excluir"
                                   onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                                
                                <form method="POST" action="?page=cart&action=add" class="d-flex">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="number" name="quantity" value="1" min="1" max="<?= $stock['quantity'] ?? 1 ?>" 
                                           class="form-control form-control-sm" style="width: 60px;">
                                    <button type="submit" class="btn btn-sm btn-success ms-1" title="Adicionar ao carrinho">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include './views/footer.php'; ?>