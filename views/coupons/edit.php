<?php
$pageTitle = 'Editar Cupom';
include './views/header.php';
?>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">Editar Cupom de Desconto</h4>
        
        <form method="POST" action="?page=coupons&action=edit&id=<?= $coupon['id'] ?>">
            <div class="mb-3">
                <label for="code" class="form-label">Código do Cupom</label>
                <input type="text" class="form-control" id="code" name="code" 
                       value="<?= $coupon['code'] ?>" required>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="discount_type" class="form-label">Tipo de Desconto</label>
                    <select class="form-select" id="discount_type" name="discount_type" required>
                        <option value="fixed" <?= $coupon['discount_type'] === 'fixed' ? 'selected' : '' ?>>Valor Fixo (R$)</option>
                        <option value="percentage" <?= $coupon['discount_type'] === 'percentage' ? 'selected' : '' ?>>Percentual (%)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="discount_value" class="form-label">Valor do Desconto</label>
                    <input type="number" step="0.01" class="form-control" id="discount_value" 
                           name="discount_value" value="<?= $coupon['discount_value'] ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="min_order_value" class="form-label">Valor Mínimo do Pedido (opcional)</label>
                <input type="number" step="0.01" class="form-control" id="min_order_value" 
                       name="min_order_value" value="<?= $coupon['min_order_value'] ?>">
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_date" class="form-label">Data de Início</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="<?= $coupon['start_date'] ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="end_date" class="form-label">Data de Término</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="<?= $coupon['end_date'] ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="max_uses" class="form-label">Número Máximo de Usos (opcional)</label>
                <input type="number" class="form-control" id="max_uses" name="max_uses" 
                       value="<?= $coupon['max_uses'] ?>" min="1">
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Salvar Alterações
            </button>
            <a href="?page=coupons" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Cancelar
            </a>
        </form>
    </div>
</div>

<script>
document.getElementById('discount_type').addEventListener('change', function() {
    const discountValue = document.getElementById('discount_value');
    if (this.value === 'percentage') {
        discountValue.setAttribute('max', '100');
        discountValue.setAttribute('placeholder', '0-100');
    } else {
        discountValue.removeAttribute('max');
        discountValue.removeAttribute('placeholder');
    }
});
</script>

<?php include './views/footer.php'; ?>