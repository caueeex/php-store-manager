<?php
require_once './models/Order.php';
require_once './config/database.php';

class OrderController {
    private $db;
    private $orderModel;
    private $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->orderModel = new Order($this->db);
    }

    public function index() {
        // Verifica se há um filtro de status
        $status = $_GET['status'] ?? null;
        
        if ($status && in_array($status, $this->validStatuses)) {
            $orders = $this->orderModel->getByStatus($status);
        } else {
            $orders = $this->orderModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
        }
        
        include './views/orders/list.php';
    }

    public function detail($id) {
        $order = $this->orderModel->readById($id);
        if (!$order) {
            $_SESSION['error'] = 'Pedido não encontrado';
            header('Location: ?page=orders');
            exit;
        }
        
        $orderItems = $this->orderModel->getOrderItems($id);
        include './views/orders/detail.php';
    }

    public function success() {
        include './views/orders/success.php';
    }

    public function update_status($id, $status) {
        // Valida o status
        if (!in_array($status, $this->validStatuses)) {
            $_SESSION['error'] = 'Status inválido';
            header('Location: ?page=orders');
            exit;
        }

        // Verifica se o pedido existe
        $order = $this->orderModel->readById($id);
        if (!$order) {
            $_SESSION['error'] = 'Pedido não encontrado';
            header('Location: ?page=orders');
            exit;
        }

        // Verifica se o status atual é diferente do novo status
        if ($order['status'] === $status) {
            $_SESSION['error'] = 'O pedido já está com este status';
            header('Location: ?page=orders');
            exit;
        }

        // Atualiza o status
        if ($this->orderModel->updateStatus($id, $status)) {
            $_SESSION['success'] = 'Status do pedido atualizado com sucesso';
            
            // Se o pedido foi cancelado, restaura o estoque
            if ($status === 'cancelled' && $order['status'] !== 'cancelled') {
                require_once './models/Stock.php';
                $stockModel = new Stock($this->db);
                
                $orderItems = $this->orderModel->getOrderItems($id);
                foreach ($orderItems as $item) {
                    $stockModel->decrement($item['product_id'], -$item['quantity']);
                }
            }
        } else {
            $_SESSION['error'] = 'Erro ao atualizar status do pedido';
        }
        
        header('Location: ?page=orders');
        exit;
    }
}
?>