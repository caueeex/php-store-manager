<?php
class Order {
    private $conn;
    private $table = 'orders';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Cria um novo pedido
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                (customer_name, customer_email, customer_phone, 
                  customer_address, customer_zipcode,
                  subtotal, shipping, discount, total, status, coupon_id)
                  VALUES 
                 (:customer_name, :customer_email, :customer_phone,
                  :customer_address, :customer_zipcode,
                  :subtotal, :shipping, :discount, :total, :status, :coupon_id)";
        
        $stmt = $this->conn->prepare($query);
        
        // Extrai os valores do array para variáveis separadas
        $customer_name = $data['customer_name'];
        $customer_email = $data['customer_email'];
        $customer_phone = $data['customer_phone'] ?? null;
        $customer_address = $data['customer_address'];
        $customer_zipcode = $data['customer_zipcode'];
        $subtotal = $data['subtotal'];
        $shipping = $data['shipping'];
        $discount = $data['discount'];
        $total = $data['total'];
        $status = $data['status'] ?? 'pending';
        $coupon_id = $data['coupon_id'] ?? null;
        
        // Agora faz o bind com as variáveis
        $stmt->bindParam(':customer_name', $customer_name);
        $stmt->bindParam(':customer_email', $customer_email);
        $stmt->bindParam(':customer_phone', $customer_phone);
        $stmt->bindParam(':customer_address', $customer_address);
        $stmt->bindParam(':customer_zipcode', $customer_zipcode);
        $stmt->bindParam(':subtotal', $subtotal);
        $stmt->bindParam(':shipping', $shipping);
        $stmt->bindParam(':discount', $discount);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':coupon_id', $coupon_id);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    /**
     * Busca um pedido pelo ID
     */
    public function readById($id) {
        $query = "SELECT o.*, c.code as coupon_code 
                 FROM {$this->table} o
                 LEFT JOIN coupons c ON o.coupon_id = c.id
                 WHERE o.id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lista todos os pedidos
     */
    public function readAll() {
        $query = "SELECT o.*, c.code as coupon_code 
                 FROM {$this->table} o
                 LEFT JOIN coupons c ON o.coupon_id = c.id
                 ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Atualiza o status de um pedido
     */
    public function updateStatus($id, $status) {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $query = "UPDATE {$this->table} SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $id]);
    }

    /**
     * Adiciona um item ao pedido (método renomeado de createOrderItem para addItem)
     */
    public function addItem($order_id, $product_id, $quantity, $price) {
        $query = "INSERT INTO order_items 
                 (order_id, product_id, quantity, price)
                  VALUES 
                 (:order_id, :product_id, :quantity, :price)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price);
        
        return $stmt->execute();
    }

    /**
     * Obtém os itens de um pedido
     */
    public function getOrderItems($order_id) {
        $query = "SELECT oi.*, p.name, 
                         (oi.quantity * oi.price) as subtotal
                  FROM order_items oi
                  JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém pedidos por status
     */
    public function getByStatus($status) {
        $query = "SELECT o.*, c.code as coupon_code 
                 FROM {$this->table} o
                 LEFT JOIN coupons c ON o.coupon_id = c.id
                 WHERE o.status = ?
                 ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém pedidos de um cliente
     */
    public function getByCustomerEmail($email) {
        $query = "SELECT o.*, c.code as coupon_code 
                 FROM {$this->table} o
                 LEFT JOIN coupons c ON o.coupon_id = c.id
                 WHERE o.customer_email = ?
                 ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function beginTransaction() {
        if (!$this->conn->inTransaction()) {
            return $this->conn->beginTransaction();
        }
        return true;
    }
    
    public function commit() {
        if ($this->conn->inTransaction()) {
            return $this->conn->commit();
        }
        return true;
    }
    
    public function rollback() {
        if ($this->conn->inTransaction()) {
            return $this->conn->rollBack();
        }
        return true;
    }
    
    public function getById($id) {
        $query = "SELECT o.*, c.code as coupon_code,
                        o.customer_name as name,
                        o.customer_email as email,
                        o.customer_address as address,
                        o.customer_zipcode as zip_code
                 FROM {$this->table} o 
                 LEFT JOIN coupons c ON o.coupon_id = c.id 
                 WHERE o.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>