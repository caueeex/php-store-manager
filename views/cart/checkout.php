<?php
$pageTitle = 'Finalizar Compra';
include './views/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Finalizar Compra</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?page=cart&action=checkout" class="needs-validation" novalidate>
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informações de Entrega</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" id="name" name="name" required 
                                       value="<?php echo isset($customerData['name']) ? htmlspecialchars($customerData['name']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">E-mail *</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?php echo isset($customerData['email']) ? htmlspecialchars($customerData['email']) : ''; ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       value="<?php echo isset($customerData['phone']) ? htmlspecialchars($customerData['phone']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="zip_code" class="form-label">CEP *</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code" required
                                       value="<?php echo isset($customerData['zip_code']) ? htmlspecialchars($customerData['zip_code']) : ''; ?>"
                                       onblur="searchZipcode(this.value)">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Endereço *</label>
                            <input type="text" class="form-control" id="address" name="address" required
                                   value="<?php echo isset($customerData['address']) ? htmlspecialchars($customerData['address']) : ''; ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Resumo do Pedido</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        $cartItems = $this->cart->getItemsWithDetails($this->db);
                        $subtotal = $this->cart->getSubtotal($this->db);
                        $shipping = $this->cart->getShipping($this->db);
                        $discount = $_SESSION['coupon']['discount_value'] ?? 0;
                        $total = $subtotal + $shipping - $discount;
                        ?>

                        <?php foreach ($cartItems as $item): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                                <span>R$ <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></span>
                            </div>
                        <?php endforeach; ?>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Frete:</span>
                            <span>R$ <?php echo number_format($shipping, 2, ',', '.'); ?></span>
                        </div>

                        <?php if (isset($_SESSION['coupon'])): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Desconto:</span>
                                <span>-R$ <?php echo number_format($_SESSION['coupon']['discount_value'], 2, ',', '.'); ?></span>
                            </div>
                        <?php endif; ?>

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong>R$ <?php echo number_format($total, 2, ',', '.'); ?></strong>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Finalizar Pedido</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function searchZipcode(zipcode) {
    zipcode = zipcode.replace(/\D/g, '');
    
    if (zipcode.length !== 8) {
        return;
    }
    
    fetch(`https://viacep.com.br/ws/${zipcode}/json/`)
        .then(response => response.json())
        .then(data => {
            if (!data.erro) {
                document.getElementById('address').value = `${data.logradouro}, ${data.bairro}`;
            }
        })
        .catch(error => console.error('Erro ao buscar CEP:', error));
}

// Validação do formulário
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php include './views/footer.php'; ?>