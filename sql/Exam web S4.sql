DROP DATABASE IF EXISTS webMSD;
CREATE DATABASE webMSD;
USE webMSD;

CREATE TABLE admin (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(255),
  mdp VARCHAR(255)
);

CREATE TABLE fonds (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  montant FLOAT,
  date_ajout DATETIME
);

CREATE TABLE type_pret (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(255),
  taux_interet FLOAT,
  duree_mois INTEGER
);

CREATE TABLE clients (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(255),
  email VARCHAR(255),
  cin VARCHAR(255),
  date_inscription DATETIME
);

CREATE TABLE statut_prets (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  statut VARCHAR(255)
);

CREATE TABLE prets (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  client_id INTEGER,
  type_pret_id INTEGER,
  montant_emprunte FLOAT,
  interet_total FLOAT,
  date_emprunt DATETIME,
  statut INTEGER,
  FOREIGN KEY (client_id) REFERENCES clients(id),
  FOREIGN KEY (type_pret_id) REFERENCES type_pret(id),
  FOREIGN KEY (statut) REFERENCES statut_prets(id)
);

CREATE TABLE remboursements (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  pret_id INTEGER,
  montant FLOAT,
  date_remboursement DATETIME,
  FOREIGN KEY (pret_id) REFERENCES prets(id)
);

INSERT INTO type_pret (nom, taux_interet, duree_mois) VALUES
('Prêt professionnel', 5.0, 36);

INSERT INTO pret_statut (idpret, idstatut, date_modif)
VALUES (1, 1, '2025-07-07 10:30:00');


