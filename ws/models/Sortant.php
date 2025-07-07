<?php
require_once __DIR__ . '/../db.php';

class Sortant{
    public static function insertSortant($data){
        $db = getDB();
        $stmt = $db->prepare('INSERT INTO sortant (date_,montant,idmotif,idpret) VALUES (?,?,?,?)');
        $stmt->execute([
            $data->date_,
            $data->montant,
            $data->idmotif,
            $data->idpret
        ]

        );
        return $db->lastInsertId();
    }
}