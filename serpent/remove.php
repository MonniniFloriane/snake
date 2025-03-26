<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $db;
include 'database.php';
include 'Serpent.php';

$id_serpent = $_GET['id_serpent'] ?? null;

if ($id_serpent) {
    Serpent::supprimer($db, $id_serpent);
}

header("Location: index.php");
?>

