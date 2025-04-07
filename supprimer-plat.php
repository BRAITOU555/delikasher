<?php
session_start();
if (!isset($_SESSION['restaurant_id'])) {
  header("Location: connexion-restaurant.html");
  exit();
}

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

$restaurant_id = $_SESSION['restaurant_id'];

$id = $_GET['id'] ?? null;
if ($id) {
  // Vérifie que le plat appartient bien au restaurant connecté
  $check = $pdo->prepare("SELECT p.id FROM plats p JOIN categories c ON p.categorie_id = c.id WHERE p.id = ? AND c.restaurant_id = ?");
  $check->execute([$id, $restaurant_id]);
  if ($check->rowCount() > 0) {
    $delete = $pdo->prepare("DELETE FROM plats WHERE id = ?");
    $delete->execute([$id]);
  }
}

header("Location: mon-menu.php");
exit();
