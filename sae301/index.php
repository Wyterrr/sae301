<?php
// Activer l'affichage des erreurs pour déboguer
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusion des fichiers nécessaires
require_once 'CommandeManager.php';
require_once 'classCommande.php';
require_once 'classCommandeItem.php'; // Assurez-vous que cette classe est bien définie

// Configuration de la connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=sae301;charset=utf8'; // Remplacez "nom_de_la_base" par le nom de votre base
$username = 'root'; // Par défaut, c'est "root" pour XAMPP
$password = ''; // Par défaut, pas de mot de passe pour XAMPP

try {
    // Créer une instance PDO
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Instanciation de CommandeManager
    $manager = new CommandeManager($pdo);

    // Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = (int)$_POST['user_id'];

        // Vérifier si l'utilisateur existe
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception('User ID does not exist.');
        }

        $commande = new Commande();
        $commande->setUserId($userId);
        $commande->setStatut('préparation'); // Set statut to 'preparation'
        $commande->setCreation($_POST['creation']);

        // Traitement des items de la commande
        $items = [];
        $total = 0.0;
        foreach ($_POST['product_id'] as $index => $productId) {
            // Vérifier si le produit existe
            $stmt = $pdo->prepare('SELECT prix FROM products WHERE id = :product_id');
            $stmt->execute(['product_id' => (int)$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$product) {
                throw new Exception('Product ID does not exist.');
            }

            $item = new CommandeItem();
            $item->setProductId((int)$productId);
            $item->setQuantity((int)$_POST['quantity'][$index]);
            $item->setPrice((float)$product['prix']);
            $items[] = $item;

            // Calculer le total
            $total += $item->getPrice() * $item->getQuantity();
        }

        $commande->setTotal($total);

        // Ajouter la commande et ses items
        $manager->createCommande($commande, $items);
    }
} catch (Exception $e) {
    echo 'Erreur de connexion à la base de données : ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Commande</title>
</head>
<body>
    <h1>Ajouter une nouvelle commande</h1>
    <form method="POST">
        <label for="user_id">ID de l'utilisateur :</label>
        <input type="number" id="user_id" name="user_id" required><br><br>

        <label for="creation">Date de création :</label>
        <input type="date" id="creation" name="creation" required><br><br>

        <h2>Items de la commande</h2>
        <div id="items">
            <div class="item">
                <label>Produit :</label>
                <input type="text" name="product_name[]" required>
                <label>Quantité :</label>
                <input type="number" name="quantity[]" required><br><br>
            </div>
        </div>
        <button type="button" onclick="addItem()">Ajouter un item</button><br><br>

        <button type="submit">Ajouter</button>
    </form>

    <script>
        function addItem() {
            const itemsDiv = document.getElementById('items');
            const newItem = document.createElement('div');
            newItem.classList.add('item');
            newItem.innerHTML = `
                <label>Produit :</label>
                <input type="number" name="product_id[]" required>
                <label>Quantité :</label>
                <input type="number" name="quantity[]" required><br><br>
            `;
            itemsDiv.appendChild(newItem);
        }
    </script>
</body>
</html>
