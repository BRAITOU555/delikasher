<?php
session_start();
if (!isset($_SESSION['restaurant_id'])) {
  header("Location: connexion-restaurant.html");
  exit();
}

$restaurant_id = $_SESSION['restaurant_id'];
$restaurant_nom = $_SESSION['restaurant_nom'];

$host = 'localhost';
$dbname = 'delikasher_db';
$user = 'root';
$pass = '';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Erreur : " . $e->getMessage());
}

$cat_stmt = $pdo->prepare("SELECT id, nom FROM categories WHERE restaurant_id = ?");
$cat_stmt->execute([$restaurant_id]);
$categories = $cat_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mon menu - <?= htmlspecialchars($restaurant_nom) ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background-color: #f4f4f4;
    }

    header {
      background-color: #0b1b3f;
      color: white;
      padding: 1rem;
      text-align: center;
    }

    .top-menu {
      background-color: #0b1b3f;
      text-align: right;
      padding: 0.5rem 1rem;
    }

    .top-menu a {
      color: white;
      text-decoration: none;
      margin-left: 1rem;
      font-weight: bold;
    }

    .container {
      max-width: 900px;
      margin: 2rem auto;
      background-color: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .accordion {
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 6px;
      overflow: hidden;
    }

    .accordion-button {
      background-color: #eaeaea;
      color: #0b1b3f;
      cursor: pointer;
      padding: 1rem;
      width: 100%;
      border: none;
      text-align: left;
      font-size: 1.1rem;
      font-weight: bold;
      transition: 0.3s;
    }

    .accordion-button:hover {
      background-color: #ddd;
    }

    .accordion-content {
      display: none;
      padding: 1rem;
      border-top: 1px solid #ccc;
    }

    .plat {
      margin-bottom: 1.5rem;
      padding: 0.5rem;
      background-color: #f9f9f9;
      border-radius: 5px;
    }

    .plat h4 {
      margin: 0 0 0.3rem 0;
      color: #333;
    }

    .plat p {
      margin: 0.2rem 0;
      font-size: 0.95rem;
    }

    .price {
      font-weight: bold;
      color: #0b1b3f;
    }

    .btn-edit, .btn-delete {
      display: inline-block;
      margin-top: 0.5rem;
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      font-size: 0.9rem;
      text-decoration: none;
      color: white;
    }

    .btn-edit {
      background-color: #007bff;
      margin-right: 0.5rem;
    }

    .btn-edit:hover {
      background-color: #0056b3;
    }

    .btn-delete {
      background-color: #dc3545;
    }

    .btn-delete:hover {
      background-color: #a71d2a;
    }

    footer {
      background-color: #0b1b3f;
      color: white;
      text-align: center;
      padding: 1.5rem;
      margin-top: 3rem;
    }
  </style>
</head>
<body>

<div class="top-menu">
  <a href="dashboard.php">📋 Dashboard</a>
  <a href="deconnexion.php">🚪 Déconnexion</a>
</div>

<header>
  <h1>Mon menu - <?= htmlspecialchars($restaurant_nom) ?></h1>
</header>

<div class="container">
  <?php if (empty($categories)): ?>
    <p>Aucune catégorie trouvée.</p>
  <?php else: ?>
    <?php foreach ($categories as $cat): ?>
      <div class="accordion">
        <button class="accordion-button"><?= htmlspecialchars($cat['nom']) ?></button>
        <div class="accordion-content">
          <?php
            $plats_stmt = $pdo->prepare("SELECT id, nom, description, prix FROM plats WHERE categorie_id = ?");
            $plats_stmt->execute([$cat['id']]);
            $plats = $plats_stmt->fetchAll();

            if (empty($plats)) {
              echo "<p><em>Aucun plat dans cette catégorie.</em></p>";
            } else {
              foreach ($plats as $plat): ?>
                <div class="plat">
                  <h4><?= htmlspecialchars($plat['nom']) ?></h4>
                  <?php if (!empty($plat['description'])): ?>
                    <p><?= htmlspecialchars($plat['description']) ?></p>
                  <?php endif; ?>
                  <p class="price"><?= number_format($plat['prix'], 2) ?> €</p>
                  <a class="btn-edit" href="modifier-plat.php?id=<?= $plat['id'] ?>">🖊 Modifier</a>
                  <a class="btn-delete" href="supprimer-plat.php?id=<?= $plat['id'] ?>" onclick="return confirm('Supprimer ce plat ?')">🗑 Supprimer</a>
                </div>
          <?php endforeach; } ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<footer>
  &copy; 2025 Delikasher - Tous droits réservés
</footer>

<script>
  const buttons = document.querySelectorAll('.accordion-button');
  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const content = btn.nextElementSibling;
      content.style.display = content.style.display === 'block' ? 'none' : 'block';
    });
  });
</script>

</body>
</html>
