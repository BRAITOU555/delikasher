<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Connexion à la base
$host = 'localhost';
$dbname = 'delikasher_db';
$user = 'root';
$pass = '';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Erreur de connexion à la base : " . $e->getMessage());
}

// Récupère les restaurants actifs
$stmt = $pdo->prepare("SELECT id, nom FROM restaurants WHERE actif = 1 ORDER BY nom ASC");
$stmt->execute();
$restaurants = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Delikasher - Accueil</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
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
      font-size: 1.5rem;
    }

    nav a {
      color: white;
      text-decoration: none;
      margin-left: 1.5rem;
      font-weight: bold;
    }

    .hero {
      background: url('https://images.unsplash.com/photo-1606788075764-0a7c04b3fa9e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1500&q=80') center/cover no-repeat;
      height: 60vh;
      display: flex;
      justify-content: center;
      align-items: center;
      color: white;
      text-align: center;
    }

    .hero h2 {
      font-size: 2.5rem;
      background-color: rgba(0, 0, 0, 0.5);
      padding: 1rem;
      border-radius: 10px;
    }

    .restaurants {
      max-width: 900px;
      margin: 2rem auto;
      padding: 1rem;
    }

    .restaurant-card {
      background-color: white;
      border-radius: 10px;
      padding: 1rem 1.5rem;
      margin-bottom: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 0 8px rgba(0,0,0,0.05);
    }

    .restaurant-card h3 {
      margin: 0;
    }

    .restaurant-card a {
      background-color: #0b1b3f;
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      text-decoration: none;
    }

    footer {
      background-color: #0b1b3f;
      color: white;
      text-align: center;
      padding: 2rem;
      margin-top: 3rem;
    }

    @media (max-width: 768px) {
      .restaurant-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }

      .restaurant-card a {
        align-self: flex-end;
      }
    }
  </style>
</head>
<body>

<header>
  <h1>Delikasher</h1>
  <nav>
    <?php if (isset($_SESSION['restaurant_id'])): ?>
      <span>Bienvenue, <?php echo htmlspecialchars($_SESSION['restaurant_nom']); ?></span>
      <a href="dashboard.php">Dashboard</a>
      <a href="deconnexion.php">Déconnexion</a>
    <?php else: ?>
      <a href="index.php">Accueil</a>
      <a href="connexion-restaurant.html">Connexion</a>
      <a href="inscription-restaurant.html">Inscription</a>
    <?php endif; ?>
  </nav>
</header>

<section class="hero">
  <h2>Découvrez les meilleurs restaurants casher près de chez vous</h2>
</section>

<section class="restaurants">
  <h2>Restaurants actifs</h2>

  <?php if (empty($restaurants)): ?>
    <p>Aucun restaurant n’est encore activé.</p>
  <?php else: ?>
    <?php foreach ($restaurants as $resto): ?>
      <div class="restaurant-card">
        <h3><?= htmlspecialchars($resto['nom']) ?></h3>
        <a href="menu.php?resto_id=<?= $resto['id'] ?>">Voir le menu</a>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</section>

<footer>
  &copy; 2025 Delikasher - Tous droits réservés
</footer>

</body>
</html>
