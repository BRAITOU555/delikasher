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
$nom_complet = $_SESSION['utilisateur_nom'];
$email = $_SESSION['utilisateur_email'];

// Traitement du formulaire d'ajout d'adresse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_adresse'])) {
    $nom_adresse = htmlspecialchars($_POST['nom_adresse']);
    $numero_telephone = htmlspecialchars($_POST['numero_telephone']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $code_postal = htmlspecialchars($_POST['code_postal']);
    $ville = htmlspecialchars($_POST['ville']);

    $stmt = $pdo->prepare("INSERT INTO adresses_utilisateur (utilisateur_id, nom_adresse, numero_telephone, adresse, code_postal, ville) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$utilisateur_id, $nom_adresse, $numero_telephone, $adresse, $code_postal, $ville]);
}

// Traitement de la modification d'une adresse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_adresse'])) {
    $id_modif = (int)$_POST['adresse_id'];
    $nom_adresse = htmlspecialchars($_POST['nom_adresse']);
    $numero_telephone = htmlspecialchars($_POST['numero_telephone']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $code_postal = htmlspecialchars($_POST['code_postal']);
    $ville = htmlspecialchars($_POST['ville']);

    $stmt = $pdo->prepare("UPDATE adresses_utilisateur SET nom_adresse = ?, numero_telephone = ?, adresse = ?, code_postal = ?, ville = ? WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$nom_adresse, $numero_telephone, $adresse, $code_postal, $ville, $id_modif, $utilisateur_id]);
}

// Définir une adresse par défaut
if (isset($_GET['defaut'])) {
    $adresse_defaut_id = (int)$_GET['defaut'];
    $pdo->prepare("UPDATE adresses_utilisateur SET est_defaut = 0 WHERE utilisateur_id = ?")
        ->execute([$utilisateur_id]);
    $pdo->prepare("UPDATE adresses_utilisateur SET est_defaut = 1 WHERE id = ? AND utilisateur_id = ?")
        ->execute([$adresse_defaut_id, $utilisateur_id]);
    header("Location: dashboard_utilisateur.php");
    exit;
}

// Suppression d'une adresse
if (isset($_GET['supprimer_adresse'])) {
    $adresse_id = (int)$_GET['supprimer_adresse'];
    $stmt = $pdo->prepare("DELETE FROM adresses_utilisateur WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$adresse_id, $utilisateur_id]);
    header("Location: dashboard_utilisateur.php");
    exit;
}

// Adresse en cours de modification ?
$adresse_en_modif = isset($_GET['modifier']) ? (int)$_GET['modifier'] : null;

// Récupération des adresses enregistrées
$stmt = $pdo->prepare("SELECT * FROM adresses_utilisateur WHERE utilisateur_id = ? ORDER BY date_enregistrement DESC");
$stmt->execute([$utilisateur_id]);
$adresses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mon compte - Delikasher</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f2f2f2;
      margin: 0;
      padding: 0;
      color: #1e1e1e;
    }
    .container {
      max-width: 900px;
      margin: 2rem auto;
      background: white;
      padding: 2rem;
      border-radius: 12px;
    }
    h2 {
      margin-top: 0;
    }
    .nav-links {
      display: flex;
      gap: 1rem;
      margin-bottom: 2rem;
    }
    .nav-links a {
      padding: 0.6rem 1.2rem;
      background: #06c167;
      color: white;
      text-decoration: none;
      border-radius: 30px;
    }
    .infos, .historique, .adresses, .form-adresse {
      margin-bottom: 2rem;
      background: #f7f7f7;
      padding: 1rem;
      border-radius: 8px;
    }
    .adresse {
      background: white;
      border: 1px solid #ddd;
      padding: 1rem;
      margin-bottom: 1rem;
      border-radius: 8px;
    }
    .adresse form {
      display: inline;
    }
    input[type="text"] {
      width: 100%;
      padding: 0.6rem;
      margin-bottom: 1rem;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    .btn {
      padding: 0.6rem 1.2rem;
      background: #06c167;
      color: white;
      border: none;
      border-radius: 30px;
      cursor: pointer;
    }
    .btn-danger {
      background: #e74c3c;
    }
    .defaut-label {
      background: #06c167;
      color: white;
      font-size: 0.8rem;
      padding: 0.3rem 0.8rem;
      border-radius: 30px;
      margin-left: 1rem;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="nav-links">
    <a href="accueil_utilisateurs.php">🏠 Accueil restaurants</a>
    <a href="deconnexion_utilisateur.php">🚪 Déconnexion</a>
  </div>

  <div class="infos">
    <h2>Mes informations</h2>
    <p><strong>Nom :</strong> <?= htmlspecialchars($nom_complet) ?></p>
    <p><strong>Email :</strong> <?= htmlspecialchars($email) ?></p>
  </div>

  <div class="historique">
    <h2>📦 Mes commandes</h2>
    <p>(Historique réel à connecter à la table commandes plus tard)</p>
  </div>

  <div class="form-adresse">
    <h2>➕ Ajouter une adresse de livraison</h2>
    <form method="POST">
      <input type="text" name="nom_adresse" placeholder="Nom de l'adresse (ex : Domicile, Travail...)" required>
      <input type="text" name="numero_telephone" placeholder="Numéro de téléphone" required>
      <input type="text" name="adresse" placeholder="Adresse complète" required>
      <input type="text" name="code_postal" placeholder="Code postal" required>
      <input type="text" name="ville" placeholder="Ville" required>
      <button type="submit" name="ajouter_adresse" class="btn">Ajouter l'adresse</button>
    </form>
  </div>

  <div class="adresses">
    <h2>📍 Mes adresses enregistrées</h2>
    <?php if (count($adresses) > 0): ?>
      <?php foreach ($adresses as $adr): ?>
        <div class="adresse">
          <?php if ($adresse_en_modif === (int)$adr['id']): ?>
            <form method="POST">
              <input type="hidden" name="adresse_id" value="<?= $adr['id'] ?>">
              <input type="text" name="nom_adresse" value="<?= htmlspecialchars($adr['nom_adresse']) ?>" required>
              <input type="text" name="numero_telephone" value="<?= htmlspecialchars($adr['numero_telephone']) ?>" required>
              <input type="text" name="adresse" value="<?= htmlspecialchars($adr['adresse']) ?>" required>
              <input type="text" name="code_postal" value="<?= htmlspecialchars($adr['code_postal']) ?>" required>
              <input type="text" name="ville" value="<?= htmlspecialchars($adr['ville']) ?>" required>
              <button type="submit" name="modifier_adresse" class="btn">Enregistrer</button>
              <a href="dashboard_utilisateur.php" class="btn btn-danger">Annuler</a>
            </form>
          <?php else: ?>
            <p><strong><?= htmlspecialchars($adr['nom_adresse']) ?></strong>
              <?php if (!empty($adr['est_defaut'])): ?>
                <span class="defaut-label">Adresse par défaut</span>
              <?php endif; ?>
            </p>
            <p><?= htmlspecialchars($adr['adresse']) ?>, <?= htmlspecialchars($adr['code_postal']) ?> <?= htmlspecialchars($adr['ville']) ?></p>
            <p>Téléphone : <?= htmlspecialchars($adr['numero_telephone']) ?></p>
            <a href="dashboard_utilisateur.php?modifier=<?= $adr['id'] ?>" class="btn">Modifier</a>
            <a href="dashboard_utilisateur.php?supprimer_adresse=<?= $adr['id'] ?>" class="btn btn-danger" onclick="return confirm('Supprimer cette adresse ?');">Supprimer</a>
            <?php if (empty($adr['est_defaut'])): ?>
              <a href="dashboard_utilisateur.php?defaut=<?= $adr['id'] ?>" class="btn">Définir par défaut</a>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Aucune adresse enregistrée.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>