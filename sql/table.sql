CREATE TABLE client (
    idclient INT AUTO_INCREMENT,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    dtn DATE,
    PRIMARY KEY (idclient)
);

CREATE TABLE typepret (
    idtypepret INT AUTO_INCREMENT,
    nom VARCHAR(50),
    taux_annuel DECIMAL(15, 2),
    montant_min DECIMAL(15, 2),
    montant_max DECIMAL(15, 2),
    duree_max DECIMAL(15, 2),
    PRIMARY KEY (idtypepret)
);

CREATE TABLE pret (
    idpret INT AUTO_INCREMENT,
    duree INT,
    montant DECIMAL(15, 2),
    idtypepret INT NOT NULL,
    idclient INT NOT NULL,
    PRIMARY KEY (idpret),
    FOREIGN KEY (idtypepret) REFERENCES typepret (idtypepret),
    FOREIGN KEY (idclient) REFERENCES client (idclient)
);

CREATE TABLE operation (
    idoperation INT AUTO_INCREMENT,
    mois INT,
    annee INT,
    emprunt_restant DECIMAL(25, 2),
    interet_mensuel DECIMAL(15, 2),
    amortissement DECIMAL(15, 2),
    echeance DECIMAL(15, 2) NOT NULL,
    valeur_nette VARCHAR(50),
    idpret INT NOT NULL,
    PRIMARY KEY (idoperation),
    FOREIGN KEY (idpret) REFERENCES pret (idpret)
);

CREATE TABLE motif (
    idmotif INT AUTO_INCREMENT,
    motif VARCHAR(50),
    PRIMARY KEY (idmotif)
);

CREATE TABLE statut (
    idstatut INT AUTO_INCREMENT,
    valeur VARCHAR(50),
    PRIMARY KEY (idstatut)
);

CREATE TABLE entrant (
    identrant INT AUTO_INCREMENT,
    montant DECIMAL(25, 2),
    date_ DATE,
    idmotif INT NOT NULL,
    PRIMARY KEY (identrant),
    FOREIGN KEY (idmotif) REFERENCES motif (idmotif)
);

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

CREATE TABLE pret_statut (
    idpret INT,
    idstatut INT,
    date_modif DATE,
    PRIMARY KEY (idpret, idstatut),
    FOREIGN KEY (idpret) REFERENCES pret (idpret),
    FOREIGN KEY (idstatut) REFERENCES statut (idstatut)
);

CREATE TABLE operation_statut (
    idoperation INT,
    idstatut INT,
    date_modif DATE,
    PRIMARY KEY (idoperation, idstatut),
    FOREIGN KEY (idoperation) REFERENCES operation (idoperation),
    FOREIGN KEY (idstatut) REFERENCES statut (idstatut)
);