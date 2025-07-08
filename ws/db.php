<?php
function getDB()
{
    $host = '172.60.0.10';
    $dbname = 'db_s2_ETU003365';
    $username = 'ETU003365';
    $password = 'FnVGonkK';

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        // En cas d'erreur, retourne une erreur JSON propre
        http_response_code(500);
        echo json_encode(['error' => 'Erreur de connexion à la base de données', 'details' => $e->getMessage()]);
        exit;
    }
}

// $db = getDB();
// echo "Base utilisée : " . $db->query("SELECT DATABASE()")->fetchColumn();
