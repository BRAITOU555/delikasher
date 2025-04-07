<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion_utilisateur.php");
    exit;
}

$host = 'localhost';
$dbname = 'delikasher_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$utilisateur_id = $_SESSION['utilisateur_id'];

// Traitement du formulaire de commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restaurant_id'], $_POST['adresse_id'], $_POST['plats'])) {
    $restaurant_id = (int)$_POST['restaurant_id'];
    $adresse_id = (int)$_POST['adresse_id'];
    $plats = $_POST['plats']; // tableau de plat_id

    $stmt = $pdo->prepare("INSERT INTO commandes (utilisateur_id, restaurant_id, adresse_id) VALUES (?, ?, ?)");
    $stmt->execute([$utilisateur_id, $restaurant_id, $adresse_id]);
    $commande_id = $pdo->lastInsertId();

    $stmtPlat = $pdo->prepare("INSERT INTO commandes_plats (commande_id, plat_id) VALUES (?, ?)");
    foreach ($plats as $plat_id) {
        $stmtPlat->execute([$commande_id, (int)$plat_id]);
    }

    echo "<p style='color: green; text-align:center;'>Commande enregistrée avec succès !</p>";
}

// Récupérer les restaurants actifs
$stmt = $pdo->query("SELECT id, nom FROM restaurants WHERE actif = 1");
$restaurants = $stmt->fetchAll();

// Récupérer les adresses de l'utilisateur
$stmt = $pdo->prepare("SELECT id, nom_adresse FROM adresses_utilisateur WHERE utilisateur_id = ?");
$stmt->execute([$utilisateur_id]);
$adresses = $stmt->fetchAll();

// Récupérer les plats si un restaurant est sélectionné
$plats = [];
if (isset($_POST['restaurant_id']) || isset($_GET['restaurant_id'])) {
    $selected_restaurant = $_POST['restaurant_id'] ?? $_GET['restaurant_id'];
    $stmt = $pdo->prepare("SELECT id, nom, description, prix FROM plats WHERE categorie_id IN (SELECT id FROM categories WHERE restaurant_id = ?)");
    $stmt->execute([$selected_restaurant]);
    $plats = $stmt->fetchAll();
}

// Historique des commandes de l'utilisateur
$stmt = $pdo->prepare("SELECT c.id, c.date_commande, r.nom AS resto_nom, a.nom_adresse
                       FROM commandes c
                       JOIN restaurants r ON c.restaurant_id = r.id
                       JOIN adresses_utilisateur a ON c.adresse_id = a.id
                       WHERE c.utilisateur_id = ?
                       ORDER BY c.date_commande DESC");
$stmt->execute([$utilisateur_id]);
$commandes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Passer une commande</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 2rem; }
        .container { max-width: 800px; margin: auto; background: white; padding: 2rem; border-radius: 10px; }
        h2, h3 { text-align: center; }
        select, button { width: 100%; padding: 0.8rem; margin: 1rem 0; border-radius: 8px; border: 1px solid #ccc; }
        button { background-color: #06c167; color: white; font-weight: bold; border: none; cursor: pointer; }
        button:hover { background-color: #049d4e; }
        .plats { margin-top: 2rem; }
        .plat { padding: 0.5rem; border-bottom: 1px solid #eee; }
        .historique { margin-top: 3rem; background: #f9f9f9; padding: 1rem; border-radius: 10px; }
        .commande { border-bottom: 1px solid #ddd; padding: 1rem 0; }
    </style>
</head>
<body>
<div class="container">
    <h2>Passer une commande</h2>
    <form method="POST">
        <label for="restaurant_id">Choisissez un restaurant :</label>
        <select name="restaurant_id" onchange="this.form.submit()" required>
            <option value="">-- Sélectionnez --</option>
            <?php foreach ($restaurants as $resto): ?>
                <option value="<?= $resto['id'] ?>" <?= (isset($selected_restaurant) && $selected_restaurant == $resto['id']) ? 'selected' : '' ?>><?= htmlspecialchars($resto['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="adresse_id">Choisissez votre adresse de livraison :</label>
        <select name="adresse_id" required>
            <option value="">-- Sélectionnez --</option>
            <?php foreach ($adresses as $adr): ?>
                <option value="<?= $adr['id'] ?>"><?= htmlspecialchars($adr['nom_adresse']) ?></option>
            <?php endforeach; ?>
        </select>

        <?php if (!empty($plats)): ?>
        <div class="plats">
            <h3>Sélectionnez vos plats :</h3>
            <?php foreach ($plats as $plat): ?>
                <div class="plat">
                    <label>
                        <input type="checkbox" name="plats[]" value="<?= $plat['id'] ?>">
                        <strong><?= htmlspecialchars($plat['nom']) ?></strong> - <?= htmlspecialchars($plat['prix']) ?> €<br>
                        <small><?= htmlspecialchars($plat['description']) ?></small>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($plats)): ?>
            <button type="submit">Valider la commande</button>
        <?php endif; ?>
    </form>

    <div class="historique">
        <h3>📦 Mon historique de commandes</h3>
        <?php if (count($commandes) > 0): ?>
            <?php foreach ($commandes as $cmd): ?>
                <div class="commande">
                    <p><strong>Commande #<?= $cmd['id'] ?></strong></p>
                    <p>🕒 <?= date('d/m/Y H:i', strtotime($cmd['date_commande'])) ?></p>
                    <p>🍽️ Restaurant : <?= htmlspecialchars($cmd['resto_nom']) ?></p>
                    <p>📍 Livraison : <?= htmlspecialchars($cmd['nom_adresse']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune commande enregistrée pour le moment.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
