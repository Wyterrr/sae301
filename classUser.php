<?php 
class User {
    private $id;
    private $name;
    private $email;
    private $password;
    private $adresse;
    private $telephone;
    private $role; // 'admin' ou 'customer'
    private $creation;

    // Setters
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

    public function setEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }
        $this->email = $email;
    }

    public function setPassword($password) {
        if (strlen($password) < 8) {
            throw new Exception("Password must be at least 8 characters long.");
        }
        $this->password = $password;
    }

    public function setAdresse($adresse) {
        if (empty($adresse)) {
            throw new Exception("Adresse cannot be empty.");
        }
        $this->adresse = $adresse;
    }

    public function setTelephone($telephone) {
        if (!preg_match('/^[0-9]{10,15}$/', $telephone)) {
            throw new Exception("Invalid telephone number. Must be 10 to 15 digits.");
        }
        $this->telephone = $telephone;
    }

    public function setRole($role) {
        $validRoles = ['admin', 'customer'];
        if (!in_array($role, $validRoles)) {
            throw new Exception("Invalid role. Must be 'admin' or 'customer'.");
        }
        $this->role = $role;
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
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getAdresse() { return $this->adresse; }
    public function getTelephone() { return $this->telephone; }
    public function getRole() { return $this->role; }
    public function getCreation() { return $this->creation; }
}

?>