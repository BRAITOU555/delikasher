<?php
session_start();
if (!isset($_SESSION['restaurant_id'])) {
  header("Location: connexion-restaurant.html");
  exit();
}

$restaurant_id = $_SESSION['restaurant_id'];
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

// Gestion de l'ajout de catégorie
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_categorie'])) {
  $nom_categorie = trim($_POST['nom_categorie']);
  if (!empty($nom_categorie)) {
    // Vérifier les doublons
    $verif = $pdo->prepare("SELECT id FROM categories WHERE nom = ? AND restaurant_id = ?");
    $verif->execute([$nom_categorie, $restaurant_id]);
    if ($verif->rowCount() > 0) {
      $message = "❌ Cette catégorie existe déjà.";
    } else {
      $stmt = $pdo->prepare("INSERT INTO categories (restaurant_id, nom) VALUES (?, ?)");
      $stmt->execute([$restaurant_id, $nom_categorie]);
      $message = "✅ Catégorie ajoutée avec succès.";
    }
  } else {
    $message = "❌ Le nom de la catégorie est requis.";
  }
}

// Récupérer les catégories du restaurant
$stmt = $pdo->prepare("SELECT id, nom FROM categories WHERE restaurant_id = ? ORDER BY nom");
$stmt->execute([$restaurant_id]);
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ajouter une catégorie</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
    .top-menu { background-color: #0b1b3f; text-align: right; padding: 1rem; }
    .top-menu a { color: white; text-decoration: none; margin-left: 1rem; font-weight: bold; }
    .container { max-width: 700px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
    h2 { text-align: center; color: #0b1b3f; }
    label, input, button { display: block; width: 100%; margin-top: 1rem; }
    input[type="text"] { padding: 0.5rem; border-radius: 5px; border: 1px solid #ccc; }
    button { padding: 0.7rem; background-color: #0b1b3f; color: white; border: none; border-radius: 8px; cursor: pointer; margin-top: 1.5rem; }
    .message { text-align: center; font-weight: bold; margin-top: 1rem; color: #0b1b3f; }
    ul { margin-top: 2rem; padding-left: 1rem; }
    li { margin: 0.3rem 0; list-style-type: disc; }
  </style>
</head>
<body>
<div class="top-menu">
  <a href="index.php">🏠 Accueil</a>
  <a href="dashboard.php">📋 Dashboard</a>
  <a href="deconnexion.php">🚪 Déconnexion</a>
</div>

<div class="container">
  <h2>Ajouter une catégorie</h2>
  <?php if (!empty($message)): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label for="nom_categorie">Nom de la catégorie *</label>
    <input type="text" name="nom_categorie" id="nom_categorie" required>
    <button type="submit">Ajouter</button>
  </form>

  <h3>Catégories existantes</h3>
  <ul>
    <?php foreach ($categories as $cat): ?>
      <li><?= htmlspecialchars($cat['nom']) ?></li>
    <?php endforeach; ?>
  </ul>
</div>
</body>
</html>
