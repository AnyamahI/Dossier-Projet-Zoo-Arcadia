-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : dim. 16 mars 2025 à 16:12
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
-- Base de données : `zoo_arcadia`
--

-- --------------------------------------------------------

--
-- Structure de la table `animals`
--

CREATE TABLE `animals` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `habitat_id` int(11) NOT NULL,
  `last_checkup_date` date DEFAULT NULL,
  `species_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `animals`
--

INSERT INTO `animals` (`id`, `name`, `habitat_id`, `last_checkup_date`, `species_id`, `image`) VALUES
(58, 'Zéphyr', 3, NULL, 15, '/uploads/animals/1741735744_blop_guépard-zéphyr.png'),
(59, 'Gaya', 3, NULL, 15, '/uploads/animals/1741730063_blob_guépaed-gaya.png'),
(60, 'Kalgara', 2, NULL, 16, '/uploads/animals/1741737674_blop-kalgara.png'),
(61, 'Koda', 4, NULL, 17, '/uploads/animals/1741799291_blop-koda.png'),
(62, 'Awawa', 3, NULL, 18, '/uploads/animals/1741809785_Awawa_blob.jpg'),
(63, 'Nyanya', 3, NULL, 18, '/uploads/animals/1741809808_NyaNya_blob.png');

-- --------------------------------------------------------

--
-- Structure de la table `animal_visits`
--

CREATE TABLE `animal_visits` (
  `id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `visit_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `habitats`
--

CREATE TABLE `habitats` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `theme` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `habitats`
--

INSERT INTO `habitats` (`id`, `name`, `description`, `image`, `theme`) VALUES
(2, 'Jungle', 'Bienvenue au cœur de la jungle luxuriante, un monde mystérieux où la végétation est si dense que la lumière peine à percer la canopée. Les cris des singes résonnent dans les hauteurs, tandis que les panthères se faufilent silencieusement entre les lianes. Dans cette forêt tropicale foisonnante de vie, chaque feuille cache un trésor : des grenouilles colorées aux perroquets éclatants, tout un écosystème vibrant s’épanouit dans un ballet incessant de nature sauvage.', '../../uploads/habitats/1740153710_Jungle description.jpg', 'jungle.css'),
(3, 'Savane', 'Sous le soleil  brûlant de la savane, les vastes plaines dorées s\'étendent à perte de vue. Ici, les lions règnent en maîtres, tandis que les girafes se déplacent gracieusement entre les acacias. Les éléphants avancent en troupeau, levant de majestueux nuages de poussière. Cet habitat, chaud et aride, recrée fidèlement l’écosystème africain, où chaque espèce joue un rôle essentiel dans l’équilibre de la nature.', '../../uploads/habitats/1740152783_Savane description.jpg', 'savane.css'),
(4, 'Marais', 'Les marais sont un univers envoûtant, où l’eau et la terre s’entrelacent pour former un refuge unique. Cachés entre les roseaux, les crocodiles guettent leur proie, tandis que les hérons s’élancent avec grâce au-dessus des eaux calmes. Grenouilles et tortues partagent ces zones humides, où le silence n’est troublé que par le chant des insectes et le clapotis discret de l’eau. Un monde fascinant, où la nature s’adapte à la frontière entre terre et eau.', '../../uploads/habitats/1740153699_marais déscription.jpg', 'marais.css');

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `role` enum('admin','employee','veterinaire') NOT NULL,
  `resource` varchar(50) NOT NULL,
  `can_create` tinyint(1) DEFAULT 0,
  `can_read` tinyint(1) DEFAULT 1,
  `can_update` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `permissions`
--

INSERT INTO `permissions` (`id`, `role`, `resource`, `can_create`, `can_read`, `can_update`, `can_delete`) VALUES
(1, 'admin', 'services', 1, 1, 1, 1),
(2, 'admin', 'habitats', 1, 1, 1, 1),
(3, 'admin', 'animals', 1, 1, 1, 1),
(4, 'employee', 'services', 0, 1, 1, 0),
(5, 'employee', 'animals', 1, 1, 0, 0),
(6, 'veterinaire', 'animals', 1, 1, 1, 0),
(7, 'veterinaire', 'habitats', 0, 1, 1, 0),
(8, 'admin', 'users', 1, 1, 1, 1),
(9, 'employee', 'users', 0, 1, 0, 0),
(10, 'veterinaire', 'users', 0, 1, 0, 0),
(11, 'admin', 'reports', 1, 1, 1, 1),
(12, 'veterinaire', 'reports', 1, 1, 1, 1),
(13, 'admin', 'species', 1, 1, 1, 1),
(14, 'veterinaire', 'species', 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `image`) VALUES
(2, 'Petit Train', 'Faites le tour du zoo avec notre petit train\r\nqui roule super mega trop vite', '../../uploads/services/1739811757_Blop_petit-train.png'),
(3, 'Restaurant', 'Venez déguster des délicieux petit plat dans nos restaurants.', '../../uploads/services/1739811778_Blop_restauration.png'),
(5, 'visite guider', 'reservez votre visite guider gratiute ', '../../uploads/services/1739811795_blop_guide.png'),
(8, 'chant des grenouille verte', 'venez écouté chanter nos grenouille a tue-tête.\r\ntout les soir d\'ete entre 20h et 22h.', '../../uploads/services/1739809629_blop_rainette-verte.png');

-- --------------------------------------------------------

--
-- Structure de la table `species`
--

CREATE TABLE `species` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `scientific_name` varchar(255) NOT NULL,
  `food` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `page_link` varchar(255) DEFAULT NULL,
  `IUCN_status` varchar(10) DEFAULT NULL,
  `IUCN_image` varchar(255) DEFAULT NULL,
  `size` varchar(100) DEFAULT NULL,
  `weight` varchar(100) DEFAULT NULL,
  `lifespan` varchar(100) DEFAULT NULL,
  `population_status` varchar(100) DEFAULT NULL,
  `distribution` text DEFAULT NULL,
  `distribution_map` varchar(255) DEFAULT NULL,
  `family` varchar(255) DEFAULT NULL,
  `order_bio` varchar(255) DEFAULT NULL,
  `class_bio` varchar(255) DEFAULT NULL,
  `description_image` text DEFAULT NULL,
  `natural_habitat` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `secondary_image` varchar(255) DEFAULT NULL,
  `gestation` varchar(255) DEFAULT NULL,
  `habitat_id` int(11) DEFAULT NULL,
  `main_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `species`
