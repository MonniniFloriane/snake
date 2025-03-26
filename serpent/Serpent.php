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
        if ($this->id_serpent) {
            // Modification
            $stmt = $db->prepare("UPDATE serpent SET name=?, weight=?, life_time=?, birth=?, race=?, gender=? WHERE id_serpent=?");
            $stmt->bind_param('siissi', $this->name, $this->weight, $this->life_time, $this->birth, $this->race, $this->gender, $this->id_serpent);
        } else {
            // Ajout
            $stmt = $db->prepare("INSERT INTO serpent (name, weight, life_time, birth, race, gender) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('siisss', $this->name, $this->weight, $this->life_time, $this->birth, $this->race, $this->gender);
        }
        $stmt->execute();
        $stmt->close();
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
}