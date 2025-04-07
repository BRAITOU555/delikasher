<?php
ob_start(); // ← AJOUT ICI
session_start();

// Connexion à la base de données
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

// Vérifie que les champs sont remplis
if (!empty($_POST['email']) && !empty($_POST['mot_de_passe'])) {
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    // Récupère l'utilisateur en base
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        // Connexion réussie : on enregistre l'utilisateur en session
        $_SESSION['utilisateur_id'] = $utilisateur['id'];
        $_SESSION['utilisateur_nom'] = $utilisateur['prenom'] . ' ' . $utilisateur['nom'];
        $_SESSION['utilisateur_email'] = $utilisateur['email'];

        // Redirection
        header("Location: accueil_utilisateurs.php");
        exit;
    } else {
        echo "<p style='color: red; text-align: center; margin-top: 2rem;'>Email ou mot de passe incorrect</p>";
        echo "<p style='text-align: center;'><a href='connexion_utilisateur.php'>⟵ Revenir à la page de connexion</a></p>";
    }
} else {
    echo "<p style='color: red; text-align: center; margin-top: 2rem;'>Veuillez remplir tous les champs.</p>";
    echo "<p style='text-align: center;'><a href='connexion_utilisateur.php'>⟵ Revenir à la page de connexion</a></p>";
}

ob_end_flush(); // ← AJOUT ICI
?>