--

INSERT INTO `species` (`id`, `name`, `scientific_name`, `food`, `description`, `image`, `page_link`, `IUCN_status`, `IUCN_image`, `size`, `weight`, `lifespan`, `population_status`, `distribution`, `distribution_map`, `family`, `order_bio`, `class_bio`, `description_image`, `natural_habitat`, `cover_image`, `secondary_image`, `gestation`, `habitat_id`, `main_image`) VALUES
(15, 'Guépard', 'Acinonyx jubatus', NULL, 'Le guépard (Acinonyx jubatus) est un félin élancé et athlétique, parfaitement adapté à la vitesse. Son corps fuselé, ses longues pattes et sa colonne vertébrale flexible lui permettent d\'atteindre des vitesses impressionnantes, jusqu’à 110 km/h en quelques secondes, faisant de lui l’animal terrestre le plus rapide. Son pelage jaune sable, parsemé de taches noires, lui offre un excellent camouflage dans les hautes herbes des savanes africaines. Ses yeux perçants, bordés de lignes noires en forme de larmes, réduisent l’éblouissement du soleil et améliorent sa vision lors des chasses diurnes. Contrairement aux autres grands félins, le guépard ne rugit pas mais communique par des miaulements, des sifflements et des ronronnements. Sa longue queue, marquée d’anneaux noirs, lui sert de gouvernail pour stabiliser ses virages lorsqu’il sprinte après ses proies. Agile, rapide et discret, le guépard est un prédateur redoutable, bien que vulnérable face aux lions et aux hyènes qui s’attaquent souvent à ses petits.', NULL, NULL, 'VU', '/uploads/species/1741725981_blop_VU.png', 'Hauteur au garrot : 65 à 80 cm.', '40 à 60 kg', 'Etat sauvage : env. 6 ans et 15 à 20 an captivité', 'En forte baisse', 'Afrique subsaharienne, Iran', '/uploads/species/1741725981_map_guépard.png', 'Félidés', 'Carnivores', 'Mammifères', '/uploads/species/1741725981_photo_guépard.png', 'Grandes plaines ouvertes', '/uploads/species/1741725981_img bg guépard.png', '/uploads/species/1741725981_blob_page-guépard.png', '3 à 3,5 mois pour 1 à 4 petits', 3, '/uploads/species/1741737512_blop_guepard.png'),
(16, 'Jaguar', 'Panthera onca', NULL, 'Le jaguar est le plus grand félin d’Amérique et le troisième plus grand au monde après le tigre et le lion. Il est souvent confondu avec le léopard, mais il est plus robuste, massif et puissant. Son pelage est jaune doré à orange, avec des rosettes noires contenant parfois un point central noir. Il existe aussi des jaguars mélaniques (entièrement noirs) appelés « panthères noires ».\r\n\r\nLe jaguar est un excellent nageur et aime l’eau, contrairement à la plupart des félins. C’est un prédateur solitaire et territorial, qui chasse principalement à l’aube et au crépuscule. Il joue un rôle essentiel dans son écosystème en régulant les populations d\'herbivores et de carnivores plus petits.', NULL, NULL, 'NT', '/uploads/species/1741736528_blop_NT.png', '75 à 90 cm au garrot', '80 à 120 kg', '12 à 15 ans à l\'état sauvage 20 à 25 ans en captivité', 'En diminution', 'Amérique latine', '/uploads/species/1741736528_map_jaguar.png', 'Félidés', 'Carnivores', 'Mammifères', '/uploads/species/1741736528_photo_jaguar.png', 'Forêts tropicales humides', '/uploads/species/1741736528_img bg jaguar.png', '/uploads/species/1741736528_blob_page_jaguar.png', '3 mois  2 à 5 petits', 2, '/uploads/species/1741737613_blop_jaguar.png'),
(17, 'Grizzli', 'Ursus arctos horribilis', NULL, 'Le grizzli est un grand ours au pelage épais, généralement brun clair à foncé, avec des reflets parfois dorés ou argentés. Il possède une bosse caractéristique au niveau des épaules, qui est un muscle puissant utilisé pour creuser et frapper. Son crâne est large, son museau allongé et ses griffes longues et incurvées lui permettent de creuser et de chasser efficacement. C’est un animal solitaire, sauf pendant la période de reproduction ou lorsqu’une femelle s’occupe de ses petits.\r\n\r\nLe grizzli est réputé pour sa force, son intelligence et son agilité malgré sa taille. Il est un excellent nageur et peut courir jusqu’à 55 km/h sur de courtes distances. Il joue un rôle clé dans son écosystème en dispersant les graines et en régulant les populations animales.', NULL, NULL, 'LC', '/uploads/species/1741798618_UICN LC.png', '1,80 à 2,80 mètres', '180 à 360 kg', '20 à 30 ans', 'Stable', 'Amérique du Nord', '/uploads/species/1741798618_map_grizzly.png', 'Ursidés', 'Carnivores', 'Mammifères', '/uploads/species/1741798618_photo_Grizzly.png', 'Forêts boréales, montagnes, plaines et les rivières', '/uploads/species/1741798618_page-grizzly_imgBg.png', '/uploads/species/1741798618_blob_grizzly.png', '6 à 8 mois pour 1 à 4 petits', 4, '/uploads/species/1741798862_blop_Grizzly.png'),
(18, 'Hyrax', 'Procaviidae', NULL, 'Le hyrax est un petit mammifère ressemblant à un rongeur, mais il est en réalité un proche parent des éléphants et des lamantins. Il possède un corps trapu, recouvert d\'un pelage dense variant du brun au gris selon l’espèce. Ses pattes courtes mais puissantes sont adaptées à l\'escalade, avec des doigts terminés par des coussinets adhérents, ce qui lui permet de grimper facilement sur les rochers ou les arbres.\r\nContrairement aux rongeurs, le hyrax possède des incisives allongées qui ressemblent à de petites défenses. Il est diurne et vit souvent en groupes sociaux pour se protéger des prédateurs comme les aigles, les serpents et les félins. Malgré sa petite taille, il est territorial et peut émettre des cris perçants pour avertir ses congénères du danger.', NULL, NULL, 'LC', '/uploads/species/1741809513_UICN LC.png', '30 à 60 cm', '2 et 5 kg', 'À l’état sauvage , environ 7 à 12 ans et jusqu’à 14 ans en captivité', 'Stable', 'Afrique et au Moyen-Orient', '/uploads/species/1741809513_Hyrax_map.png', 'Procaviidae', 'Hyracoidea', 'Mammifères', '/uploads/species/1741809513_Hyrax_description.jpg', 'zones rocheuses, forêts et les régions boisées, zones plus ouvertes et arides', '/uploads/species/1741809513_Hyrax bg.jpg', '/uploads/species/1741809513_Hyrax_secondaire.png', '6 à 7 mois pour à 1 à 4 petits', 3, '/uploads/species/1741809513_Hyrax_principal.png'),
(20, 'Felin', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','veterinaire','employee') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(255) NOT NULL DEFAULT 'Nom. Inconnu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `created_at`, `name`) VALUES
(7, 'test@test.com', '$2y$10$kopv6xIHfpqiTbi4VkShb.s1SMLpFIkvH81Xd62.Gp46ssX7j3XOm', 'admin', '2025-01-28 01:47:26', 'Mr. Big Bosse'),
(28, 'vet@vet.com', '$2y$10$XT4iTQhoQZbS1R3FZwixaO7ajgHPSlAc0XcZhkTcTn6Nx3ZwW87dW', 'veterinaire', '2025-03-08 14:45:26', 'Dr. Veto'),
(29, 'empl@empl.com', '$2y$10$rXQNX9Si9g2OH8wOP.6Jj.SUdjI6dqSL2BdrqkAtVY3SBCca1tkBq', 'employee', '2025-03-08 15:02:48', 'Nom. Inconnu');

