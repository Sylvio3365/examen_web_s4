CREATE TABLE `admin` (
  `id` integer PRIMARY KEY,
  `username` varchar(255),
  `mdp` varchar(255)
);

CREATE TABLE `fonds` (
  `id` integer PRIMARY KEY,
  `montant` float,
  `date_ajout` datetime
);

CREATE TABLE `type_pret` (
  `id` integer PRIMARY KEY,
  `nom` varchar(255),
  `taux_interet` float,
  `duree_mois` integer
);

CREATE TABLE `clients` (
  `id` integer PRIMARY KEY,
  `nom` varchar(255),
  `email` varchar(255),
  `cin` varchar(255),
  `date_inscription` datetime
);

CREATE TABLE `prets` (
  `id` integer PRIMARY KEY,
  `client_id` integer,
  `type_pret_id` integer,
  `montant_emprunte` float,
  `interet_total` float,
  `date_emprunt` datetime,
  `statut` integer
);

CREATE TABLE `remboursements` (
  `id` integer PRIMARY KEY,
  `pret_id` integer,
  `montant` float,
  `date_remboursement` datetime
);

CREATE TABLE `statut_prets` (
  `id` integer,
  `statut` varchar(255)
);

ALTER TABLE `prets` ADD FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

ALTER TABLE `prets` ADD FOREIGN KEY (`type_pret_id`) REFERENCES `type_pret` (`id`);

ALTER TABLE `prets` ADD FOREIGN KEY (`statut`) REFERENCES `statut_prets` (`id`);

ALTER TABLE `remboursements` ADD FOREIGN KEY (`pret_id`) REFERENCES `prets` (`id`);
