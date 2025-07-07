ALTER TABLE typepret ADD COLUMN deleted_at DATE;

ALTER TABLE typepret ADD COLUMN taux_assurance FLOAT DEFAULT 0;

ALTER TABLE remboursement ADD COLUMN assurance FLOAT DEFAULT 0;

ALTER TABLE remboursement MODIFY COLUMN valeur_nette FLOAT DEFAULT 0;


CREATE TABLE pret_statut (
    idpret_statut INT AUTO_INCREMENT,
    date_modif DATE,
    idstatut INT NOT NULL,
    idpret INT NOT NULL,
    PRIMARY KEY (idpret_statut),
    FOREIGN KEY (idstatut) REFERENCES statut (idstatut),
    FOREIGN KEY (idpret) REFERENCES pret (idpret)
);