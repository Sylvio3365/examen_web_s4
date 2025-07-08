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

    // Méthode pour afficher la page HTML
    public static function listeAvecPrets()
    {
        try {
            // Rendre la vue HTML directement
            Flight::render('clients/liste_avec_prets');
            
        } catch (Exception $e) {
            // En cas d'erreur, afficher une page d'erreur
            echo "Erreur lors de l'affichage de la page : " . $e->getMessage();
        }
    }

    // Méthode pour retourner les données JSON (utilisée par AJAX)
    public static function getClientsAvecPretsJson()
    {
        try {
            $clients = Client::getAllWithLoans();
            
            // Formatage des données pour la réponse JSON
            $response = [
                'status' => 'success',
                'data' => [
                    'clients' => $clients
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            Flight::json($response);
            
        } catch (Exception $e) {
            // En cas d'erreur, retourner un JSON d'erreur
            Flight::json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des données',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ], 500);
        }
    }
}