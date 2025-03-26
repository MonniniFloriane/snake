-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 20 mars 2025 à 14:31
-- Version du serveur : 8.0.30
-- Version de PHP : 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `snake`
--

-- --------------------------------------------------------

--
-- Structure de la table `serpent`
--

CREATE TABLE `serpent` (
  `id_serpent` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `weight` int NOT NULL,
  `life_time` int NOT NULL,
  `birth` datetime NOT NULL,
  `race` set('Python','Boa','Cobra','Venimous') DEFAULT NULL,
  `gender` enum('male','femelle') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `serpent`
--

INSERT INTO `serpent` (`id_serpent`, `name`, `weight`, `life_time`, `birth`, `race`, `gender`) VALUES
(1, 'ggg', 10, 2, '2025-01-15 15:01:00', 'Python', 'male'),
(2, 'aaaa', 10, 2, '2025-01-16 12:00:00', 'Boa', 'femelle'),
(3, 'ggggo', 10, 2, '2025-01-17 02:00:00', 'Python', 'male'),
(4, 'tttt', 20, 2, '2025-01-16 12:00:00', 'Cobra', 'male'),
(5, 'aaaal', 10, 2, '2025-01-18 12:00:00', 'Venimous', 'male'),
(6, 'gggg', 10, 1, '2025-03-19 12:00:00', 'Python', 'male'),
(7, 'ffff', 10, 1, '2025-03-20 12:00:00', 'Python', 'male'),
(8, 'ffff', 10, 1, '2025-03-18 12:00:00', 'Python', 'male'),
(9, 'ffff', 10, 1, '2025-03-19 12:00:00', 'Python', 'male');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `serpent`
--
ALTER TABLE `serpent`
  ADD PRIMARY KEY (`id_serpent`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `serpent`
--
ALTER TABLE `serpent`
  MODIFY `id_serpent` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
