<?php
require_once __DIR__ . '/../db.php';

class TypePret {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM typepret WHERE deleted_at IS NULL");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM typepret WHERE idtypepret = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO typepret (nom, taux_annuel, montant_min, montant_max, duree_max) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->nom, 
            $data->taux_annuel, 
            $data->montant_min, 
            $data->montant_max, 
            $data->duree_max
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE typepret SET nom = ?, taux_annuel = ?, montant_min = ?, montant_max = ?, duree_max = ? WHERE idtypepret = ? AND deleted_at IS NULL");
        $stmt->execute([
            $data->nom, 
            $data->taux_annuel, 
            $data->montant_min, 
            $data->montant_max, 
            $data->duree_max, 
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE typepret SET deleted_at = CURRENT_DATE() WHERE idtypepret = ?");
        $stmt->execute([$id]);
    }
}