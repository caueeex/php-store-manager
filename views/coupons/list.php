<?php
$pageTitle = 'Cupons de Desconto';
include './views/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Cupons de Desconto</h2>
    <a href="?page=coupons&action=create" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Adicionar Cupom
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Validade</th>
                        <th>Usos</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($coupons as $coupon): ?>
                    <tr>
                        <td><?= $coupon['code'] ?></td>
                        <td><?= $coupon['discount_type'] === 'percentage' ? 'Percentual' : 'Fixo' ?></td>
                        <td>
                            <?= $coupon['discount_type'] === 'percentage' 
                                ? $coupon['discount_value'] . '%' 
                                : formatPrice($coupon['discount_value']) ?>
                        </td>
                        <td>
                            <?= date('d/m/Y', strtotime($coupon['start_date'])) ?> - 
                            <?= date('d/m/Y', strtotime($coupon['end_date'])) ?>
                        </td>
                        <td>
                            <?= $coupon['use_count'] ?>
                            <?= $coupon['max_uses'] ? "/{$coupon['max_uses']}" : '' ?>
                        </td>
                        <td>
                            <a href="?page=coupons&action=edit&id=<?= $coupon['id'] ?>" 
                               class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="?page=coupons&action=delete&id=<?= $coupon['id'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Tem certeza que deseja excluir este cupom?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include './views/footer.php'; ?>