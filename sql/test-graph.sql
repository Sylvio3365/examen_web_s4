INSERT INTO client (nom, prenom, dtn) VALUES 
('Dupont', 'Jean', '1985-05-15'),
('Martin', 'Sophie', '1990-08-22'),
('Bernard', 'Pierre', '1978-03-10'),
('Petit', 'Marie', '1992-11-30');

INSERT INTO typepret (nom, taux_annuel, montant_min, montant_max, duree_max) VALUES 
('Prêt personnel', 5.00, 1000.00, 50000.00, 60),
('Prêt immobilier', 2.50, 50000.00, 500000.00, 240),
('Prêt étudiant', 1.50, 500.00, 20000.00, 120);

INSERT INTO pret (duree, montant, idtypepret, idclient, delais) VALUES 
(36, 10000.00, 1, 1, 0),
(120, 15000.00, 3, 2, 6),
(240, 200000.00, 2, 3, 0),
(60, 25000.00, 1, 4, 3);

-- Pour le prêt 1 (36 mois)
INSERT INTO remboursement (mois, annee, emprunt_restant, interet_mensuel, amortissement, echeance, valeur_nette, idpret) VALUES
(1, 2023, 9741.58, 41.67, 258.42, 300.09, 'payé', 1),
(2, 2023, 9481.15, 40.59, 259.50, 300.09, 'payé', 1),
(3, 2023, 9218.68, 39.50, 260.59, 300.09, 'payé', 1);

-- Pour le prêt 2 (120 mois avec 6 mois de délai)
INSERT INTO remboursement (mois, annee, emprunt_restant, interet_mensuel, amortissement, echeance, valeur_nette, idpret) VALUES
(7, 2023, 14900.00, 18.75, 100.00, 118.75, 'payé', 2),
(8, 2023, 14800.00, 18.63, 100.12, 118.75, 'payé', 2),
(9, 2023, 14700.00, 18.50, 100.25, 118.75, 'payé', 2);

-- Pour le prêt 3 (240 mois)
INSERT INTO remboursement (mois, annee, emprunt_restant, interet_mensuel, amortissement, echeance, valeur_nette, idpret) VALUES
(1, 2023, 199583.33, 416.67, 416.67, 833.34, 'payé', 3),
(2, 2023, 199166.66, 415.80, 417.54, 833.34, 'payé', 3),
(3, 2023, 198749.99, 414.93, 418.41, 833.34, 'payé', 3);

-- Pour le prêt 4 (60 mois avec 3 mois de délai)
INSERT INTO remboursement (mois, annee, emprunt_restant, interet_mensuel, amortissement, echeance, valeur_nette, idpret) VALUES
(4, 2023, 24900.00, 104.17, 100.00, 204.17, 'payé', 4),
(5, 2023, 24800.00, 103.75, 100.42, 204.17, 'payé', 4),
(6, 2023, 24700.00, 103.33, 100.84, 204.17, 'payé', 4);


-- Ajout de données supplémentaires pour tester le filtre
INSERT INTO remboursement (mois, annee, emprunt_restant, interet_mensuel, amortissement, echeance, valeur_nette, idpret) VALUES
(4, 2023, 9741.58, 41.67, 258.42, 300.09, 'payé', 1),
(5, 2023, 9481.15, 40.59, 259.50, 300.09, 'payé', 1),
(6, 2023, 9218.68, 39.50, 260.59, 300.09, 'payé', 1),
(1, 2024, 8999.99, 38.41, 261.68, 300.09, 'payé', 1),
(2, 2024, 8766.66, 37.50, 262.59, 300.09, 'payé', 1),
(3, 2024, 8533.33, 36.53, 263.56, 300.09, 'payé', 1);