<?php
require_once 'classProduct.php';

class ProductManager
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Ajouter un produit
    public function addProduct($product)
    {
        $query = $this->db->prepare(
            "INSERT INTO products (name, description, prix, stock, creation)
             VALUES (:name, :description, :prix, :stock, :creation)"
        );

        $query->execute([
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'prix' => $product->getPrix(),
            'stock' => $product->getStock(),
            'creation' => date('Y-m-d H:i:s'),
        ]);
    }

    // Récupérer un produit par ID
    public function getProductById($id)
    {
        $query = $this->db->prepare("SELECT * FROM products WHERE id = :id");
        $query->execute(['id' => $id]);
        $data = $query->fetch();
        return $data ? $this->mapToProduct($data) : null;
    }

    // Mettre à jour les informations d’un produit
    public function updateProduct($product)
    {
        $query = $this->db->prepare(
            "UPDATE products SET name = :name, description = :description, prix = :prix, stock = :stock
             WHERE id = :id"
        );

        $query->execute([
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'prix' => $product->getPrix(),
            'stock' => $product->getStock(),
            'id' => $product->getId(),
        ]);
    }

    // Mettre à jour le stock
    public function updateStock($productId, $newStock)
    {
        $query = $this->db->prepare("UPDATE products SET stock = :stock WHERE id = :id");
        $query->execute(['stock' => $newStock, 'id' => $productId]);
    }

    // Récupérer tous les produits disponibles (stock > 0)
    public function getAvailableProducts()
    {
        $query = $this->db->query("SELECT * FROM products WHERE stock > 0");
        $results = $query->fetchAll();
        return array_map([$this, 'mapToProduct'], $results);
    }

    // Mapper les données SQL vers un objet Product
    private function mapToProduct($data)
    {
        $product = new Product();
        $product->setId($data['id']);
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrix($data['prix']);
        $product->setStock($data['stock']);
        $product->setCreation($data['creation']);
        return $product;
    }


    // Récupérer le stock d'un produit par ID
    public function getStockById($id)
    {
        // Prépare la requête SQL pour sélectionner le stock du produit avec l'ID donné
        $query = $this->db->prepare("SELECT stock FROM products WHERE id = :id");

        // Exécute la requête avec l'ID fourni
        $query->execute(['id' => $id]);

        // Récupère le résultat de la requête
        $data = $query->fetch();

        // Retourne le stock si le produit est trouvé, sinon retourne null
        return $data ? $data['stock'] : null;
    }
    public function getLowStockProducts($threshold)
    {
        $query = $this->db->prepare("SELECT * FROM products WHERE stock < :threshold");
        $query->execute(['threshold' => $threshold]);
        $results = $query->fetchAll();
        return array_map([$this, 'mapToProduct'], $results);
    }

    public function getAllProducts()
    {
        $stmt = $this->db->query('SELECT * FROM products');
        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $product = new Product();
            $product->setId($row['id']);
            $product->setName($row['name']);
            $product->setDescription($row['description']);
            $product->setPrix($row['prix']);
            $product->setStock($row['stock']);
            $products[] = $product;
        }
        return $products;
    }
}
?>
