<?php
require_once 'Manager/CommandeManager.php';

// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=sae301;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // créer de CommandeManager
    $manager = new CommandeManager($pdo);

    // id de toutes les commandes en cours
    $commandeIds = $manager->getAllCommandeIds();
} catch (Exception $e) {
    echo 'Erreur de connexion à la base de données : ' . $e->getMessage();
    exit;
}

// Vérifier si c'est une requête AJAX pour obtenir les détails de la commande
if (isset($_GET['action']) && $_GET['action'] === 'getDetails' && isset($_GET['id'])) {
    $commandeId = $_GET['id'];
    $details = $manager->getCommandeDetails($commandeId);

    $commande = $details['commande'];
    $items = $details['items'];
    ?>

    <h3>Détails de la commande #<?php echo htmlspecialchars($commandeId); ?></h3>
    <p>ID de la commande: <?php echo htmlspecialchars($commande['id']); ?></p>
    <p>Date de création: <?php echo htmlspecialchars($commande['creation']); ?></p>
    <h4>Items:</h4>
    <ul>
        <?php foreach ($items as $item): ?>
            <li>Produit: <?php echo htmlspecialchars($item['product_id']); ?>, Quantité: <?php echo htmlspecialchars($item['quantity']); ?></li>
        <?php endforeach; ?>
    </ul>

    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi des commandes > En cours</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="main">
        <div class="navbar">
            <a href="index.php">Ajout de commande</a>
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
                        <div class="list-number" onclick="logCommandeId(<?php echo htmlspecialchars($commandeId); ?>)">
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
            const seconds = String(now.getSeconds()).padStart(2, '0');
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

        function updateTitle() {
            const title = document.title;
            document.getElementById('page-title').textContent = title;
        }

        setInterval(updateTime, 1000);
        updateTime();
        updateTitle();
    </script>
</body>
</html>