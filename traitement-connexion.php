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
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$email = $_POST['email'] ?? '';
$mot_de_passe = $_POST['mot_de_passe'] ?? '';

if (empty($email) || empty($mot_de_passe)) {
    die("<p style='color:red;text-align:center;'>Veuillez remplir tous les champs.<br><a href='connexion-restaurant.html'>Retour</a></p>");
}

// Vérifier si l'utilisateur existe
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE email = ?");
$stmt->execute([$email]);
$restaurant = $stmt->fetch();

if ($restaurant && password_verify($mot_de_passe, $restaurant['mot_de_passe'])) {
    // Connexion réussie
    $_SESSION['restaurant_id'] = $restaurant['id'];
    $_SESSION['restaurant_nom'] = $restaurant['nom'];

    echo "<h2 style='text-align:center;'>Bienvenue, " . htmlspecialchars($restaurant['nom']) . " !</h2>";
    echo "<p style='text-align:center;'><a href='dashboard.php'>Accéder au tableau de bord</a></p>";
} else {
    echo "<p style='color:red;text-align:center;'>Identifiants incorrects.<br><a href='connexion-restaurant.html'>Réessayer</a></p>";
}
?>
