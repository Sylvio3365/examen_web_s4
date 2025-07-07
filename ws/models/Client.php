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
}
