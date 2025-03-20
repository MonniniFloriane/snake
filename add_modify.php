<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $db;
include 'database.php';
include 'Serpent.php';

// Récupérer les données du formulaire
$id_serpent = $_POST['id_serpent'] ?? null;
$name = $_POST['name'] ?? '';
$weight = $_POST['weight'] ?? 0;
$life_time = $_POST['life_time'] ?? 0;
$birth = $_POST['birth'] ?? '';
$race = $_POST['race'] ?? '';
$gender = $_POST['gender'] ?? '';

// Valider les données (exemple simple)
if (empty($name) || empty($birth) || empty($race) || empty($gender)) {
    die("Tous les champs sont obligatoires.");
}

// Convertir la date de naissance au format MySQL
$date = new DateTime($birth);
$birth = $date->format('Y-m-d H:i:s');

// Créer une instance de Serpent
$serpent = new Serpent($id_serpent, $name, $weight, $life_time, $birth, $race, $gender);

// Ajouter ou modifier le serpent dans la base de données
$serpent->ajouterOuModifier($db);

// Rediriger vers la page principale
header("Location: index.php");
exit();
?>