<?php

require_once  __DIR__ . '/../models/Remboursement.php';

class RemboursementController
{
    public static function formulaireFond()
    {
        Flight::render('remboursement/index');
    }

    public static function getEnAttente()
    {
        try {
            $resultats = Remboursement::getRemboursementsEnAttente();
            Flight::json($resultats);
        } catch (Exception $e) {
            Flight::json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
