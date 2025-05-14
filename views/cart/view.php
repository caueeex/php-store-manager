<?php
$pageTitle = 'Carrinho de Compras';
include './views/header.php';

// Calcula valores
$subtotal = $this->cart->getSubtotal($this->db);
$shipping = $this->calculateShipping($subtotal);
$discount = isset($_SESSION['coupon']) ? $_SESSION['coupon']['discount_value'] : 0;
$total = $subtotal + $shipping - $discount;
?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Itens no Carrinho</h4>
                
                <?php if ($this->cart->isEmpty()): ?>
                    <div class="alert alert-info">Seu carrinho está vazio</div>
                    <a href="?page=products" class="btn btn-primary">Continuar Comprando</a>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Preço Unitário</th>
                                    <th>Quantidade</th>
                                    <th>Subtotal</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td><?= formatPrice($item['price']) ?></td>
                                    <td>
                                        <form method="POST" action="?page=cart&action=update" class="d-flex">
                                            <input type="number" name="quantity[<?= $item['id'] ?>]" 
                                                   value="<?= $item['quantity'] ?>" min="1" class="form-control" style="width: 80px;">
                                            <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td><?= formatPrice($item['subtotal']) ?></td>
                                    <td>
                                        <form method="POST" action="?page=cart&action=remove" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-3">
                        <a href="?page=products" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Continuar Comprando
                        </a>
                        <a href="?page=cart&action=checkout_form" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Finalizar Compra
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body">
                <h4 class="card-title">Calcular Frete</h4>
                
                <div class="mb-3">
                    <label for="cepInput" class="form-label">Digite seu CEP</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="cepInput" placeholder="00000-000">
                        <button class="btn btn-primary" id="calculateShippingBtn">
                            <i class="bi bi-truck"></i> Calcular
                        </button>
                    </div>
                </div>
                
                <div id="shippingResult" class="d-none">
                    <p><strong>Endereço:</strong> <span id="addressText"></span></p>
                    <p><strong>Frete:</strong> <span id="shippingValue"></span></p>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Resumo do Pedido</h4>
                
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Subtotal</span>
                        <span id="subtotalDisplay"><?= formatPrice($subtotal) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Frete</span>
                        <span id="shippingDisplay"><?= formatPrice($shipping) ?></span>
                    </li>
                    
                    <?php if (isset($_SESSION['coupon'])): ?>
                    <li class="list-group-item d-flex justify-content-between bg-light">
                        <span>Desconto (<?= $_SESSION['coupon']['code'] ?>)</span>
                        <span class="text-danger" id="discountDisplay">-<?= formatPrice($_SESSION['coupon']['discount_value']) ?></span>
                    </li>
                    <?php endif; ?>
                    
                    <li class="list-group-item d-flex justify-content-between bg-primary text-white">
                        <strong>Total</strong>
                        <strong id="totalDisplay"><?= formatPrice($total) ?></strong>
                    </li>
                </ul>
                
                <?php if (!isset($_SESSION['coupon'])): ?>
                <form method="POST" action="?page=cart&action=apply_coupon" class="mb-3" id="couponForm">
                    <div class="input-group">
                        <input type="text" class="form-control" name="coupon_code" placeholder="Código do cupom" required>
                        <button type="submit" class="btn btn-secondary">Aplicar</button>
                    </div>
                </form>
                <?php else: ?>
                <form method="POST" action="?page=cart&action=remove_coupon" class="mb-3">
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        Remover Cupom
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para o CEP
    const cepInput = document.getElementById('cepInput');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            e.target.value = value;
        });
    }

    // Cálculo de frete
    const calculateBtn = document.getElementById('calculateShippingBtn');
    if (calculateBtn) {
        calculateBtn.addEventListener('click', function() {
            const cep = cepInput.value.replace(/\D/g, '');
            const shippingResult = document.getElementById('shippingResult');
            
            if (cep.length !== 8) {
                alert('CEP inválido. Digite um CEP com 8 dígitos.');
                return;
            }
            
            // Mostra loading
            calculateBtn.disabled = true;
            calculateBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Calculando...';
            
            // Busca endereço via ViaCEP
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        throw new Error('CEP não encontrado');
                    }
                    
                    // Calcula frete baseado no subtotal
                    const subtotal = <?= $subtotal ?>;
                    let shipping = 20.00; // Default
                    
                    if (subtotal > 200) {
                        shipping = 0;
                    } else if (subtotal >= 52 && subtotal <= 166.59) {
                        shipping = 15.00;
                    }
                    
                    // Atualiza a UI
                    document.getElementById('addressText').textContent = 
                        `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
                    document.getElementById('shippingValue').textContent = 
                        shipping === 0 ? 'GRÁTIS' : 'R$ ' + shipping.toFixed(2).replace('.', ',');
                    
                    shippingResult.classList.remove('d-none');
                    
                    // Atualiza os valores na seção de resumo
                    document.getElementById('shippingDisplay').textContent = 
                        shipping === 0 ? 'GRÁTIS' : 'R$ ' + shipping.toFixed(2).replace('.', ',');
                    
                    const discount = <?= isset($_SESSION['coupon']) ? $_SESSION['coupon']['discount_value'] : 0 ?>;
                    const total = subtotal + shipping - discount;
                    document.getElementById('totalDisplay').textContent = 
                        'R$ ' + total.toFixed(2).replace('.', ',');
                    
                    // Armazena o frete na sessão via AJAX
                    return fetch('?page=cart&action=saveShipping', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `shipping=${shipping}&cep=${cep}`
                    });
                })
                .catch(error => {
                    alert(error.message);
                })
                .finally(() => {
                    calculateBtn.disabled = false;
                    calculateBtn.innerHTML = '<i class="bi bi-truck"></i> Calcular';
                });
        });
    }
});
</script>

<?php include './views/footer.php'; ?>