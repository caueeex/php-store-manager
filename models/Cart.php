<?php
class Cart {
    public function __construct() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function addItem($product_id, $quantity = 1) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }

    public function removeItem($product_id) {
        unset($_SESSION['cart'][$product_id]);
    }

    public function updateItem($product_id, $quantity) {
        if ($quantity <= 0) {
            $this->removeItem($product_id);
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }

    public function getItems() {
        return $_SESSION['cart'] ?? [];
    }

    public function clear() {
        $_SESSION['cart'] = [];
    }

    public function getItemsWithDetails($db) {
        $items = [];
        $productModel = new Product($db);
        
        foreach ($this->getItems() as $product_id => $quantity) {
            $product = $productModel->readById($product_id);
            if ($product) {
                $product['quantity'] = $quantity;
                $product['subtotal'] = $product['price'] * $quantity;
                $items[] = $product;
            }
        }
        
        return $items;
    }

    public function getSubtotal($db) {
        $subtotal = 0;
        foreach ($this->getItemsWithDetails($db) as $item) {
            $subtotal += $item['subtotal'];
        }
        return $subtotal;
    }

    public function getShipping($db) {
        return calculateShipping($this->getSubtotal($db));
    }

    public function getTotal($db) {
        $subtotal = $this->getSubtotal($db);
        $shipping = $this->getShipping($db);
        $discount = isset($_SESSION['coupon']) ? $_SESSION['coupon']['discount_value'] : 0;
        
        return ($subtotal + $shipping) - $discount;
    }

    public function isEmpty() {
        return empty($this->getItems());
    }
}
?>