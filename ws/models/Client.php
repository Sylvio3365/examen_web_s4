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

    public static function getAllWithLoans()
    {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT c.*, 
                COUNT(p.idpret) AS nombre_prets,
                SUM(p.montant) AS total_montant
            FROM client c
            LEFT JOIN pret p ON c.idclient = p.idclient
            GROUP BY c.idclient
            ORDER BY c.nom, c.prenom
        ");
        $stmt->execute();
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($clients as &$client) {
            $stmt = $db->prepare("
                SELECT p.*, tp.nom AS type_pret,
                       (SELECT ps.date_modif 
                        FROM pret_statut ps 
                        WHERE ps.idpret = p.idpret 
                        ORDER BY ps.date_modif DESC 
                        LIMIT 1) AS derniere_modification
                FROM pret p
                JOIN typepret tp ON p.idtypepret = tp.idtypepret
                WHERE p.idclient = ?
                ORDER BY derniere_modification DESC
            ");
            $stmt->execute([$client['idclient']]);
            $client['prets'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $clients;
    }
}

