<?php
require_once 'Manager/CommandeManager.php';

// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=sae301;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $manager = new CommandeManager($pdo);

    $commandeIds = $manager->getAllCommandeIds();
} catch (Exception $e) {
    echo 'Erreur de connexion à la base de données : ' . $e->getMessage();
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'getDetails' && isset($_GET['id'])) {
    $commandeId = $_GET['id'];
    $details = $manager->getCommandeDetails($commandeId);

    $commande = $details['commande'];
    $items = $details['items'];
    ?>

    <h3>Commande n°<?php echo htmlspecialchars($commandeId); ?></h3>
    <div class="info">
        <p class="name"><?php echo htmlspecialchars($commande['name']); ?></p>
        <p class="date"><?php echo htmlspecialchars($commande['creation']); ?></p>
    </div>
    <div class="contact">
        <p class="mail"><?php echo htmlspecialchars($commande['email']); ?></p>
        <p class="telephone"><?php echo htmlspecialchars($commande['telephone']); ?></p>
    </div>
    <div class="items">
        <h4>Items:</h4>
        <ul>
            <?php foreach ($items as $item): ?>
                <li class="product">Produit: <?php echo htmlspecialchars($item['product_name']); ?>, Quantité: <?php echo htmlspecialchars($item['quantity']); ?></li>
            <?php endforeach; ?>
        </ul>
        <button onclick="event.stopPropagation(); confirmDelivery(<?php echo htmlspecialchars($commandeId); ?>)">Confirmer la livraison</button>
    </div>

    <?php
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'confirmDelivery' && isset($_GET['id'])) {
    $commandeId = $_GET['id'];
    try {
        $manager->updateStatut($commandeId, 'livrée');
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi des commandes > En cours</title>
    <link rel="stylesheet" href="./style/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DynaPuff:wght@400..700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="main">
        <div class="navbar">
            <a href="index.php">Ajout de commande</a>
            <a href="carte/index.html">Carte</a>
        </div>
        <div class="header">
            <div class="title">
                <h1 id="page-title"></h1>
            </div>
            <div class="hour" id="current-time"></div>
        </div>
        <div class="view">
            <div class="list">
                <?php if (empty($commandeIds)): ?>
                    <div class="list-number">
                        <h2 style="background-color: white; border-radius: 10px; padding:15px; width:85%; display:flex; justify-content:center; margin:20px;">Aucune commande en préparation</h2>
                    </div>
                <?php else: ?>
                    <?php foreach ($commandeIds as $commandeId): ?>
                        <div class="list-number" data-id="<?php echo htmlspecialchars($commandeId); ?>" onclick="logCommandeId(<?php echo htmlspecialchars($commandeId); ?>)">
                            <h2 style="background-color: white; border-radius: 10px; padding:15px; width:85%; display:flex; justify-content:center; margin:20px;">Commande # <?php echo htmlspecialchars($commandeId); ?></h2>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="details"></div>
        </div>
    </div>

    <script>
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('current-time').textContent = `${hours}:${minutes}`;
        }

        function logCommandeId(commandeId) {
            const url = `commande.php?action=getDetails&id=${commandeId}`;

            fetch(url)
                .then(response => response.text())
                .then(data => {
                    const detailsDiv = document.querySelector('.details');
                    detailsDiv.innerHTML = data;
                })
                .catch(error => console.error('Erreur:', error));
        }

        function confirmDelivery(commandeId) {
            const url = `commande.php?action=confirmDelivery&id=${commandeId}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(`.list-number[data-id="${commandeId}"]`).remove();
                    } else {
                        alert('Erreur lors de la confirmation de la livraison.');
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }

        function updateTitle() {
            const title = document.title;
            document.getElementById('page-title').textContent = title;
        }

        document.addEventListener('DOMContentLoaded', () => {
            setInterval(updateTime, 1000);
            updateTime();
            updateTitle();
        });
    </script>
</body>
</html>