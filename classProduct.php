<?php 
class Product {
    private $id;
    private $name;
    private $description;
    private $prix;
    private $stock;
    private $creation;

    public function setId($id) {
        if (!is_int($id) || $id <= 0) {
            throw new Exception("Invalid ID. Must be a positive integer.");
        }
        $this->id = $id;
    }

    public function setName($name) {
        if (empty($name) || strlen($name) > 255) {
            throw new Exception("Invalid name. Must not be empty and less than 255 characters.");
        }
        $this->name = $name;
    }

    public function setDescription($description) {
        $this->description = $description; // Optionnel, aucune règle stricte
    }

    public function setPrix($prix) {
        if (!is_numeric($prix) || $prix < 0) {
            throw new Exception("Price must be a positive number.");
        }
        $this->prix = $prix;
    }

    public function setStock($stock) {
        if (!is_int($stock) || $stock < 0) {
            throw new Exception("Stock must be a non-negative integer.");
        }
        $this->stock = $stock;
    }

    public function setCreation($creation) {
        if (!strtotime($creation)) {
            throw new Exception("Invalid creation date format.");
        }
        $this->creation = $creation;
    }

    // Getters (inchangés)
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getDescription() { return $this->description; }
    public function getPrix() { return $this->prix; }
    public function getStock() { return $this->stock; }
    public function getCreation() { return $this->creation; }
}

?>