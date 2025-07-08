<?php
require_once __DIR__ . '/../models/Fond.php';
require_once __DIR__ . '/../helpers/Utils.php';

class FondController
{
    public static function formulaireFond()
    {
        $page = 'Fond/ajouterFond'; // Chemin relatif à `views/`
        Flight::render('template/index', ['page' => $page]);
    }

    public static function insertFond()
    {
        try {
            $montant = $_POST['montant'] ?? null;
            $date_ = $_POST['date_'] ?? null;

            // Validation plus stricte
            if (!$montant || !$date_ || !is_numeric($montant)) {
                Flight::json(['error' => 'Données manquantes ou invalides'], 400);
                return;
            }

            $data = (object) [
                'montant' => floatval($montant),
                'date_' => $date_
            ];

            $id = Fond::insertFond($data);
            Flight::json(['message' => 'Fond ajouté avec succès', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

    public static function getCapitalActuel()
    {
        try {
            $sommeMontantEntrant = Fond::getSommeMontantEntrant();
            $sommeMontantSortant = Fond::getSommeMontantSortant();

            $TotalMontantActuel = $sommeMontantEntrant - $sommeMontantSortant;

            // Renvoyer le résultat au frontend
            Flight::json(['capital' => $TotalMontantActuel]);
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }
}
