<?php
require_once __DIR__ . '/../db.php';

class Remboursement
{
    public static function findById($idremboursement)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM remboursement WHERE idremboursement = ?");
        $stmt->execute([$idremboursement]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getRemboursementsEnAttente()
    {
        $db = getDB();
        $stmt = $db->prepare("
        SELECT r.*
        FROM remboursement r
        JOIN remboursement_statut rs ON rs.idremboursement = r.idremboursement
        JOIN (
            SELECT idremboursement, MAX(date_modif) AS max_date
            FROM remboursement_statut
            GROUP BY idremboursement
        ) latest ON latest.idremboursement = rs.idremboursement AND latest.max_date = rs.date_modif
        WHERE rs.idstatut = 1
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function insert($data)
    {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO remboursement (
        mois, annee, emprunt_restant, interet_mensuel, assurance, amortissement, echeance, valeur_nette, idpret
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $data['mois'],
            $data['annee'],
            $data['emprunt_restant'],
            $data['interet_mensuel'],
            $data['assurance'],
            $data['amortissement'],
            $data['echeance'],
            $data['valeur_nette'],
            $data['idpret']
        ]);
        return $db->lastInsertId();
    }

    public static function insertStatut($idremboursement, $idstatut)
    {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO remboursement_statut (
            idremboursement, idstatut, date_modif
        ) VALUES (?, ?, NOW())");
        $stmt->execute([$idremboursement, $idstatut]);
    }

    public static function inserEntrant($montant, $idmotif)
    {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO entrant (montant, date_, idmotif) VALUES (?, NOW(), ?)");
        $stmt->execute([$montant, $idmotif]);
        return $db->lastInsertId();
    }
}
