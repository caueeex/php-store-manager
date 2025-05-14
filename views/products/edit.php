<?php
$pageTitle = 'Editar Produto';
include './views/header.php';

$variations = json_decode($product['variations'], true) ?? [];
?>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">Editar Produto</h4>
        
        <form method="POST" action="?page=products&action=edit&id=<?= $product['id'] ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Nome do Produto</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="price" class="form-label">Preço</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" 
                           value="<?= $product['price'] ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Descrição</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="stock" class="form-label">Estoque</label>
                <input type="number" class="form-control" id="stock" name="stock" 
                       value="<?= $stock['quantity'] ?? 0 ?>" min="0" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Variações</label>
                <div id="variations-container">
                    <?php if (!empty($variations['name'])): ?>
                        <?php for ($i = 0; $i < count($variations['name']); $i++): ?>
                            <div class="variation-item mb-2">
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="variations[name][]" 
                                               value="<?= htmlspecialchars($variations['name'][$i]) ?>" 
                                               placeholder="Nome (ex: Tamanho)">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="variations[options][]" 
                                               value="<?= htmlspecialchars($variations['options'][$i]) ?>" 
                                               placeholder="Opções (ex: P,M,G)">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger w-100 remove-variation">Remover</button>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    <?php else: ?>
                        <div class="variation-item mb-2">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="variations[name][]" placeholder="Nome (ex: Tamanho)">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="variations[options][]" placeholder="Opções (ex: P,M,G)">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger w-100 remove-variation">Remover</button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-secondary mt-2" id="add-variation">
                    <i class="bi bi-plus-lg"></i> Adicionar Variação
                </button>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Salvar Alterações
            </button>
            <a href="?page=products" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </form>
    </div>
</div>

<script>
document.getElementById('add-variation').addEventListener('click', function() {
    const container = document.getElementById('variations-container');
    const newItem = document.createElement('div');
    newItem.className = 'variation-item mb-2';
    newItem.innerHTML = `
        <div class="row g-2">
            <div class="col-md-5">
                <input type="text" class="form-control" name="variations[name][]" placeholder="Nome (ex: Tamanho)">
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control" name="variations[options][]" placeholder="Opções (ex: P,M,G)">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger w-100 remove-variation">Remover</button>
            </div>
        </div>
    `;
    container.appendChild(newItem);
    
    // Add event to remove button
    newItem.querySelector('.remove-variation').addEventListener('click', function() {
        container.removeChild(newItem);
    });
});

// Add events to existing remove buttons
document.querySelectorAll('.remove-variation').forEach(button => {
    button.addEventListener('click', function() {
        this.closest('.variation-item').remove();
    });
});
</script>

<?php include './views/footer.php'; ?>