<?php
// Récupérer les données existantes (si modification)
$serpent = $serpent ?? null;
$id_serpent = $_GET['id_serpent'] ?? null;
$name = $_GET['name'] ?? '';
$weight = $_GET['weight'] ?? 0;
$life_time = $_GET['life_time'] ?? 0;
$race = $_GET['race'] ?? '';
$gender = $_GET['gender'] ?? '';
?>

<form method="POST" class="needs-validation" novalidate>
    <input type="hidden" name="id_serpent" value="<?= htmlspecialchars($id_serpent) ?>">

    <!-- Champ Nom -->
    <div class="mb-3">
        <label class="form-label">Nom *</label>
        <input type="text" class="form-control" name="name"
               value="<?= htmlspecialchars($serpent['name'] ?? $_POST['name'] ?? '') ?>"
               required>
    </div>

    <!-- Champ Poids -->
    <div class="mb-3">
        <label class="form-label">Poids (en grammes) *</label>
        <input type="number" class="form-control" name="weight"
               value="<?= htmlspecialchars($serpent['weight'] ?? $_POST['weight'] ?? '') ?>"
               min="0" step="10" required>
    </div>

    <!-- Champ Date de naissance -->
    <div class="mb-3">
        <label class="form-label">Date de naissance *</label>
        <input type="datetime-local" class="form-control" name="birth"
               max="<?= date('Y-m-d\TH:i') ?>"
               value="<?= isset($serpent['birth']) ? date('Y-m-d\TH:i', strtotime($serpent['birth'])) :
                   (htmlspecialchars($_POST['birth'] ?? '')) ?>"
               required>
    </div>

    <!-- Menu Race -->
    <div class="mb-3">
        <label class="form-label">Race *</label>
        <select class="form-select" name="race" required>
            <option value="">Choisissez...</option>
            <?php foreach (['Python', 'Boa', 'Cobra', 'Venimous'] as $race): ?>
                <option value="<?= $race ?>"
                    <?= ($race === ($serpent['race'] ?? $_POST['race'] ?? '')) ? 'selected' : '' ?>>
                    <?= $race ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Genre *</label>
        <select class="form-control" name="gender" required>
            <option value="">Sélectionnez un genre</option>
            <?php foreach (['male', 'femelle'] as $gender): ?>
                <option value="<?= $race ?>"
                    <?= ($gender === ($serpent['gender'] ?? $_POST['gender'] ?? '')) ? 'selected' : '' ?>>
                    <?= $gender ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Bouton Soumettre -->
    <button type="submit" class="btn btn-primary">
        <?= $id_serpent ? 'Modifier' : 'Ajouter' ?> le serpent
    </button>
</form>