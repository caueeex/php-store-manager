<?php
class Coupon {
    private $conn;
    private $table = 'coupons';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readByCode($code) {
        $query = "SELECT * FROM {$this->table} WHERE code = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  SET code = :code,
                  discount_type = :discount_type,
                  discount_value = :discount_value,
                  min_order_value = :min_order_value,
                  start_date = :start_date,
                  end_date = :end_date,
                  max_uses = :max_uses";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':code', $data['code']);
        $stmt->bindParam(':discount_type', $data['discount_type']);
        $stmt->bindParam(':discount_value', $data['discount_value']);
        $stmt->bindParam(':min_order_value', $data['min_order_value']);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':max_uses', $data['max_uses']);
        
        return $stmt->execute();
    }

    public function update($data) {
        $query = "UPDATE {$this->table} 
                  SET code = :code,
                  discount_type = :discount_type,
                  discount_value = :discount_value,
                  min_order_value = :min_order_value,
                  start_date = :start_date,
                  end_date = :end_date,
                  max_uses = :max_uses
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':code', $data['code']);
        $stmt->bindParam(':discount_type', $data['discount_type']);
        $stmt->bindParam(':discount_value', $data['discount_value']);
        $stmt->bindParam(':min_order_value', $data['min_order_value']);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':max_uses', $data['max_uses']);
        $stmt->bindParam(':id', $data['id']);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function incrementUseCount($id) {
        $query = "UPDATE {$this->table} SET use_count = use_count + 1 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function validateCoupon($code, $subtotal) {
        $coupon = $this->readByCode($code);
        if (!$coupon) return false;

        $today = date('Y-m-d');
        if ($today < $coupon['start_date'] || $today > $coupon['end_date']) {
            return false;
        }

        if ($coupon['max_uses'] && $coupon['use_count'] >= $coupon['max_uses']) {
            return false;
        }

        if ($coupon['min_order_value'] && $subtotal < $coupon['min_order_value']) {
            return false;
        }

        return $coupon;
    }
}
?>