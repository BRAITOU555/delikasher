<?php
session_start();

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

$resto_id = $_GET['resto_id'] ?? null;

if (!$resto_id) {
  die("Aucun restaurant sélectionné.");
}

// Vérifie que le restaurant est actif
$resto_stmt = $pdo->prepare("SELECT nom FROM restaurants WHERE id = ? AND actif = 1");
$resto_stmt->execute([$resto_id]);
$restaurant = $resto_stmt->fetch();

if (!$restaurant) {
  die("Restaurant introuvable ou inactif.");
}

// Récupération des catégories
$cat_stmt = $pdo->prepare("SELECT id, nom FROM categories WHERE restaurant_id = ?");
$cat_stmt->execute([$resto_id]);
$categories = $cat_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Menu - <?= htmlspecialchars($restaurant['nom']) ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
    }

    header {
      background-color: #0b1b3f;
      color: white;
      padding: 1rem;
      position: relative;
    }

    .top-menu {
      text-align: right;
      margin-bottom: 0.5rem;
    }

    .top-menu a {
      color: white;
      text-decoration: none;
      margin-left: 1rem;
      font-weight: bold;
    }

    .top-menu a:hover {
      text-decoration: underline;
    }

    header h1 {
      margin: 0;
      text-align: center;
      font-size: 1.8rem;
    }

    .container {
      max-width: 900px;
      margin: 2rem auto;
      background-color: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    h2 {
      color: #0b1b3f;
      margin-bottom: 1rem;
    }

    .category {
      margin-bottom: 2rem;
    }

    .plat {
      padding: 0.7rem 1rem;
      border-bottom: 1px solid #ddd;
    }

    .plat h4 {
      margin: 0;
      color: #333;
    }

    .plat p {
      margin: 0.3rem 0;
      font-size: 0.9rem;
      color: #555;
    }

    .price {
      font-weight: bold;
      color: #0b1b3f;
    }

    footer {
      background-color: #0b1b3f;
      color: white;
      text-align: center;
      padding: 1.5rem;
      margin-top: 3rem;
    }

    @media (max-width: 768px) {
      header h1 {
        font-size: 1.2rem;
      }

      .top-menu {
        text-align: center;
      }
    }
  </style>
</head>
<body>

<header>
  <div class="top-menu">
    <a href="index.php">🏡 Accueil</a>
    <?php if (isset($_SESSION['restaurant_id'])): ?>
      <a href="dashboard.php">📋 Dashboard</a>
      <a href="deconnexion.php">🚪 Déconnexion</a>
    <?php endif; ?>
  </div>
  <h1>Menu - <?= htmlspecialchars($restaurant['nom']) ?></h1>
</header>

<div class="container">
  <?php if (empty($categories)): ?>
    <p>Aucune catégorie disponible pour ce restaurant.</p>
  <?php else: ?>
    <?php foreach ($categories as $cat): ?>
      <div class="category">
        <h2><?= htmlspecialchars($cat['nom']) ?></h2>

        <?php
        $plats_stmt = $pdo->prepare("SELECT nom, description, prix FROM plats WHERE categorie_id = ?");
        $plats_stmt->execute([$cat['id']]);
        $plats = $plats_stmt->fetchAll();

        if (empty($plats)): ?>
          <p><em>Aucun plat dans cette catégorie.</em></p>
        <?php else: ?>
          <?php foreach ($plats as $plat): ?>
            <div class="plat">
              <h4><?= htmlspecialchars($plat['nom']) ?></h4>
              <?php if (!empty($plat['description'])): ?>
                <p><?= htmlspecialchars($plat['description']) ?></p>
              <?php endif; ?>
              <p class="price"><?= number_format($plat['prix'], 2) ?> €</p>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<footer>
  &copy; 2025 Delikasher - Tous droits réservés
</footer>

</body>
</html>
