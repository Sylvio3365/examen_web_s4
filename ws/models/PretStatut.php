<?php
require_once __DIR__ . '/../db.php';

class PretStatut{
    public static function insertPretStatut($data){
        $db = getDB();
        $stmt = $db->prepare('INSERT INTO pret_statut (idpret, idstatut, date_modif) VALUES (?, ?, NOW())');
        $stmt->execute([
            $data->idpret,
            $data->idstatut
        ]);
        return $db->lastInsertId();
    }
    
}