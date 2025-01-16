<?php
require_once 'CommandeManager.php';

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
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi des commandes > En cours</title>
    <link rel="stylesheet" href="style.css">
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
                            <div class="list-number">
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