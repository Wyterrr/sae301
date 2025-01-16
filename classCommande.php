<?php
class Commande {
    private $id;
    private $user_id;
    private $statut; // 'préparation', 'en cours', 'livrée'
    private $total;
    private $creation;

    public function setId($id) {
        if (!is_int($id) || $id <= 0) {
            throw new Exception("Invalid ID. Must be a positive integer.");
        }
        $this->id = $id;
    }

    public function setUserId($user_id) {
        if (!is_int($user_id) || $user_id <= 0) {
            throw new Exception("Invalid user ID. Must be a positive integer.");
        }
        $this->user_id = $user_id;
    }

    public function setStatut($statut) {
        $validStatuts = ['préparation', 'en cours', 'livrée'];
        if (!in_array($statut, $validStatuts)) {
            throw new Exception("Invalid status. Must be 'préparation', 'en cours', or 'livrée'.");
        }
        $this->statut = $statut;
    }

    public function setTotal($total) {
        if (!is_numeric($total) || $total < 0) {
            throw new Exception("Total must be a positive number.");
        }
        $this->total = $total;
    }

    public function setCreation($creation) {
        if (!strtotime($creation)) {
            throw new Exception("Invalid creation date format.");
        }
        $this->creation = $creation;
    }

    // Getters (inchangés)
    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getStatut() { return $this->statut; }
    public function getTotal() { return $this->total; }
    public function getCreation() { return $this->creation; }
}
 ?>