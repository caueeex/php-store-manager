<?php
require_once './models/Cart.php';
require_once './models/Product.php';
require_once './models/Coupon.php';
require_once './models/Order.php';
require_once './models/Stock.php';
require_once './config/database.php';
require_once './config/functions.php';
require_once './config/constants.php';
require_once './models/OrderItem.php';
require_once './lib/Mailer.php';

class CartController
{
    private $db;
    private $cart;
    private $productModel;
    private $couponModel;
    private $stockModel;
    private $orderModel;
    private $orderItemModel;
    private $mailer;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
        $this->cart = new Cart();
        $this->productModel = new Product($this->db);
        $this->couponModel = new Coupon($this->db);
        $this->stockModel = new Stock($this->db);
        $this->orderModel = new Order($this->db);
        $this->orderItemModel = new OrderItem($this->db);
        $this->mailer = new Mailer();
    }

    public function index()
    {
        $this->view();
    }
    public function view()
    {
        $cartItems = $this->cart->getItemsWithDetails($this->db);
        $subtotal = $this->cart->getSubtotal($this->db);

        // Cálculo do frete baseado no subtotal
        $shipping = $this->calculateShipping($subtotal);
        $total = $subtotal + $shipping;

        // Verifica se tem CEP na sessão
        $shippingAddress = $_SESSION['shipping_address'] ?? null;

        include './views/cart/view.php';
    }
    public function saveShipping()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['shipping'] = [
                'value' => (float)$_POST['shipping'],
                'cep' => preg_replace('/[^0-9]/', '', $_POST['cep'])
            ];
            echo json_encode(['success' => true]);
            exit;
        }
    }

    private function calculateShipping($subtotal)
    {
        if ($subtotal > 200) {
            return 0;
        } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15;
        } else {
            return 20;
        }
    }

    public function calculateShippingByCep()
    {
        $cep = $_POST['cep'] ?? '';
        $cep = preg_replace('/[^0-9]/', '', $cep);

        if (strlen($cep) !== 8) {
            echo json_encode(['error' => 'CEP inválido']);
            exit;
        }

        // Busca endereço via ViaCEP
        $url = "https://viacep.com.br/ws/{$cep}/json/";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $addressData = json_decode($response, true);

        if (isset($addressData['erro'])) {
            echo json_encode(['error' => 'CEP não encontrado']);
            exit;
        }

        // Calcula frete baseado no subtotal
        $subtotal = $this->cart->getSubtotal($this->db);
        $shipping = $this->calculateShipping($subtotal);

        // Armazena na sessão
        $_SESSION['shipping_address'] = [
            'cep' => $cep,
            'logradouro' => $addressData['logradouro'],
            'bairro' => $addressData['bairro'],
            'localidade' => $addressData['localidade'],
            'uf' => $addressData['uf']
        ];

        echo json_encode([
            'address' => "{$addressData['logradouro']}, {$addressData['bairro']}, {$addressData['localidade']} - {$addressData['uf']}",
            'shipping' => $shipping,
            'subtotal' => $subtotal,
            'total' => $subtotal + $shipping
        ]);
        exit;
    }
    // public function view() {
    //     $cartItems = $this->cart->getItemsWithDetails($this->db);
    //     $subtotal = $this->cart->getSubtotal($this->db);
    //     $shipping = $this->cart->getShipping($this->db);
    //     $total = $this->cart->getTotal($this->db);

    //     include './views/cart/view.php';
    // }

    public function add()
    {
        if (!isset($_POST['product_id'])) {
            $_SESSION['error'] = 'Produto não especificado';
            header('Location: ?page=products');
            exit;
        }

        $productId = (int)$_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        if ($quantity < 1) {
            $_SESSION['error'] = 'Quantidade inválida';
            header('Location: ?page=products');
            exit;
        }

        $product = $this->productModel->readById($productId);
        if (!$product) {
            $_SESSION['error'] = 'Produto não encontrado';
            header('Location: ?page=products');
            exit;
        }

        $stock = $this->stockModel->getByProductId($productId);
        if ($stock['quantity'] < $quantity) {
            $_SESSION['error'] = 'Quantidade em estoque insuficiente';
            header('Location: ?page=products');
            exit;
        }

        $this->cart->addItem($productId, $quantity);
        $_SESSION['success'] = 'Produto adicionado ao carrinho';
        header('Location: ?page=cart');
        exit;
    }

    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Método inválido';
            header('Location: ?page=cart');
            exit;
        }

        if (!isset($_POST['product_id'])) {
            $_SESSION['error'] = 'Produto não especificado';
            header('Location: ?page=cart');
            exit;
        }

        $productId = (int)$_POST['product_id'];

        if (!$this->productModel->readById($productId)) {
            $_SESSION['error'] = 'Produto não encontrado';
            header('Location: ?page=cart');
            exit;
        }

        $this->cart->removeItem($productId);
        $_SESSION['success'] = 'Produto removido do carrinho';
        header('Location: ?page=cart');
        exit;
    }

    public function update()
    {
        if (!isset($_POST['quantity']) || !is_array($_POST['quantity'])) {
            $_SESSION['error'] = 'Quantidades não especificadas';
            header('Location: ?page=cart');
            exit;
        }

        foreach ($_POST['quantity'] as $productId => $quantity) {
            $productId = (int)$productId;
            $quantity = (int)$quantity;

            if ($quantity < 1) {
                $this->cart->removeItem($productId);
                continue;
            }

            $product = $this->productModel->readById($productId);
            if (!$product) {
                continue;
            }

            $stock = $this->stockModel->getByProductId($productId);
            if ($stock['quantity'] < $quantity) {
                $_SESSION['error'] = "Quantidade em estoque insuffciente para {$product['name']}";
                continue;
            }

            $this->cart->updateItem($productId, $quantity);
        }

        header('Location: ?page=cart');
        exit;
    }

    public function applyCoupon()
    {
        if (!isset($_POST['coupon_code']) || empty(trim($_POST['coupon_code']))) {
            $_SESSION['error'] = 'Código do cupom não especificado';
            header('Location: ?page=cart');
            exit;
        }

        $code = trim($_POST['coupon_code']);
        $subtotal = $this->cart->getSubtotal($this->db);

        $coupon = $this->couponModel->validateCoupon($code, $subtotal);
        if ($coupon) {
            $_SESSION['coupon'] = [
                'id' => $coupon['id'],
                'code' => $coupon['code'],
                'discount_type' => $coupon['discount_type'],
                'discount_value' => $coupon['discount_type'] === 'percentage'
                    ? ($subtotal * $coupon['discount_value'] / 100)
                    : $coupon['discount_value']
            ];
            $_SESSION['success'] = 'Cupom aplicado com sucesso!';
        } else {
            $_SESSION['error'] = 'Cupom inválido, expirado ou não aplicável ao valor atual';
        }

        header('Location: ?page=cart');
        exit;
    }
    public function checkoutForm()
    {
        // Verifica se há itens no carrinho
        if (empty($this->cart->getItems())) {
            $_SESSION['error'] = 'Seu carrinho está vazio';
            header('Location: ?page=products');
            exit;
        }

        // Busca endereço se CEP já foi informado
        $shippingAddress = $_SESSION['shipping_address'] ?? null;

        // Dados do cliente da sessão (se existir)
        $customerData = [
            'name' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'phone' => $_SESSION['user_phone'] ?? '',
            'address' => $shippingAddress ?
                ($shippingAddress['logradouro'] . ', ' . $shippingAddress['bairro'] . ', ' .
                    $shippingAddress['localidade'] . ' - ' . $shippingAddress['uf']) : ''
        ];

        include './views/cart/checkout.php';
    }
    public function checkout()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validação dos campos obrigatórios
                $requiredFields = ['name', 'email', 'address', 'zip_code'];
                foreach ($requiredFields as $field) {
                    if (empty(trim($_POST[$field]))) {
                        throw new Exception("O campo " . ucfirst($field) . " é obrigatório");
                    }
                }

                // Formata o CEP (remove caracteres não numéricos)
                $zipcode = preg_replace('/[^0-9]/', '', $_POST['zip_code']);
                if (strlen($zipcode) !== 8) {
                    throw new Exception("CEP inválido");
                }

                // Verifica se há itens no carrinho
                if ($this->cart->isEmpty()) {
                    throw new Exception("O carrinho está vazio");
                }

                // Calcula os valores do pedido
                $subtotal = $this->cart->getSubtotal($this->db);
                $shipping = $this->cart->getShipping($this->db);
                $discount = isset($_SESSION['coupon']['discount_value']) ? $_SESSION['coupon']['discount_value'] : 0;
                $total = $subtotal + $shipping - $discount;

                // Inicia a transação
                if (!$this->orderModel->beginTransaction()) {
                    throw new Exception("Erro ao iniciar a transação");
                }
                
                try {
                    // Cria o pedido
                    $orderData = [
                        'customer_name' => trim($_POST['name']),
                        'customer_email' => trim($_POST['email']),
                        'customer_phone' => isset($_POST['phone']) ? trim($_POST['phone']) : null,
                        'customer_address' => trim($_POST['address']),
                        'customer_zipcode' => $zipcode,
                        'subtotal' => $subtotal,
                        'shipping' => $shipping,
                        'discount' => $discount,
                        'total' => $total,
                        'status' => 'pending',
                        'coupon_id' => $_SESSION['coupon']['id'] ?? null
                    ];
                    
                    $orderId = $this->orderModel->create($orderData);
                    
                    if (!$orderId) {
                        throw new Exception("Erro ao criar o pedido");
                    }
                    
                    // Cria os itens do pedido e atualiza o estoque
                    $cartItems = $this->cart->getItemsWithDetails($this->db);
                    foreach ($cartItems as $item) {
                        $orderItemData = [
                            'order_id' => $orderId,
                            'product_id' => $item['id'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price']
                        ];
                        
                        if (!$this->orderItemModel->create($orderItemData)) {
                            throw new Exception("Erro ao criar item do pedido");
                        }
                        
                        if (!$this->stockModel->decrement($item['id'], $item['quantity'])) {
                            throw new Exception("Erro ao atualizar estoque");
                        }
                    }
                    
                    // Obtém os dados do pedido e itens para o email
                    $order = $this->orderModel->getById($orderId);
                    $items = $this->orderItemModel->getByOrderId($orderId);
                    
                    // Envia o email de confirmação
                    if (!$this->mailer->sendOrderConfirmation($order, $items)) {
                        error_log("Erro ao enviar email de confirmação para o pedido #" . $orderId);
                        $_SESSION['warning'] = 'O pedido foi processado com sucesso, mas houve um problema ao enviar o email de confirmação. Por favor, verifique seu email.';
                    }
                    
                    // Limpa o carrinho e cupom
                    $this->cart->clear();
                    if (isset($_SESSION['coupon'])) {
                        unset($_SESSION['coupon']);
                    }
                    
                    // Confirma a transação
                    if (!$this->orderModel->commit()) {
                        throw new Exception("Erro ao confirmar a transação");
                    }
                    
                    // Redireciona para a página de sucesso
                    header('Location: index.php?page=orders&action=success&id=' . $orderId);
                    exit;
                    
                } catch (Exception $e) {
                    // Em caso de erro, desfaz a transação
                    $this->orderModel->rollback();
                    throw $e; // Repassa a exceção para ser tratada no catch externo
                }
                
            } catch (Exception $e) {
                $_SESSION['error'] = 'Erro ao processar o pedido: ' . $e->getMessage();
                header('Location: index.php?page=cart');
                exit;
            }
        }
        
        // Se não for POST, mostra o formulário de checkout
        if ($this->cart->isEmpty()) {
            $_SESSION['error'] = 'O carrinho está vazio';
            header('Location: index.php?page=cart');
            exit;
        }

        $cartItems = $this->cart->getItemsWithDetails($this->db);
        $subtotal = $this->cart->getSubtotal($this->db);
        $shipping = $this->cart->getShipping($this->db);
        $discount = isset($_SESSION['coupon']['discount_value']) ? $_SESSION['coupon']['discount_value'] : 0;
        $total = $subtotal + $shipping - $discount;
        
        include 'views/cart/checkout.php';
    }

    private function getAddressFromZipcode($zipcode)
    {
        $zipcode = preg_replace('/[^0-9]/', '', $zipcode);
        if (strlen($zipcode) !== 8) {
            return null;
        }

        $url = "https://viacep.com.br/ws/{$zipcode}/json/";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return isset($data['erro']) ? null : $data;
    }

    private function sendConfirmationEmail($orderId, $orderData)
    {
        $to = $orderData['customer_email'];
        $subject = '[' . SITE_NAME . '] Confirmação de Pedido #' . $orderId;

        $message = "
        <html>
        <head>
            <title>Confirmação de Pedido</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .order-info { margin-bottom: 20px; }
                .order-items { width: 100%; border-collapse: collapse; }
                .order-items th, .order-items td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .order-items th { background-color: #f2f2f2; }
                .total { font-weight: bold; font-size: 1.2em; }
                .footer { margin-top: 20px; font-size: 0.9em; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h2>" . SITE_NAME . "</h2>
                <h3>Confirmação de Pedido #$orderId</h3>
            </div>
            
            <div class='content'>
                <p>Olá " . htmlspecialchars($orderData['customer_name']) . ",</p>
                <p>Seu pedido foi recebido com sucesso e está sendo processado.</p>
                
                <div class='order-info'>
                    <h4>Resumo do Pedido:</h4>
                    <table class='order-items'>
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Subtotal</th>
                        </tr>";

        $orderItems = $this->orderItemModel->getByOrderId($orderId);
        foreach ($orderItems as $item) {
            $message .= "
                        <tr>
                            <td>" . htmlspecialchars($item['name']) . "</td>
                            <td>" . $item['quantity'] . "</td>
                            <td>" . formatPrice($item['price']) . "</td>
                            <td>" . formatPrice($item['price'] * $item['quantity']) . "</td>
                        </tr>";
        }

        $message .= "
                        <tr>
                            <td colspan='3'><strong>Subtotal</strong></td>
                            <td>" . formatPrice($orderData['subtotal']) . "</td>
                        </tr>
                        <tr>
                            <td colspan='3'><strong>Frete</strong></td>
                            <td>" . formatPrice($orderData['shipping']) . "</td>
                        </tr>";

        if ($orderData['discount'] > 0) {
            $message .= "
                        <tr>
                            <td colspan='3'><strong>Desconto</strong></td>
                            <td>-" . formatPrice($orderData['discount']) . "</td>
                        </tr>";
        }

        $message .= "
                        <tr class='total'>
                            <td colspan='3'><strong>Total</strong></td>
                            <td>" . formatPrice($orderData['total']) . "</td>
                        </tr>
                    </table>
                </div>
                
                <div class='shipping-info'>
                    <h4>Endereço de Entrega:</h4>
                    <p>" . nl2br(htmlspecialchars($orderData['customer_address'])) . "<br>
                    CEP: " . htmlspecialchars($orderData['customer_zipcode']) . "</p>
                </div>
                
                <div class='footer'>
                    <p>Agradecemos por sua compra!</p>
                    <p>" . SITE_NAME . "</p>
                </div>
            </div>
        </body>
        </html>";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . ADMIN_EMAIL . "\r\n";
        $headers .= "Reply-To: " . ADMIN_EMAIL . "\r\n";

        mail($to, $subject, $message, $headers);
    }
}
