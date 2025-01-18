<?php
//voir erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'Manager/CommandeManager.php';
require_once 'Class/classCommande.php';
require_once 'Class/classCommandeItem.php';

// BDD
$dsn = 'mysql:host=localhost;dbname=sae301;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // produits
    $stmt = $pdo->prepare('SELECT id, name, prix FROM products');
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // commandes en préparation
    $stmt = $pdo->prepare('SELECT id, user_id, total, creation FROM commande WHERE statut = "preparation"');
    $stmt->execute();
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $manager = new CommandeManager($pdo);
    //formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_commande_id'])) {
        $commandeId = (int)$_POST['delete_commande_id'];
        $manager->deleteCommande($commandeId);

        // Recharger la page après la suppression de la commande
        echo "<form id='reload-form' method='POST' style='display:none;'>
                <input type='hidden' name='form_submitted' value='1'>
              </form>
              <script>
                document.getElementById('reload-form').submit();
              </script>";
        exit;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['form_submitted'])) {
        $userId = (int)$_POST['user_id'];

        // si le user existe
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception('User ID does not exist.');
        }

        $commande = new Commande();
        $commande->setUserId($userId);
        $commande->setStatut('préparation'); //met  le statut en préparation
        $commande->setCreation($_POST['creation']);

        $items = [];
        $total = 0.0;
        foreach ($_POST['product_id'] as $index => $productId) {
            // si le produit existe
            $stmt = $pdo->prepare('SELECT prix FROM products WHERE id = :product_id');
            $stmt->execute(['product_id' => (int)$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$product) {
                throw new Exception('Product ID does not exist.');
            }

            $quantity = (int)$_POST['quantity'][$index];
            if ($quantity < 1) {
                throw new Exception('Quantity must be at least 1.');
            }

            $item = new CommandeItem();
            $item->setProductId((int)$productId);
            $item->setQuantity($quantity);
            $item->setPrice((float)$product['prix']);
            $items[] = $item;

            // Calculer le total
            $total += $item->getPrice() * $item->getQuantity();
        }

        $commande->setTotal($total);

        // ajout commande et items
        $manager->createCommande($commande, $items);

        // Recharger la page après l'ajout de la commande
        echo "<form id='reload-form' method='POST' style='display:none;'>
                <input type='hidden' name='form_submitted' value='1'>
              </form>
              <script>
                document.getElementById('reload-form').submit();
              </script>";
        exit;
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
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="style/IndexStyle.css">
    <title>Ajouter une Commande</title>
</head>

<body>
    <h1>Ajouter une nouvelle commande</h1> <a href="commande.php">commande</a>
    <form method="POST" id="commande-form">
        <label for="user_id">ID de l'utilisateur :</label>
        <input type="number" id="user_id" name="user_id" required><br><br>

        <label for="creation">Date de création :</label>
        <input type="date" id="creation" name="creation" required><br><br>

        <h2>Items de la commande</h2>
        <div id="items">
            <div class="item">
                <label>Produit :</label>
                <select name="product_id[]" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo htmlspecialchars($product['id']); ?>" data-price="<?php echo htmlspecialchars($product['prix'] ?? '0'); ?>">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Quantité :</label>
                <input type="number" name="quantity[]" min="1" value="1" required><br><br>
                <input type="text" name="prix[]" readonly>
            </div>
        </div>
        <button type="button" id="ajout-item">Ajouter un item</button><br><br>

        <button type="submit" id="ajouter-commande">Ajouter</button>
    </form>

    <h2>Commandes en préparation</h2>
    <?php if (empty($commandes)): ?>
        <p>Il n'y a pas de commandes en préparation.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($commandes as $commande): ?>
                <li>
                    Commande ID: <?php echo htmlspecialchars($commande['id']); ?>, 
                    Utilisateur ID: <?php echo htmlspecialchars($commande['user_id']); ?>, 
                    Total: <?php echo htmlspecialchars($commande['total']); ?>, 
                    Création: <?php echo htmlspecialchars($commande['creation']); ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="delete_commande_id" value="<?php echo htmlspecialchars($commande['id']); ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <script>
        //// AJOUTER UN NOUVEL ITEM DANS LA COMMANDE ////

        document.getElementById("ajout-item").addEventListener("click", function() {
            const itemsDiv = document.getElementById("items");
            const newItem = document.createElement("div");
            newItem.classList.add("item");
            newItem.innerHTML = `
        <label>Produit :</label>
        <select name="product_id[]" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo htmlspecialchars($product['id']); ?>" data-price="<?php echo htmlspecialchars($product['prix'] ?? '0'); ?>">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </option>
                    <?php endforeach; ?>
        </select>
        <label>Quantité :</label>
        <input type="number" name="quantity[]" min="1" value="1" required><br><br>
        <input type="text" name="prix[]" readonly>
    `;
            itemsDiv.appendChild(newItem);
            updatePrice(newItem);
        });

        function updatePrice(item) {
            const select = item.querySelector('select[name="product_id[]"]');
            const priceInput = item.querySelector('input[name="prix[]"]');
            select.addEventListener('change', function() {
                const selectedOption = select.options[select.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                priceInput.value = price;
            });
        }

        document.querySelectorAll('.item').forEach(updatePrice);
    </script>
</body>

</html>