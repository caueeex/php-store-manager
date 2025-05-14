$(document).ready(function() {
    // ViaCEP integration
    $('#zipcode').on('blur', function() {
        const zipcode = $(this).val().replace(/\D/g, '');
        
        if(zipcode.length === 8) {
            $.getJSON(`https://viacep.com.br/ws/${zipcode}/json/`, function(data) {
                if(!data.erro) {
                    $('#address').val(data.logradouro);
                    $('#neighborhood').val(data.bairro);
                    $('#city').val(data.localidade);
                    $('#state').val(data.uf);
                } else {
                    alert('CEP não encontrado');document.addEventListener('DOMContentLoaded', function() {
    // Atualiza contador do carrinho
    function updateCartCount() {
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            fetch('?page=cart&action=count')
                .then(response => response.json())
                .then(data => {
                    cartCount.textContent = data.count;
                });
        }
    }
    
    // Atualiza a cada 5 segundos (opcional)
    setInterval(updateCartCount, 5000);
    
    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Busca CEP
    const searchZipcodeBtn = document.getElementById('search-zipcode');
    if (searchZipcodeBtn) {
        searchZipcodeBtn.addEventListener('click', function() {
            const zipcode = document.getElementById('zipcode').value.replace(/\D/g, '');
            
            if (zipcode.length !== 8) {
                alert('CEP deve conter 8 dígitos');
                return;
            }
            
            fetch(`https://viacep.com.br/ws/${zipcode}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        alert('CEP não encontrado');
                        return;
                    }
                    
                    document.getElementById('address').value = data.logradouro;
                    document.getElementById('neighborhood').value = data.bairro;
                    document.getElementById('city').value = data.localidade;
                    document.getElementById('state').value = data.uf;
                })
                .catch(error => {
                    console.error('Erro ao buscar CEP:', error);
                    alert('Erro ao buscar CEP');
                });
        });
    }
});
                }
            }).fail(function() {
                alert('Erro ao buscar CEP');
            });
        }
    });
    
    // Cart functionality
    $('.add-to-cart').on('click', function() {
        const productId = $(this).data('id');
        
        $.post('controllers/CartController.php', {
            action: 'add',
            product_id: productId,
            quantity: 1
        }, function(response) {
            if(response.success) {
                updateCartCount(response.cart_count);
                alert('Product added to cart');
            } else {
                alert(response.message);
            }
        }, 'json');
    });
    
    function updateCartCount(count) {
        $('#cart-count').text(count);
    }
});