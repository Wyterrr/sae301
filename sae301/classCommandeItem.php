<?php 
class CommandeItem {
    private $id;
    private $order_id;
    private $product_id;
    private $quantity;
    private $price;

    public function setId($id) {
        if (!is_int($id) || $id <= 0) {
            throw new Exception("Invalid ID. Must be a positive integer.");
        }
        $this->id = $id;
    }

    public function setOrderId($order_id) {
        if (!is_int($order_id) || $order_id <= 0) {
            throw new Exception("Invalid order ID. Must be a positive integer.");
        }
        $this->order_id = $order_id;
    }

    public function setProductId($product_id) {
        if (!is_int($product_id) || $product_id <= 0) {
            throw new Exception("Invalid product ID. Must be a positive integer.");
        }
        $this->product_id = $product_id;
    }

    public function setQuantity($quantity) {
        if (!is_int($quantity) || $quantity <= 0) {
            throw new Exception("Quantity must be a positive integer.");
        }
        $this->quantity = $quantity;
    }

    public function setPrice($price) {
        if (!is_numeric($price) || $price < 0) {
            throw new Exception("Price must be a positive number.");
        }
        $this->price = $price;
    }

    // Getters (inchangés)
    public function getId() { return $this->id; }
    public function getOrderId() { return $this->order_id; }
    public function getProductId() { return $this->product_id; }
    public function getQuantity() { return $this->quantity; }
    public function getPrice() { return $this->price; }
}

?>