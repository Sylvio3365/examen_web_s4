<?php
require_once __DIR__ . '/../db.php';

class Client
{
    public static function getAll()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM client");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM client WHERE idclient = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO client (nom, prenom, dtn) VALUES (?, ?, ?)");
        $stmt->execute([
            $data->nom,
            $data->prenom,
            $data->dtn
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data)
    {
        $db = getDB();
        $stmt = $db->prepare("UPDATE client SET nom = ?, prenom = ?, dtn = ? WHERE idclient = ?");
        $stmt->execute([
            $data->nom,
            $data->prenom,
            $data->dtn,
            $id
        ]);
    }

    public static function delete($id)
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM client WHERE idclient = ?");
        $stmt->execute([$id]);
    }
}