-- --------------------------------------------------------

--
-- Structure de la table `vet_reports`
--

CREATE TABLE `vet_reports` (
  `id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `vet_id` int(11) NOT NULL,
  `state` varchar(255) NOT NULL,
  `food` varchar(255) DEFAULT NULL,
  `weight` decimal(10,2) NOT NULL,
  `visit_date` date NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vet_reports`
--

INSERT INTO `vet_reports` (`id`, `animal_id`, `vet_id`, `state`, `food`, `weight`, `visit_date`, `details`, `created_at`) VALUES
(8, 58, 28, 'Plein de vie', 'Lapin de 6 semains', 600.00, '2025-03-12', 'Les lapins été plus vieux :/', '2025-03-11 23:07:17');

-- --------------------------------------------------------

--
-- Structure de la table `visitor_reviews`
--

CREATE TABLE `visitor_reviews` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(100) NOT NULL,
  `review` text NOT NULL,
  `is_validated` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `visitor_reviews`
--

INSERT INTO `visitor_reviews` (`id`, `pseudo`, `review`, `is_validated`, `created_at`) VALUES
(1, 'visiteur', 'je test les avis en ligne', 1, '2025-01-29 18:10:03'),
(2, 'administrateur', 'et non je ne suis pas administrateur', 1, '2025-01-29 18:21:08'),
(4, 'Paul', 'Super zoo, très bien entretenu !', 1, '2025-01-30 18:51:19');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `animals`
--
ALTER TABLE `animals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `habitat_id` (`habitat_id`),
  ADD KEY `fk_species` (`species_id`);

--
-- Index pour la table `animal_visits`
--
ALTER TABLE `animal_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `animal_id` (`animal_id`);

--
-- Index pour la table `habitats`
--
ALTER TABLE `habitats`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `species`
--
ALTER TABLE `species`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `habitat_id` (`habitat_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `vet_reports`
--
ALTER TABLE `vet_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vet_id` (`vet_id`),
  ADD KEY `vet_reports_ibfk_1` (`animal_id`);

--
-- Index pour la table `visitor_reviews`
--
ALTER TABLE `visitor_reviews`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `animals`
--
ALTER TABLE `animals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT pour la table `animal_visits`
--
ALTER TABLE `animal_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `habitats`
--
ALTER TABLE `habitats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `species`
--
ALTER TABLE `species`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `vet_reports`
--
ALTER TABLE `vet_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `visitor_reviews`
--
ALTER TABLE `visitor_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `animals`
--
ALTER TABLE `animals`
  ADD CONSTRAINT `animals_ibfk_1` FOREIGN KEY (`habitat_id`) REFERENCES `habitats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_species` FOREIGN KEY (`species_id`) REFERENCES `species` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `animal_visits`
--
ALTER TABLE `animal_visits`
  ADD CONSTRAINT `animal_visits_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `species`
--
ALTER TABLE `species`
  ADD CONSTRAINT `species_ibfk_1` FOREIGN KEY (`habitat_id`) REFERENCES `habitats` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `vet_reports`
--
ALTER TABLE `vet_reports`
  ADD CONSTRAINT `vet_reports_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vet_reports_ibfk_2` FOREIGN KEY (`vet_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
