<?php
// Connexion à la base
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

// Récupération des restaurants actifs
$stmt = $pdo->prepare("SELECT id, nom, specialite FROM restaurants WHERE actif = 1");
$stmt->execute();
$restaurants = $stmt->fetchAll();

// Extraire les spécialités uniques
$specialites = array_unique(array_map(function ($r) {
    return $r['specialite'];
}, $restaurants));
sort($specialites);

session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Accueil - Delikasher</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root {
      --black: #1e1e1e;
      --green: #06c167;
      --white: #ffffff;
      --gray-dark: #333333;
      --gray-light: #eeeeee;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: var(--white);
      color: var(--black);
    }

    header {
      background-color: var(--black);
      color: var(--white);
      padding: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
    }

    .logo {
      font-size: 1.5rem;
      font-weight: bold;
    }

    .search-bar {
      flex: 1;
      margin: 0 1rem;
    }

    .search-bar input {
      width: 100%;
      max-width: 300px;
      padding: 0.5rem 1rem;
      border-radius: 30px;
      border: none;
      font-size: 1rem;
    }

    .icons {
      display: flex;
      gap: 1rem;
      font-size: 1.4rem;
    }

    .icons a {
      color: var(--white);
      text-decoration: none;
    }

    .hamburger {
      display: none;
      flex-direction: column;
      gap: 5px;
      cursor: pointer;
    }

    .hamburger div {
      width: 25px;
      height: 3px;
      background: white;
    }

    .mobile-menu {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      background: var(--black);
      width: 100%;
      flex-direction: column;
      padding: 1rem;
      z-index: 10;
    }

    .mobile-menu a {
      color: white;
      text-decoration: none;
      padding: 0.5rem 0;
    }

    .specialites {
      display: flex;
      overflow-x: auto;
      padding: 1rem;
      gap: 1rem;
      background-color: var(--gray-light);
    }

    .specialite {
      flex: 0 0 auto;
      background-color: var(--white);
      border: 1px solid var(--gray-dark);
      padding: 0.6rem 0.8rem;
      border-radius: 25px;
      text-align: center;
      font-weight: bold;
      color: var(--black);
      text-decoration: none;
      transition: background-color 0.3s;
      min-width: 70px;
      line-height: 1.2;
      font-size: 0.85rem;
    }

    .specialite:hover {
      background-color: var(--green);
      color: var(--white);
    }

    .section-title {
      padding: 1.5rem 1rem 0.5rem;
      font-size: 1.2rem;
      font-weight: bold;
    }

    .resto-list {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 1rem;
      padding: 0 1rem 2rem;
    }

    .resto-card {
      background-color: var(--white);
      border: 1px solid var(--gray-light);
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.03);
      text-align: center;
      width: 220px;
      transition: transform 0.3s;
      text-decoration: none;
      color: inherit;
      padding: 1rem 0.5rem;
    }

    .resto-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }

    .resto-card h4 {
      margin: 0.5rem 0 0.2rem;
      font-size: 1rem;
      color: var(--black);
    }

    .resto-card p {
      font-size: 0.85rem;
      margin: 0;
      color: var(--gray-dark);
    }

    footer {
      background-color: var(--black);
      color: var(--white);
      text-align: center;
      padding: 1rem;
    }

    @media (max-width: 768px) {
      .icons { display: none; }
      .hamburger { display: flex; }
      .mobile-menu.active { display: flex; }
    }
  </style>
</head>
<body>
<header>
  <div class="logo">Delikasher</div>
  <div class="search-bar">
    <input type="text" placeholder="Rechercher un plat ou un restaurant...">
  </div>
  <div class="icons">
    <?php if (isset($_SESSION['utilisateur_id'])): ?>
      <a href="dashboard_utilisateur.php">👤 <?= htmlspecialchars(explode(' ', $_SESSION['utilisateur_nom'])[0]) ?></a>
    <?php else: ?>
      <a href="connexion_utilisateur.php">Connexion</a>
      <a href="inscription_utilisateur.php">Inscription</a>
    <?php endif; ?>
    <a href="panier.php">🛒</a>
  </div>
  <div class="hamburger" onclick="toggleMenu()">
    <div></div>
    <div></div>
    <div></div>
  </div>
</header>
<div class="mobile-menu" id="mobileMenu">
  <?php if (isset($_SESSION['utilisateur_id'])): ?>
    <a href="dashboard_utilisateur.php">👤 <?= htmlspecialchars(explode(' ', $_SESSION['utilisateur_nom'])[0]) ?></a>
  <?php else: ?>
    <a href="connexion_utilisateur.php">Connexion</a>
    <a href="inscription_utilisateur.php">Inscription</a>
  <?php endif; ?>
  <a href="panier.php">🛒 Panier</a>
</div>

<!-- Spécialités avec icônes emojis -->
<div class="specialites">
  <a class="specialite" href="specialite.php?type=Pizza">🍕<br>Pizza</a>
  <a class="specialite" href="specialite.php?type=Hamburger">🍔<br>Hamburger</a>
  <a class="specialite" href="specialite.php?type=Japonais">🍣<br>Japonais</a>
  <a class="specialite" href="specialite.php?type=Salade">🥗<br>Salades</a>
  <a class="specialite" href="specialite.php?type=Chinois">🍜<br>Chinois</a>
</div>

<!-- Restaurants -->
<div class="section-title">Nos restaurants disponibles</div>
<div class="resto-list">
  <?php foreach ($restaurants as $resto): ?>
    <a class="resto-card" href="voir-menu.php?restaurant_id=<?= $resto['id'] ?>">
      <h4><?= htmlspecialchars($resto['nom']) ?></h4>
      <p><?= htmlspecialchars($resto['specialite']) ?> • ⭐ <?= rand(4, 5) ?>.<?= rand(0, 9) ?></p>
    </a>
  <?php endforeach; ?>
</div>

<footer>
  &copy; 2025 Delikasher - Tous droits réservés
</footer>

<script>
  function toggleMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('active');
  }
</script>
</body>
</html>
