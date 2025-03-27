<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $db;
include 'database.php';
include 'Serpent.php';

// Si c'est une requête POST (envoi du formulaire)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<pre>POST data:";
    print_r($_POST);
    echo "</pre>";
    // Récupérer les données du formulaire
    $id_serpent = $_POST['id_serpent'] ?? null;
    $name = $_POST['name'] ?? '';
    $weight = $_POST['weight'] ?? 0;
    $life_time = $_POST['life_time'] ?? 0;
    $race = $_POST['race'] ?? '';
    $gender = $_POST['gender'] ?? '';

    // Valider les données
    $errors = [];

// Vérification des champs obligatoires
    if (empty(trim($_POST['name']))) {
        $errors[] = "Le nom est obligatoire";
    }
    if (empty($_POST['birth'])) {
        $errors[] = "La date de naissance est obligatoire";
    }
    if (empty($_POST['race']) || $_POST['race'] === '') {
        $errors[] = "La race est obligatoire";
    }
    if (empty($_POST['gender']) || $_POST['gender'] === '') {
        $errors[] = "Le genre est obligatoire";
    }

// Si erreurs, les afficher et arrêter l'exécution
    if (!empty($errors)) {
        echo "<div class='alert alert-danger'>";
        echo implode("<br>", $errors);
        echo "</div>";
        // Réafficher le formulaire avec les données existantes
        include 'add_modify_form.php'; // Créez ce fichier ou gardez le formulaire ici
        exit();
    }

    // Valider le genre
    $allowed_genders = ['male', 'femelle'];
    if (!in_array($_POST['gender'], $allowed_genders)) {
        die("Genre invalide. Valeurs acceptées: " . implode(', ', $allowed_genders));
    }

// Valider la race
    $allowed_races = ['Python', 'Boa', 'Cobra', 'Venimous'];
    if (!in_array($_POST['race'], $allowed_races)) {
        die("Race invalide. Valeurs acceptées: " . implode(', ', $allowed_races));
    }

// Vérification de la date
    $current_date = date('Y-m-d H:i:s');
    if (strtotime($_POST['birth']) > strtotime($current_date)) {
        die("La date de naissance ne peut pas être dans le futur");
    }

// Convertir la date
    try {
        $date = new DateTime($_POST['birth']);
        $birth = $date->format('Y-m-d H:i:s');
    } catch (Exception $e) {
        die("Format de date invalide: " . $e->getMessage());
    }

    // Créer et sauvegarder le serpent
    echo "<pre>";
    var_dump([
        'id_serpent' => $id_serpent,
        'name' => $name,
        'weight' => $weight,
        'life_time' => $life_time,
        'birth' => $birth,
        'race' => $race,
        'gender' => $gender
    ]);
    echo "</pre>";

    if (empty($birth) || empty($race) || empty($gender)) {
        die("Erreur de validation: certaines données obligatoires sont manquantes après traitement");
    }

    $serpent = new Serpent($id_serpent, $name, $weight, $life_time, $birth, $race, $gender);

    try {
        $result = $serpent->ajouterOuModifier($db);

        if (!$result) {
            throw new Exception("Échec de l'opération sans exception déclenchée");
        }

        header("Location: index.php");
        exit();

    } catch (mysqli_sql_exception $e) {
        // Erreur spécifique MySQL
        error_log("Erreur MySQL: " . $e->getMessage());
        echo "<div class='alert alert-danger'>";
        echo "Erreur base de données: " . htmlspecialchars($e->getMessage());
        echo "<br>Code erreur: " . $e->getCode();
        echo "</div>";

        // Debug supplémentaire
        if (method_exists($serpent, 'debugConnection')) {
            $serpent->debugConnection($db);
        }

        // Affiche le formulaire à nouveau avec les données saisies
        include 'add_modify_form.php';

    } catch (Exception $e) {
        // Erreur générique
        error_log("Erreur générale: " . $e->getMessage());
        echo "<div class='alert alert-danger'>";
        echo "Erreur: " . htmlspecialchars($e->getMessage());
        echo "</div>";
    }
}
// Si c'est une requête GET (affichage du formulaire)
else {
    $id_serpent = $_GET['id_serpent'] ?? null;
    $serpent = null;

    // Si on modifie un serpent existant
    if ($id_serpent) {
        $serpent = Serpent::obtenirSerpentParId($db, $id_serpent);
    }
    ?>

<?php }