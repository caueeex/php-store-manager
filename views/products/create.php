<?php
$pageTitle = 'Adicionar Produto';
include './views/header.php';
?>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">Adicionar Novo Produto</h4>
        
        <form method="POST" action="?page=products&action=create">
            <div class="mb-3">
                <label for="name" class="form-label">Nome do Produto</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            
            <div class="mb-3">
                <label for="price" class="form-label">Preço</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Descrição</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="mb-3">
                <label for="stock" class="form-label">Estoque Inicial</label>
                <input type="number" class="form-control" id="stock" name="stock" min="0" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Variações</label>
                <div id="variations-container">
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
                </div>
                <button type="button" class="btn btn-secondary mt-2" id="add-variation">
                    <i class="bi bi-plus-lg"></i> Adicionar Variação
                </button>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Salvar Produto
            </button>
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