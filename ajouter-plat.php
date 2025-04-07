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

// Récupérer les catégories du restaurant
$stmt = $pdo->prepare("SELECT id, nom FROM categories WHERE restaurant_id = ? ORDER BY nom");
$stmt->execute([$restaurant_id]);
$categories = $stmt->fetchAll();

// Récupérer les ingrédients groupés par famille
$stmt = $pdo->prepare("SELECT * FROM ingredients ORDER BY categorie ASC, nom ASC");
$stmt->execute();
$ingredients_par_famille = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $ingredients_par_famille[$row['categorie']][] = $row;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $categorie_id = $_POST['categorie_id'] ?? '';
  $nom = trim($_POST['nom'] ?? '');
  $prix = $_POST['prix'] ?? '';
  $ingredients = $_POST['ingredients'] ?? [];
  $ingredients_perso = $_POST['ingredients_perso'] ?? [];

  // Nettoyer les ingrédients personnalisés
  foreach ($ingredients_perso as $perso) {
    if (!empty(trim($perso))) {
      $ingredients[] = trim($perso);
    }
  }

  if (!empty($categorie_id) && !empty($nom) && !empty($prix)) {
    $description = implode(", ", $ingredients);

    $stmt = $pdo->prepare("INSERT INTO plats (categorie_id, nom, description, prix) VALUES (?, ?, ?, ?)");
    $stmt->execute([$categorie_id, $nom, $description, $prix]);

    $message = "✅ Plat ajouté avec succès.";
  } else {
    $message = "❌ Merci de remplir tous les champs obligatoires.";
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ajouter un plat</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
    .top-menu { background-color: #0b1b3f; padding: 1rem; text-align: right; }
    .top-menu a { color: white; text-decoration: none; margin-left: 1rem; font-weight: bold; }
    .container { max-width: 800px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    h2 { text-align: center; color: #0b1b3f; }
    label { display: block; margin-top: 1rem; font-weight: bold; }
    select, input[type="text"], input[type="number"] {
      width: 100%; padding: 0.5rem; margin-top: 0.3rem; border-radius: 5px; border: 1px solid #ccc;
    }
    .checkbox-group h4 { margin-top: 1.5rem; color: #0b1b3f; }
    .checkbox-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.4rem; margin-top: 0.5rem; }
    .ingredient-plus { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
    .ingredient-plus input { flex: 1; }
    .btn-add { background-color: #ddd; border: none; padding: 0.4rem 0.8rem; border-radius: 5px; cursor: pointer; }
    button[type="submit"] {
      margin-top: 2rem; width: 100%; padding: 0.8rem;
      background-color: #0b1b3f; color: white; border: none; border-radius: 8px; font-size: 1rem;
    }
    .message { text-align: center; font-weight: bold; margin-top: 1rem; color: green; }
  </style>
</head>
<body>

<div class="top-menu">
  <a href="index.php">🏠 Accueil</a>
  <a href="dashboard.php">📋 Dashboard</a>
  <a href="deconnexion.php">🚪 Déconnexion</a>
</div>

<div class="container">
  <h2>Ajouter un plat</h2>
  <?php if (!empty($message)): ?><div class="message"><?= htmlspecialchars($message) ?></div><?php endif; ?>

  <form method="POST">
    <label>Catégorie *</label>
    <select name="categorie_id" required>
      <option value="">-- Choisir une catégorie --</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Nom du plat *</label>
    <input type="text" name="nom" required>

    <label>Ingrédients disponibles</label>
    <?php foreach ($ingredients_par_famille as $famille => $liste): ?>
      <div class="checkbox-group">
        <h4><?= htmlspecialchars($famille) ?></h4>
        <div class="checkbox-grid">
          <?php foreach ($liste as $ing): ?>
            <label><input type="checkbox" name="ingredients[]" value="<?= htmlspecialchars($ing['nom']) ?>"> <?= htmlspecialchars($ing['nom']) ?></label>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <label>Ingrédients supplémentaires (facultatif)</label>
    <div id="extra-ingredients">
      <div class="ingredient-plus">
        <input type="text" name="ingredients_perso[]" placeholder="Ex: Roquette, Harissa...">
        <button type="button" class="btn-add" onclick="addIngredient()">+</button>
      </div>
    </div>

    <label>Prix (€) *</label>
    <input type="number" name="prix" step="0.01" min="0" required>

    <button type="submit">Ajouter le plat</button>
  </form>
</div>

<script>
function addIngredient() {
  const container = document.getElementById('extra-ingredients');
  const div = document.createElement('div');
  div.className = 'ingredient-plus';
  div.innerHTML = `<input type="text" name="ingredients_perso[]" placeholder="Ingrédient supplémentaire"><button type="button" class="btn-add" onclick="this.parentElement.remove()">-</button>`;
  container.appendChild(div);
}
</script>

</body>
</html>
