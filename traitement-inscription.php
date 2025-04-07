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
    die("Erreur de connexion : " . $e->getMessage());
}

$message = "";

// Vérification des champs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $siret = trim($_POST['siret']);
    $telephone = trim($_POST['telephone']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $adresse = trim($_POST['adresse']);
    $specialite = trim($_POST['specialite']);

    if (empty($nom) || empty($siret) || empty($email) || empty($mot_de_passe) || empty($adresse) || empty($specialite)) {
        $message = "❌ Tous les champs sont obligatoires.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM restaurants WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $message = "❌ Cet email est déjà utilisé.";
        } else {
            $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Insertion du restaurant avec spécialité
            $stmt = $pdo->prepare("INSERT INTO restaurants (nom, siret, telephone, email, mot_de_passe, adresse, specialite) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $siret, $telephone, $email, $mot_de_passe_hache, $adresse, $specialite]);

            $restaurant_id = $pdo->lastInsertId();

            // Catégories selon la spécialité
            $specialite_categories = [
                "Pizzeria"   => ["Pizzas", "Paninis", "Entrées", "Salades", "Desserts", "Boissons"],
                "Hamburger"  => ["Burgers", "Frites", "Accompagnements", "Desserts", "Boissons"],
                "Chinois"    => ["Nems", "Riz", "Plats sautés", "Soupes", "Boissons"],
                "Japonais"   => ["Sushis", "Makis", "Plats chauds", "Entrées", "Boissons"],
                "Oriental"   => ["Tajines", "Couscous", "Grillades", "Entrées", "Boissons"],
                "Saladerie"  => ["Salades", "Accompagnements", "Desserts", "Boissons"],
                "Autre"      => ["Menus", "Plats", "Desserts", "Boissons"]
            ];

            $categories_a_creer = $specialite_categories[$specialite] ?? [];

            $cat_stmt = $pdo->prepare("INSERT INTO categories (restaurant_id, nom) VALUES (?, ?)");
            foreach ($categories_a_creer as $cat) {
                $cat_stmt->execute([$restaurant_id, $cat]);
            }

            // Connexion automatique
            $_SESSION['restaurant_id'] = $restaurant_id;
            $_SESSION['restaurant_nom'] = $nom;

            // Redirection
            header("Location: dashboard.php");
            exit();
        }
    }
}
?>
