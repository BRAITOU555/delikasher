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

// Récupérer tous les ingrédients de la base
$stmt = $pdo->prepare("SELECT nom, categorie FROM ingredients ORDER BY categorie, nom");
$stmt->execute();
$ingredients_groupes = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $ingredients_groupes[$row['categorie']][] = $row['nom'];
}

// Récupérer le plat
$id = $_GET['id'] ?? null;
if (!$id) die("Aucun plat sélectionné.");

$stmt = $pdo->prepare("SELECT * FROM plats WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$plat = $stmt->fetch();
if (!$plat) die("Plat introuvable.");

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nom = trim($_POST['nom']);
  $prix = $_POST['prix'];
  $base = $_POST['base'] ?? '';
  $ingredients = $_POST['ingredients'] ?? [];
  $ingredient_perso = trim($_POST['ingredient_perso'] ?? '');

  if (!empty($ingredient_perso)) $ingredients[] = $ingredient_perso;
  if (!empty($base)) array_unshift($ingredients, "Base : $base");

  $description = implode(", ", $ingredients);

  $update = $pdo->prepare("UPDATE plats SET nom = ?, description = ?, prix = ? WHERE id = ?");
  $update->execute([$nom, $description, $prix, $id]);

  $message = "✅ Plat modifié avec succès.";
  echo "<script>setTimeout(() => { window.location.href = 'dashboard.php'; }, 2000);</script>";
}

// Préparer les données pré-cochées
$description_exploded = explode(", ", $plat['description']);
$base_actuelle = "";
$checked_ingredients = [];
foreach ($description_exploded as $ing) {
  if (str_starts_with($ing, "Base :")) {
    $base_actuelle = trim(str_replace("Base :", "", $ing));
  } else {
    $checked_ingredients[] = $ing;
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier un plat</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
    .top-menu { background-color: #0b1b3f; text-align: right; padding: 1rem; }
    .top-menu a { color: white; text-decoration: none; margin-left: 1rem; font-weight: bold; }
    .container { max-width: 900px; margin: 2rem auto; background-color: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
    h2 { text-align: center; color: #0b1b3f; }
    label { font-weight: bold; margin-top: 1rem; display: block; }
    input[type="text"], input[type="number"], select { width: 100%; padding: 0.5rem; margin-top: 0.3rem; border: 1px solid #ccc; border-radius: 5px; }
    .checkbox-group { display: none; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.5rem; margin-top: 1rem; }
    .checkbox-group.active { display: grid; }
    .checkbox-group label { font-weight: normal; }
    .radio-group { display: flex; gap: 1rem; margin-top: 0.5rem; }
    .message { margin-top: 1rem; text-align: center; font-weight: bold; color: green; }
    button { margin-top: 2rem; width: 100%; padding: 0.8rem; background-color: #0b1b3f; color: white; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; }
  </style>
  <script>
    function afficherGroupe() {
      const groupes = document.querySelectorAll('.checkbox-group');
      groupes.forEach(g => g.classList.remove('active'));

      const sel = document.getElementById('categorie_selector').value;
      const actif = document.getElementById('groupe-' + sel);
      if (actif) actif.classList.add('active');
    }
  </script>
</head>
<body>
<div class="top-menu">
  <a href="index.php">🏠 Accueil</a>
  <a href="dashboard.php">📋 Dashboard</a>
  <a href="deconnexion.php">🚪 Déconnexion</a>
</div>

<div class="container">
  <h2>Modifier un plat</h2>
  <?php if (!empty($message)): ?><p class="message"><?= $message ?></p><?php endif; ?>

  <form method="POST">
    <label>Nom du plat *</label>
    <input type="text" name="nom" value="<?= htmlspecialchars($plat['nom']) ?>" required>

    <label>Base (facultatif - Pizza uniquement)</label>
    <div class="radio-group">
      <label><input type="radio" name="base" value="Sauce tomate" <?= $base_actuelle === "Sauce tomate" ? 'checked' : '' ?>> Sauce tomate</label>
      <label><input type="radio" name="base" value="Crème fraîche" <?= $base_actuelle === "Crème fraîche" ? 'checked' : '' ?>> Crème fraîche</label>
    </div>

    <label for="categorie_selector">Afficher les ingrédients par famille</label>
    <select id="categorie_selector" onchange="afficherGroupe()">
      <option value="">-- Choisir une famille --</option>
      <?php foreach ($ingredients_groupes as $cat => $ings): ?>
        <option value="<?= $cat ?>"><?= htmlspecialchars($cat) ?></option>
      <?php endforeach; ?>
    </select>

    <?php foreach ($ingredients_groupes as $cat => $ings): ?>
      <div class="checkbox-group" id="groupe-<?= $cat ?>">
        <?php foreach ($ings as $ing): ?>
          <label><input type="checkbox" name="ingredients[]" value="<?= htmlspecialchars($ing) ?>" <?= in_array($ing, $checked_ingredients) ? 'checked' : '' ?>> <?= htmlspecialchars($ing) ?></label>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <label>Ingrédient personnalisé (facultatif)</label>
    <input type="text" name="ingredient_perso" placeholder="Ex : Harissa, Roquette">

    <label>Prix en euros *</label>
    <input type="number" name="prix" value="<?= $plat['prix'] ?>" step="0.01" min="0" required>

    <button type="submit">Mettre à jour</button>
  </form>
</div>
</body>
</html>
