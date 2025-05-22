-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 22 mai 2025 à 22:36
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `article_blog`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE `article` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `contenu` longtext NOT NULL,
  `date_creation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`id`, `titre`, `contenu`, `date_creation`) VALUES
(1, 'Les tendances web design en 2025', 'Les tendances incluent les polices audacieuses, les animations fluides, le dark mode, et l’utilisation de l’IA dans la personnalisation des contenus. Un bon design doit aussi rester rapide et accessible.', '2025-05-21 18:12:09'),
(3, 'Faut-il apprendre le solfège pour jouer de la guitare ?', 'Non, ce n’est pas obligatoire. Beaucoup de guitaristes jouent sans connaître le solfège, en utilisant des tablatures. Mais apprendre quelques bases de théorie musicale peut grandement améliorer ta compréhension des accords et des gammes.', '2025-05-21 20:29:24'),
(4, '10 chansons faciles pour débutants à la guitare', 'voici une playlist idéale :\r\n\r\nKnockin’ on Heaven’s Door – Bob Dylan\r\n\r\nZombie – The Cranberries\r\n\r\nStand by Me – Ben E. King\r\n\r\nLet Her Go – Passenger\r\n\r\nPerfect – Ed Sheeran\r\nTravaille-les lentement au début et augmente le rythme avec l’habitude.', '2025-05-21 20:30:12'),
(5, 'Git et GitHub pour les débutants', 'Git est un système de versionnage de code, et GitHub est une plateforme pour partager et collaborer sur des projets. Utiliser Git permet de sauvegarder et suivre les modifications de son code efficacement.', '2025-05-21 20:31:13'),
(6, 'L’importance du responsive design', 'Le responsive design permet à votre site de s’adapter à tous les écrans : smartphone, tablette, ordinateur. C’est crucial pour offrir une bonne expérience utilisateur et améliorer le référencement sur Google.', '2025-05-21 20:31:55'),
(7, 'PHP : encore utile en 2025 ?', 'Oui ! Malgré l’émergence de nouveaux langages, PHP reste largement utilisé pour les sites dynamiques et les CMS comme WordPress. Sa simplicité et sa communauté active en font un bon choix pour les projets web.', '2025-05-21 20:32:50'),
(8, 'Le JavaScript expliqué simplement', 'JavaScript permet d’ajouter de l’interactivité à un site : menus déroulants, formulaires dynamiques, animations, etc. Il est compatible avec tous les navigateurs modernes et peut aussi être utilisé côté serveur avec Node.js.', '2025-05-21 20:34:10');

-- --------------------------------------------------------

--
-- Structure de la table `article_categorie`
--

CREATE TABLE `article_categorie` (
  `article_id` int(11) NOT NULL,
  `categorie_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `article_categorie`
--

INSERT INTO `article_categorie` (`article_id`, `categorie_id`) VALUES
(1, 3),
(1, 4),
(3, 2),
(3, 3),
(4, 2),
(4, 3),
(5, 3),
(5, 4),
(6, 3),
(6, 4),
(7, 3),
(7, 4),
(8, 3),
(8, 4);

-- --------------------------------------------------------

--
-- Structure de la table `article_like`
--

CREATE TABLE `article_like` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `article_like`
--

INSERT INTO `article_like` (`id`, `article_id`, `user_id`, `ip_address`, `created_at`) VALUES
(30, 1, 3, NULL, '2025-05-21 20:06:23'),
(48, 6, 3, NULL, '2025-05-21 21:06:47'),
(54, 6, 4, NULL, '2025-05-21 21:38:55'),
(57, 7, 4, NULL, '2025-05-21 21:50:26'),
(62, 1, 4, NULL, '2025-05-21 21:55:57'),
(64, 8, 4, NULL, '2025-05-21 21:57:25'),
(71, 3, 4, NULL, '2025-05-21 22:03:59'),
(72, 4, 4, NULL, '2025-05-21 22:04:03'),
(73, 5, 4, NULL, '2025-05-21 22:04:08'),
(82, 5, 3, NULL, '2025-05-22 17:40:12'),
(83, 4, 3, NULL, '2025-05-22 17:40:15'),
(84, 3, 3, NULL, '2025-05-22 17:40:18'),
(86, 8, 3, NULL, '2025-05-22 22:15:51'),
(87, 7, 3, NULL, '2025-05-22 22:15:54');

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id`, `nom`) VALUES
(1, 'sport'),
(2, 'musique'),
(3, 'education'),
(4, 'Technologie');

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

CREATE TABLE `commentaire` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `contenu` longtext NOT NULL,
  `auteur` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commentaire`
--

INSERT INTO `commentaire` (`id`, `article_id`, `contenu`, `auteur`, `created_at`) VALUES
(8, 8, 'j\'aime bien cette article', 'larion', '2025-05-22 17:13:56'),
(9, 7, 'Article interessant', 'Larion', '2025-05-22 17:14:28'),
(10, 8, 'ftytfytr', 'tfryrty', '2025-05-22 20:43:22'),
(11, 8, 'ftytfytr', 'tfryrty', '2025-05-22 20:43:23');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250521160901', '2025-05-21 18:09:07', 5127),
('DoctrineMigrations\\Version20250521164224', '2025-05-21 18:42:41', 1196);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`) VALUES
(3, 'andriaina.rktn@gmail.com', '[\"ROLE_USER\",\"ROLE_ADMIN\"]', '$2y$13$anw.orOjIrH40FK0wZsTwuII0BqdY4Lm4ZT40gXzHTBdM96t44Uui'),
(4, 'laryy@gmail.com', '[]', '$2y$13$gVptKNstdKadRNnulaBfgOjqbGqVnnTzCU4suSATSeGVFqdJwWrk2');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `article_categorie`
--
ALTER TABLE `article_categorie`
  ADD PRIMARY KEY (`article_id`,`categorie_id`),
  ADD KEY `IDX_934886107294869C` (`article_id`),
  ADD KEY `IDX_93488610BCF5E72D` (`categorie_id`);

--
-- Index pour la table `article_like`
--
ALTER TABLE `article_like`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1C21C7B27294869C` (`article_id`),
  ADD KEY `IDX_1C21C7B2A76ED395` (`user_id`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_67F068BC7294869C` (`article_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `article_like`
--
ALTER TABLE `article_like`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `commentaire`
--
ALTER TABLE `commentaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article_categorie`
--
ALTER TABLE `article_categorie`
  ADD CONSTRAINT `FK_934886107294869C` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_93488610BCF5E72D` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `article_like`
--
ALTER TABLE `article_like`
  ADD CONSTRAINT `FK_1C21C7B27294869C` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`),
  ADD CONSTRAINT `FK_1C21C7B2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD CONSTRAINT `FK_67F068BC7294869C` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
