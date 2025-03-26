<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $db;
include 'database.php';
include 'Serpent.php';

$filtre_genre = $_GET['filtre_genre'] ?? "";
$filtre_race = $_GET['filtre_race'] ?? "";
$tri = $_GET['tri'] ?? null;
$page = $_GET['page'] ?? 1;
$par_page = 10;

// Récupérer les serpents
$serpents = Serpent::obtenirSerpents($db, $filtre_genre, $filtre_race, $tri, $page, $par_page);

// Compter le nombre total de serpents pour la pagination
$total_serpents = Serpent::compterSerpents($db, $filtre_genre, $filtre_race);
$total_pages = ceil($total_serpents / $par_page);

// Récupérer les totaux par genre
$total_par_genre = Serpent::compterSerpentsParGenre($db);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Serpentin</title>

    <!--Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!--<link href="style.css" rel="stylesheet">-->
</head>

<body class="container mt-5 mb-5 rounded-3 p-0">

<style>
    html {
        background-image: url('img/snake.jpg');
        background-size: cover;
        background-position: center;
    }
    h1 {
        text-align: center;
        text-transform: uppercase;
        font-weight: bold;
        color: #F4EFE9;
    }

    h2 {
        font-weight: bold;
    }

    h2, label{
        color: #343509;
        text-transform: uppercase;
    }

    .bgHeader {
        background-color: #CE9152;
    }

    body {
        background-color: #F4EFE9;
        border : 2px solid #CE9152;
    }

    .btnAdd {
        background-color: #343509;
        color: #F4EFE9;
    }

    .btnFiltre {
        background-color: #CE9152;
        color: #343509;
    }

    .thead {
        color: #343509;
    }
</style>

<header>
    <div class="bgHeader rounded-2 mb-5 p-3">
        <h1 class="text-center">Serpentin!</h1>
    </div>
</header>

