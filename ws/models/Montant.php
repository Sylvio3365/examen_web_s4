<?php
require_once __DIR__ . '/../db.php';

class Montant
{
    public static function getSortant($datedebut, $datefin)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT SUM(montant) as total FROM sortant WHERE date_ BETWEEN ? AND ?");
        $stmt->execute([$datedebut, $datefin]);
        $row = $stmt->fetch();
        return $row ? $row['total'] : 0;
    }

    public static function getEntrant($datedebut, $datefin)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT SUM(montant) as total FROM entrant WHERE date_ BETWEEN ? AND ?");
        $stmt->execute([$datedebut, $datefin]);
        $row = $stmt->fetch();
        return $row ? $row['total'] : 0;
    }
}
