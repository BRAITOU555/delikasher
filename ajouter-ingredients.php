<?php
$host = 'localhost';
$dbname = 'delikasher_db';  // Assure-toi que le nom de ta base de données est correct
$user = 'root';  // Par défaut, l'utilisateur est root sur XAMPP
$pass = '';  // Le mot de passe est vide par défaut

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Liste d'ingrédients (tu peux ajouter tout ce que tu veux ici)
$ingredients = [
    'Billes de Mozzarella', 'Cœurs de palmier', 'Concombre', 'Croûtons', 'Cranberry', 'Fruits de saison', 'Gravlax', 
    'Maïs', 'Mesclun', 'Noisettes', 'Oignons rouges', 'Parmesan', 'Pommes de terre', 'Salade verte', 'Sauce tomate', 
    'Tomates', 'Tomates cerises', 'Tomates séchées', 'Thon', 'Limonade', 'Orangeade', 'Coca-Cola', 'Fanta', 'Jus de fruits', 
    'Thé', 'Eau', 'Sauce harissa', 'Roquette'
];

// Catégories d'ingrédients (Pizza, Boisson, Supplément)
$categories = [
    'Pizza' => ['Billes de Mozzarella', 'Cœurs de palmier', 'Concombre', 'Croûtons', 'Cranberry', 'Fruits de saison', 
                'Gravlax', 'Maïs', 'Mesclun', 'Noisettes', 'Oignons rouges', 'Parmesan', 'Pommes de terre', 'Salade verte', 
                'Sauce tomate', 'Tomates', 'Tomates cerises', 'Tomates séchées', 'Thon'],
    'Boisson' => ['Limonade', 'Orangeade', 'Coca-Cola', 'Fanta', 'Jus de fruits', 'Thé', 'Eau'],
    'Supplément' => ['Sauce harissa', 'Roquette']
];

// Insertion des ingrédients dans la base de données
$stmt = $pdo->prepare("INSERT INTO ingredients (nom, categorie) VALUES (?, ?)");

// On ajoute chaque ingrédient pour chaque catégorie
foreach ($categories as $categorie => $ingrédients) {
    foreach ($ingrédients as $ingrédient) {
        $stmt->execute([$ingrédient, $categorie]);
    }
}

echo "Les ingrédients ont été ajoutés avec succès.";
?>
