<?php
require_once __DIR__ . '/../db.php';

class Pret
{
    public static function getAll()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT p.*, tp.nom AS type_pret, tp.taux_annuel FROM pret p JOIN typepret tp ON p.idtypepret = tp.idtypepret WHERE p.idpret = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO pret (duree, montant, idtypepret, idclient, delais) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->duree,
            $data->montant,
            $data->idtypepret,
            $data->idclient,
            $data->delais ?? 0
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data)
    {
        $db = getDB();
        $stmt = $db->prepare("UPDATE pret SET duree = ?, montant = ?, idtypepret = ?, idclient = ?,delais = ? WHERE idpret = ?");
        $stmt->execute([
            $data->duree,
            $data->montant,
            $data->idtypepret,
            $data->idclient,
            $data->delais ?? 0,
            $id
        ]);
    }

    public static function delete($id)
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM pret WHERE idpret = ?");
        $stmt->execute([$id]);
    }

    public static function getAllTypes()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM typepret WHERE deleted_at IS NULL");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function calculerAmortissement($idPret)
    {
        $pret = self::getById($idPret);
        $tauxMensuel = $pret['taux_annuel'] / 100 / 12;
        $duree = $pret['duree'];
        $montant = $pret['montant'];
        $delais = $pret['delais'] ?? 0;

        $mensualite = $montant * $tauxMensuel * pow(1 + $tauxMensuel, $duree) / (pow(1 + $tauxMensuel, $duree) - 1);

        $tableau = [];
        $capitalRestant = $montant;

        for ($i = 1; $i <= $duree + $delais; $i++) {
            if ($i <= $delais) {
                $tableau[] = [
                    'mois' => $i,
                    'annee' => date('Y', strtotime("+$i months")),
                    'echeance' => 0,
                    'interet' => 0,
                    'amortissement' => 0,
                    'capital_restant' => $capitalRestant
                ];
            } else {
                $moisEffectif = $i - $delais;
                $interet = $capitalRestant * $tauxMensuel;
                $amortissement = $mensualite - $interet;
                $capitalRestant -= $amortissement;

                $tableau[] = [
                    'mois' => $i,
                    'annee' => date('Y', strtotime("+$i months")),
                    'echeance' => $mensualite,
                    'interet' => $interet,
                    'amortissement' => $amortissement,
                    'capital_restant' => max($capitalRestant, 0)
                ];
            }
        }

        return $tableau;
    }

    public static function getInteretsParPeriode($dateDebut, $dateFin)
    {
        $db = getDB();
        $sql = "SELECT 
                    r.mois, 
                    r.annee, 
                    SUM(r.interet_mensuel) AS total_interets,
                    COUNT(r.idremboursement) AS nombre_remboursements
                FROM remboursement r
                JOIN pret p ON r.idpret = p.idpret
                WHERE (r.annee > YEAR(:dateDebut) OR (r.annee = YEAR(:dateDebut) AND r.mois >= MONTH(:dateDebut)))
                AND (r.annee < YEAR(:dateFin) OR (r.annee = YEAR(:dateFin) AND r.mois <= MONTH(:dateFin)))
                GROUP BY r.annee, r.mois
                ORDER BY r.annee, r.mois";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':dateDebut' => $dateDebut,
            ':dateFin' => $dateFin
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllInterets()
    {
        $db = getDB();
        $sql = "SELECT 
                r.mois, 
                r.annee, 
                SUM(r.interet_mensuel) AS total_interets,
                COUNT(r.idremboursement) AS nombre_remboursements
            FROM remboursement r
            JOIN pret p ON r.idpret = p.idpret
            GROUP BY r.annee, r.mois
            ORDER BY r.annee, r.mois";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}