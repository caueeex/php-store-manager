<?php
class OrderItem {
    private $conn;
    private $table = 'order_items';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                 (order_id, product_id, quantity, price) 
                 VALUES 
                 (:order_id, :product_id, :quantity, :price)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':order_id', $data['order_id'], PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $data['product_id'], PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':price', $data['price'], PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    public function getByOrderId($orderId) {
        $query = "SELECT oi.*, p.name 
                 FROM {$this->table} oi 
                 JOIN products p ON oi.product_id = p.id 
                 WHERE oi.order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 