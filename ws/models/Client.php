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
        
        // Récupérer tous les clients (même sans prêts validés)
        $stmt = $db->prepare("
            SELECT c.*, 
                0 AS nombre_prets,
                0 AS total_montant
            FROM client c
            ORDER BY c.nom, c.prenom
        ");
        $stmt->execute();
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Pour chaque client, récupérer uniquement ses prêts validés
        foreach ($clients as &$client) {
            $stmt = $db->prepare("
                SELECT p.*, 
                       tp.nom AS type_pret,
                       latest_status.idstatut AS statut,
                       latest_status.date_modif AS derniere_modification
                FROM pret p
                JOIN typepret tp ON p.idtypepret = tp.idtypepret
                JOIN (
                    SELECT ps1.idpret, ps1.idstatut, ps1.date_modif
                    FROM pret_statut ps1
                    INNER JOIN (
                        SELECT idpret, MAX(date_modif) AS max_date, MAX(idpret_statut) AS max_id
                        FROM pret_statut
                        GROUP BY idpret
                    ) ps2 ON ps1.idpret = ps2.idpret 
                         AND ps1.date_modif = ps2.max_date 
                         AND ps1.idpret_statut = ps2.max_id
                ) latest_status ON p.idpret = latest_status.idpret
                WHERE p.idclient = ? AND latest_status.idstatut = 2
                ORDER BY latest_status.date_modif DESC
            ");
            $stmt->execute([$client['idclient']]);
            $client['prets'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculer le nombre de prêts et le montant total pour les prêts validés uniquement
            $client['nombre_prets'] = count($client['prets']);
            $client['total_montant'] = array_sum(array_column($client['prets'], 'montant'));
        }

        return $clients;
    }
}