<main class="p-3">
    <!-- Formulaire pour ajouter/modifier des serpents -->
    <section class="mb-5">
        <form action="add_modify.php" method="post">
            <input type="hidden" name="id_serpent" value="">
            <fieldset>
                <h2 class="mb-4">Ajouter un serpent</h2>
                <div class="mb-3 row g-3 align-items-center">
                    <div class="col-6">
                        <label for="name" class="form-label fw-bold">Nom :</label>
                        <input type="text" id="name" class="form-control" placeholder="Entrée un nom"
                               name="name">
                    </div>
                    <div class="col-6">
                        <label for="weight" class="form-label fw-bold">Poids :</label>
                        <input type="number" id="weight" class="form-control" placeholder="Poids en gramme"
                               name="weight" min="0" step="10" required>
                    </div>
                </div>
                <!--col2-->
                <div class="mb-3 row align-items-center">
                    <div class="col-6">
                        <label for="life" class="form-label fw-bold">Durée de Vie :</label>
                        <input type="number" id="life" class="form-control" placeholder="Vie en année"
                               name="life_time" min="0" required>
                    </div>
                    <div class="col-6">
                        <label for="birth" class="form-label fw-bold">Date de Naissance :</label>
                        <input type="datetime-local" id="birth" class="form-control" name="birth" min="0"
                               required>
                    </div>
                </div>
                <!--col3-->
                <div class="mb-3 row align-items-center">
                    <div class="col-6">
                        <label for="race" class="form-label fw-bold">Race :</label>
                        <select id="race" class="form-select" name="race">
                            <option value="default">Selectionner une race</option>
                            <option value="Python">Python</option>
                            <option value="Boa">Boa</option>
                            <option value="Cobra">Cobra</option>
                            <option value="Venimous">Venimous</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="gender" class="form-label fw-bold">Genre :</label>
                        <select id="gender" class="form-select" name="gender">
                            <option value="default">Selectionner un genre</option>
                            <option value="male">Mâle</option>
                            <option value="femelle">Femelle</option>
                        </select>
                    </div>
                </div>

                <div class="mt-5 d-md-flex gap-2 justify-content-md-end">

                    <button class="btn btnAdd fw-bold rounded-5" type="submit">
                        <i class="fa-solid fa-plus"></i>
                        AJOUTER UN SERPENT
                    </button>

                    <button class="btn text-danger rounded-5" type="reset">
                        <i class="fa-solid fa-arrow-rotate-left"></i>
                        EFFACER
                    </button>
                </div>

            </fieldset>
        </form>
    </section>
    <!------------------------LISTE--------------------------->

    <section>
        <h2 class="mb-4">Liste de serpent</h2>
        <!--FILTRE-->
        <div class="dropdown d-flex gap-4">
            <a href="index.php?tri=name" class="btn btnFiltre">
                <i class="fa-solid fa-arrow-down-a-z"></i>
                Ordre alphabétique
            </a>

            <a href="index.php?tri=birth DESC" class="btn btnFiltre">
                <i class="fa-solid fa-arrow-down-1-9"></i>
                Date + récente
            </a>

            <a href="index.php?tri=weight DESC" class="btn btnFiltre">
                <i class="fa-solid fa-dumbbell"></i>
                Poids ordre décroissant
            </a>

            <a href="index.php?tri=life_time DESC" class="btn btnFiltre">
                <i class="fa-solid fa-hourglass-half"></i>
                durée de vie ordre décroissant
            </a>

            <div class="dropdown">
                <button class="btn btnFiltre dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-mars-and-venus"></i>
                    Genre
                </button>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item" href="index.php?filtre_genre=male">Mâle</a></li>
                    <li><a class="dropdown-item" href="index.php?filtre_genre=femelle">Femelle</a></li>
                </ul>
            </div>

            <div class="dropdown">
                <button class="btn btnFiltre dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-earth-americas"></i>
                    Race
                </button>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item" href="index.php?filtre_race=Python">Python</a></li>
                    <li><a class="dropdown-item" href="index.php?filtre_race=Boa">Boa</a></li>
                    <li><a class="dropdown-item" href="index.php?filtre_race=Cobra">Cobra</a></li>
                    <li><a class="dropdown-item" href="index.php?filtre_race=Venimous">Venimous</a></li>
                </ul>
            </div>
        </div>

        <!--TABLE-->
        <table class="table mt-5">
            <thead>
            <tr class="text-uppercase fw-bold thead">
                <th>Nom</th>
                <th>Poids</th>
                <th>Durée de Vie</th>
                <th>Date de Naissance</th>
                <th>Race</th>
                <th>Genre</th>
                <th colspan="2"></th>
            </tr>
            </thead>
            <tbody class="table-group-divider">
            <?php if (is_array($serpents) && !empty($serpents)) { ?>
                <?php foreach ($serpents as $serpent) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($serpent['name']); ?></td>
                        <td><?php echo htmlspecialchars($serpent['weight']); ?></td>
                        <td><?php echo htmlspecialchars($serpent['life_time']); ?></td>
                        <?php $date = new DateTime($serpent['birth']); ?>
                        <td><?php echo htmlspecialchars($date->format("d/m/Y H:i")); ?></td>
                        <td><?php echo htmlspecialchars($serpent['race']); ?></td>
                        <td><?php echo htmlspecialchars($serpent['gender']); ?></td>
                        <td colspan="2">
                            <a href="add_modify.php?id_serpent=<?php echo $serpent['id_serpent']; ?>" class="btn text-success">
                                <i class="fa-solid fa-pencil"></i>
                            </a>
                            <a href="remove.php?id_serpent=<?php echo $serpent['id_serpent']; ?>" class="btn text-danger">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="7" class="text-center">Aucun serpent trouvé.</td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <th class="text-uppercase fw-bold table-active">Total par genre :</th>
                <td>Mâles : <?php echo $total_par_genre['male']; ?></td>
                <td>Femelles : <?php echo $total_par_genre['femelle']; ?></td>
            </tr>
            </tfoot>
        </table>

        <!--Pagination-->

        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Bouton Précédent -->
                <li class="page-item <?php echo ($page == 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="index.php?page=<?php echo $page - 1; ?>&filtre_genre=<?php echo $filtre_genre; ?>&filtre_race=<?php echo $filtre_race; ?>&tri=<?php echo $tri; ?>">Précédent</a>
                </li>

                <!-- Numéros de page -->
                <?php
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<li class='page-item'><a class='page-link' href='index.php?page=$i&filtre_genre=$filtre_genre&filtre_race=$filtre_race&tri=$tri'>$i</a></li>";
                }
                ?>

                <!-- Bouton Suivant -->
                <li class="page-item <?php echo ($page == $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="index.php?page=<?php echo $page + 1; ?>&filtre_genre=<?php echo $filtre_genre; ?>&filtre_race=<?php echo $filtre_race; ?>&tri=<?php echo $tri; ?>">Suivant</a>
                </li>
            </ul>
        </nav>
    </section>
</main>

<!--bootstrap JS-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

<!--fontawesome-->
<script src="https://kit.fontawesome.com/4083d2487c.js" crossorigin="anonymous"></script>
</body>
</html>