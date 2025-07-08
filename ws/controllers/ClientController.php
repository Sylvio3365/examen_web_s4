<?php
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Pret.php';

class ClientController
{
    
    public static function getAll()
    {
        $clients = Client::getAll();
        Flight::json($clients);
    }

    public static function listeAvecPrets()
    {
        try {
            $clients = Client::getAllWithLoans();
            Flight::render('clients/liste_avec_prets', ['clients' => $clients]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors de la récupération des données: ' . $e->getMessage());
        }
    }
}
