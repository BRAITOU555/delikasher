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
  die("Erreur de connexion : " . $e->getMessage());
}

// Récupère le statut du restaurant (actif ou non)
$stmt = $pdo->prepare("SELECT actif FROM restaurants WHERE id = ?");
$stmt->execute([$restaurant_id]);
$restaurant = $stmt->fetch();
$actif = $restaurant['actif'];

// Gestion du bouton d'activation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_activation'])) {
  $new_statut = $actif ? 0 : 1;
  $update = $pdo->prepare("UPDATE restaurants SET actif = ? WHERE id = ?");
  $update->execute([$new_statut, $restaurant_id]);
  header("Location: dashboard.php"); // Recharge la page pour mettre à jour l'affichage
  exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard - <?php echo htmlspecialchars($restaurant_nom); ?></title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      color: #333;
    }

    header {
      background-color: #0b1b3f;
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      margin: 0;
      font-size: 1.4rem;
    }

    .container {
      max-width: 900px;
      margin: 2rem auto;
      background-color: white;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .container h2 {
      color: #0b1b3f;
    }

    .actions {
      display: flex;
      flex-wrap: wrap;
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .action-box {
      flex: 1 1 250px;
      padding: 1.5rem;
      background-color: #f1f1f1;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 0 4px rgba(0,0,0,0.05);
      transition: 0.2s;
    }

    .action-box:hover {
      background-color: #eaeaea;
      transform: scale(1.02);
    }

    .action-box a, .action-box form button {
      text-decoration: none;
      color: #0b1b3f;
      font-weight: bold;
      background: none;
      border: none;
      cursor: pointer;
      font-size: 1rem;
    }

    .status {
      text-align: center;
      margin-top: 1rem;
      font-weight: bold;
      color: <?= $actif ? 'green' : 'red'; ?>;
    }

    footer {
      text-align: center;
      margin-top: 3rem;
      padding: 1rem;
      color: #666;
    }
  </style>
</head>
<body>

<header>
  <h1>Tableau de bord - <?php echo htmlspecialchars($restaurant_nom); ?></h1>
  <div>
    <a href="deconnexion.php" style="color: #fff; text-decoration: underline;">Déconnexion</a>
  </div>
</header>


<div class="container">
  <h2>Bienvenue, <?php echo htmlspecialchars($restaurant_nom); ?> 👋</h2>

  <div class="status">
    Statut du restaurant : <?= $actif ? "✅ Actif (visible sur l'accueil)" : "⛔ Inactif (non visible)" ?>
  </div>

  <div class="actions">
    <div class="action-box">
      <a href="ajouter-categorie.php">📁 Créer une catégorie</a>
    </div>
    <div class="action-box">
      <a href="ajouter-plat.php">🍽️ Ajouter un plat</a>
    </div>
    <div class="action-box">
    <a href="mon-menu.php">📋 Voir mon menu</a>

    </div>
    <div class="action-box">
      <form method="POST">
        <input type="hidden" name="toggle_activation" value="1">
        <button type="submit">
          <?= $actif ? "❌ Désactiver mon restaurant" : "✅ Activer mon restaurant" ?>
        </button>
      </form>
    </div>
  </div>
</div>

<footer>
  &copy; 2025 Delikasher - Tous droits réservés
</footer>

</body>
</html>
