<?php
require_once __DIR__ . '/../db.php';

class Fond {
    public static function getAll(){
        $db = getDB();
        $stmt = $db->query("SELECT * FROM entrant WHERE idmotif= 2");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insertFond($data) {
        try {
            $db = getDB();
            $stmt = $db->prepare("
                INSERT INTO entrant (montant, date_, idmotif)
                VALUES (?, ?, 2)
            ");
            $result = $stmt->execute([$data->montant, $data->date_]);
            
            if (!$result) {
                throw new Exception("Erreur lors de l'insertion");
            }
            
            return $db->lastInsertId();
        } catch (Exception $e) {
            error_log("Erreur insertFond: " . $e->getMessage());
            throw $e;
        }
    }
    

}