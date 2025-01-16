<?php 
class CommandeManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


    // Créer une commande
    public function createCommande($commande, $items) {
        $this->db->beginTransaction();

        // Insérer la commande
        $query = $this->db->prepare(
            "INSERT INTO commande (user_id, statut, total, creation) 
             VALUES (:user_id, :statut, :total, :creation)"
        );

        $query->execute([
            'user_id' => $commande->getUserId(),
            'statut' => $commande->getStatut(),
            'total' => $commande->getTotal(),
            'creation' => date('Y-m-d H:i:s'),
        ]);

        $commandeId = $this->db->lastInsertId();

        // Insérer les items de la commande
        foreach ($items as $item) {
            $query = $this->db->prepare(
                "INSERT INTO commande_item (order_id, product_id, quantity, price) 
                 VALUES (:order_id, :product_id, :quantity, :price)"
            );

            $query->execute([
                'order_id' => $commandeId,
                'product_id' => $item->getProductId(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getPrice(),
            ]);
        }

        $this->db->commit();
    }

    // Mettre à jour le statut d'une commande
    public function updateStatut($commandeId, $newStatut) {
        $query = $this->db->prepare("UPDATE commande SET statut = :statut WHERE id = :id");
        $query->execute(['statut' => $newStatut, 'id' => $commandeId]);
    }

    // Récupérer les détails d’une commande
    public function getCommandeDetails($commandeId) {
        $query = $this->db->prepare("SELECT * FROM commande WHERE id = :id");
        $query->execute(['id' => $commandeId]);
        $commande = $query->fetch();

        $query = $this->db->prepare("SELECT * FROM commande_item WHERE order_id = :id");
        $query->execute(['id' => $commandeId]);
        $items = $query->fetchAll();

        return ['commande' => $commande, 'items' => $items];
    }

    public function getAllCommandeIds() {
        $query = $this->db->prepare("SELECT id FROM commande");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }
}

    

?>
