<?php
function getDB()
{
    $host = '172.60.0.10';
    $dbname = 'db_s2_ETU003289';
    $username = 'ETU003289';
    $password = 'Rxwey1b7';

    try {
        return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die(json_encode(['error' => $e->getMessage()]));
    }
}
