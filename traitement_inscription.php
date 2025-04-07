<?php
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

// Vérifier les champs obligatoires
if (
    isset($_POST['civilite'], $_POST['nom'], $_POST['prenom'], $_POST['jour'], $_POST['mois'], $_POST['annee'],
          $_POST['email'], $_POST['mot_de_passe'], $_POST['cgu'])
) {
    // Sécurisation des données
    $civilite = htmlspecialchars($_POST['civilite']);
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $jour = htmlspecialchars($_POST['jour']);
    $mois = htmlspecialchars($_POST['mois']);
    $annee = htmlspecialchars($_POST['annee']);
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe']; // pas de htmlspecialchars ici
    $parrainage = isset($_POST['parrainage']) ? htmlspecialchars($_POST['parrainage']) : null;
    $accepte_cgu = isset($_POST['cgu']) ? 1 : 0;
    $accepte_pubs = isset($_POST['pubs']) ? 1 : 0;

    // Vérification du mot de passe (min 8 caractères, 1 chiffre, 1 majuscule)
    if (strlen($mot_de_passe) < 8 || !preg_match('/[0-9]/', $mot_de_passe) || !preg_match('/[A-Z]/', $mot_de_passe)) {
        die("Mot de passe invalide. Il doit contenir au moins 8 caractères, 1 chiffre et 1 majuscule.");
    }

    // Vérifier si l'email est déjà utilisé
    $checkEmail = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $checkEmail->execute([$email]);

    if ($checkEmail->rowCount() > 0) {
        die("Cette adresse email est déjà utilisée. Veuillez en choisir une autre.");
    }

    // Hash du mot de passe
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Insertion en base
    $sql = "INSERT INTO utilisateurs (civilite, nom, prenom, jour, mois, annee, email, mot_de_passe, parrainage, accepte_cgu, accepte_pubs)
            VALUES (:civilite, :nom, :prenom, :jour, :mois, :annee, :email, :mot_de_passe, :parrainage, :accepte_cgu, :accepte_pubs)";
    
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':civilite' => $civilite,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':jour' => $jour,
            ':mois' => $mois,
            ':annee' => $annee,
            ':email' => $email,
            ':mot_de_passe' => $mot_de_passe_hash,
            ':parrainage' => $parrainage,
            ':accepte_cgu' => $accepte_cgu,
            ':accepte_pubs' => $accepte_pubs
        ]);

        echo "<h2>🎉 Inscription réussie !</h2>";
        echo "<p><a href='connexion_utilisateur.php'>vous connecter</a>.</p>";


    } catch (PDOException $e) {
        echo "Erreur lors de l'enregistrement : " . $e->getMessage();
    }

} else {
    echo "Tous les champs obligatoires doivent être remplis.";
}
?>
