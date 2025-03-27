<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Serpent {
    private $id_serpent;
    private $name;
    private $weight;
    private $life_time;
    private $birth;
    private $race;
    private $gender;

    public function __construct($id_serpent, $name, $weight, $life_time, $birth, $race, $gender) {
        $this->id_serpent = $id_serpent;
        $this->name = $name;
        $this->weight = $weight;
        $this->life_time = $life_time;
        $this->birth = $birth;
        $this->race = $race;
        $this->gender = $gender;
    }


    public function ajouterOuModifier($db) {
        // Debug des données
        error_log("Tentative de modification/ajout - ID: " . $this->id_serpent);
        error_log("Données: " . print_r([
                'name' => $this->name,
                'weight' => $this->weight,
                'life_time' => $this->life_time,
                'birth' => $this->birth,
                'race' => $this->race,
                'gender' => $this->gender
            ], true));

        if ($this->id_serpent) {
            // Modification
            $stmt = $db->prepare("UPDATE serpent SET name=?, weight=?, life_time=?, birth=?, race=?, gender=? WHERE id_serpent=?");
            if (!$stmt) {
                error_log("Erreur préparation UPDATE: " . $db->error);
                return false;
            }
            $stmt->bind_param('siissii',
                $this->name,
                $this->weight,
                $this->life_time,
                $this->birth,
                $this->race,
                $this->gender,
                $this->id_serpent);
        } else {
            // Ajout
            $stmt = $db->prepare("INSERT INTO serpent (name, weight, life_time, birth, race, gender) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                error_log("Erreur préparation INSERT: " . $db->error);
                return false;
            }
            $stmt->bind_param('siisss',
                $this->name,
                $this->weight,
                $this->life_time,
                $this->birth,
                $this->race,
                $this->gender);
        }

        $result = $stmt->execute();
        if (!$result) {
            error_log("Erreur exécution: " . $stmt->error);
        }
        $stmt->close();

        return $result;
    }

    public static function supprimer($db, $id_serpent) {
        $stmt = $db->prepare("DELETE FROM serpent WHERE id_serpent=?");
        $stmt->bind_param("i", $id_serpent);
        $stmt->execute();
        $stmt->close();
    }

    public static function obtenirSerpents($db, $filtre_genre = "", $filtre_race = "", $tri = null, $page = 1, $par_page = 10) {
        $query = "SELECT * FROM serpent WHERE 1=1";

        if ($filtre_genre) {
            $query .= " AND gender=?";
        }
        if ($filtre_race) {
            $query .= " AND race=?";
        }
        if ($tri) {
            $query .= " ORDER BY " . $tri;
        }

        $offset = ($page - 1) * $par_page;
        $query .= " LIMIT ?, ?";

        $stmt = $db->prepare($query);

        if ($filtre_genre && $filtre_race) {
            $stmt->bind_param("ssii", $filtre_genre, $filtre_race, $offset, $par_page);
        } else if ($filtre_genre) {
            $stmt->bind_param("sii", $filtre_genre, $offset, $par_page);
        } else if ($filtre_race) {
            $stmt->bind_param("sii", $filtre_race, $offset, $par_page);
        } else {
            $stmt->bind_param("ii", $offset, $par_page);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $serpents = $result->fetch_all(MYSQLI_ASSOC); // Récupère tous les résultats sous forme de tableau associatif
        $stmt->close();

        return $serpents; // Retourne un tableau de serpents
    }

    public static function obtenirSerpentParId($db, $id_serpent) {
        $stmt = $db->prepare("SELECT * FROM serpent WHERE id_serpent = ?");
        $stmt->bind_param("i", $id_serpent);
        $stmt->execute();
        $result = $stmt->get_result();
        $serpent = $result->fetch_assoc();
        $stmt->close();

        // Convertit la date au format datetime-local
        if ($serpent && isset($serpent['birth'])) {
            $serpent['birth'] = date('Y-m-d\TH:i', strtotime($serpent['birth']));
        }

        return $serpent;
    }

    public static function compterSerpents($db, $filtre_genre = "", $filtre_race = "") {
        $query = "SELECT COUNT(*) as total FROM serpent WHERE 1=1";

        if ($filtre_genre) {
            $query .= " AND gender=?";
        }
        if ($filtre_race) {
            $query .= " AND race=?";
        }

        $stmt = $db->prepare($query);

        if ($filtre_genre && $filtre_race) {
            $stmt->bind_param("ss", $filtre_genre, $filtre_race);
        } else if ($filtre_genre) {
            $stmt->bind_param("s", $filtre_genre);
        } else if ($filtre_race) {
            $stmt->bind_param("s", $filtre_race);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['total']; // Retourne le nombre total de serpents
    }

    public static function compterSerpentsParGenre($db) {
        $query = "SELECT gender, COUNT(*) as count FROM serpent GROUP BY gender";
        $result = $db->query($query);
        $counts = ["male" => 0, "femelle" => 0];
        while ($row = $result->fetch_assoc()) {
            if ($row['gender'] == 'male') {
                $counts['male'] = $row['count'];
            } elseif ($row['gender'] == 'femelle') {
                $counts['femelle'] = $row['count'];
            }
        }
        return $counts;
    }

    public static function compterSerpentsParRace($db) {
        $query = "SELECT race, COUNT(*) as count FROM serpent GROUP BY race";
        $result = $db->query($query);

        // Initialisation du tableau avec toutes les races possibles à 0
        $counts = [
            'Python' => 0,
            'Boa' => 0,
            'Cobra' => 0,
            'Venimous' => 0
        ];

        while ($row = $result->fetch_assoc()) {
            // On met à jour le compteur pour la race correspondante
            if (isset($counts[$row['race']])) {
                $counts[$row['race']] = $row['count'];
            }
        }

        return $counts;
    }

    public static function debugConnection($db) {
        echo "Statut connexion MySQL: ";
        echo $db->ping() ? "Connecté" : "Non connecté";
        echo "<br>Dernière erreur: " . $db->error;

        // Testez une requête simple
        $result = $db->query("SELECT 1");
        echo "<br>Test requête: ";
        echo $result ? "Réussi" : "Échec (" . $db->error . ")";
    }
}