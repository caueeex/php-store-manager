<?php
class Stock {
    private $conn;
    private $table = 'stock';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByProductId($product_id) {
        $query = "SELECT * FROM {$this->table} WHERE product_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  SET product_id = :product_id, quantity = :quantity";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $data['product_id']);
        $stmt->bindParam(':quantity', $data['quantity']);
        
        return $stmt->execute();
    }

    public function update($data) {
        $query = "UPDATE {$this->table} 
                  SET quantity = :quantity 
                  WHERE product_id = :product_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $data['quantity']);
        $stmt->bindParam(':product_id', $data['product_id']);
        
        return $stmt->execute();
    }

    /**
     * Decrementa a quantidade em estoque de um produto
     */
    public function decrement($product_id, $quantity) {
        $query = "UPDATE {$this->table} 
                  SET quantity = quantity - :quantity,
                      updated_at = NOW()
                  WHERE product_id = :product_id 
                  AND quantity >= :quantity";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
?>