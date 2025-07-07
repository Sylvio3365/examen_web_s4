DROP DATABASE IF EXISTS webSMD;

CREATE DATABASE webSMD;

USE webSMD;

-- Table Client
CREATE TABLE client (
    idclient INT AUTO_INCREMENT,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    dtn DATE,
    PRIMARY KEY (idclient)
);

-- Table Type de prêt
CREATE TABLE typepret (
    idtypepret INT AUTO_INCREMENT,
    nom VARCHAR(50),
    taux_annuel DECIMAL(15, 2),
    montant_min DECIMAL(15, 2),
    montant_max DECIMAL(15, 2),
    duree_max DECIMAL(15, 2),
    deleted_at DATE,
    PRIMARY KEY (idtypepret)
);

-- Table Prêt
CREATE TABLE pret (
    idpret INT AUTO_INCREMENT,
    duree INT,
    montant DECIMAL(15, 2),
    idtypepret INT NOT NULL,
    idclient INT NOT NULL,
    delais INT DEFAULT 0,
    PRIMARY KEY (idpret),
    FOREIGN KEY (idtypepret) REFERENCES typepret (idtypepret),
    FOREIGN KEY (idclient) REFERENCES client (idclient)
);

-- Table Remboursement (avec correction du nom de clé primaire)
CREATE TABLE remboursement (
    idremboursement INT AUTO_INCREMENT,
    mois INT,
    annee INT,
    emprunt_restant DECIMAL(25, 2),
    interet_mensuel DECIMAL(15, 2),
    amortissement DECIMAL(15, 2),
    echeance DECIMAL(15, 2) NOT NULL,
    valeur_nette VARCHAR(50),
    idpret INT NOT NULL,
    PRIMARY KEY (idremboursement),
    FOREIGN KEY (idpret) REFERENCES pret (idpret)
);

-- Table Motif
CREATE TABLE motif (
    idmotif INT AUTO_INCREMENT,
    motif VARCHAR(50),
    PRIMARY KEY (idmotif)
);

-- Table Statut
CREATE TABLE statut (
    idstatut INT AUTO_INCREMENT,
    valeur VARCHAR(50),
    PRIMARY KEY (idstatut)
);

-- Table Entrant
CREATE TABLE entrant (
    identrant INT AUTO_INCREMENT,
    montant DECIMAL(25, 2),
    date_ DATE,
    idmotif INT NOT NULL,
    PRIMARY KEY (identrant),
    FOREIGN KEY (idmotif) REFERENCES motif (idmotif)
);

-- Table Sortant
CREATE TABLE sortant (
    idsortant INT AUTO_INCREMENT,
    date_ DATE,
    montant DECIMAL(25, 2) NOT NULL,
    idmotif INT NOT NULL,
    idpret INT NOT NULL,
    PRIMARY KEY (idsortant),
    UNIQUE (idpret),
    FOREIGN KEY (idmotif) REFERENCES motif (idmotif),
    FOREIGN KEY (idpret) REFERENCES pret (idpret)
);

-- Table Prêt_statut
CREATE TABLE pret_statut (
    idpret INT,
    idstatut INT,
    date_modif DATE,
    PRIMARY KEY (idpret, idstatut),
    FOREIGN KEY (idpret) REFERENCES pret (idpret),
    FOREIGN KEY (idstatut) REFERENCES statut (idstatut)
);

-- Table Remboursement_statut
CREATE TABLE remboursement_statut (
    idremboursement INT,
    idstatut INT,
    date_modif DATE,
    PRIMARY KEY (idremboursement, idstatut),
    FOREIGN KEY (idremboursement) REFERENCES remboursement (idremboursement),
    FOREIGN KEY (idstatut) REFERENCES statut (idstatut)
);

-- Insertion dans Statut
INSERT INTO
    statut (valeur)
VALUES ('En attente'),
    ('Validé'),
    ('Annulé');

-- Insertion dans Motif
INSERT INTO
    motif (motif)
VALUES ('Prêt'),
    ('Ajout de fonds'),
    ('Remboursement');

