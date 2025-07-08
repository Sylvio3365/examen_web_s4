<?php

require_once  __DIR__ . '/../models/Remboursement.php';

class RemboursementController
{
    public static function liste()
    {
        $page = 'remboursement/index';
        Flight::render('template/index', ['page' => $page]);
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

    public static function setStatus($id)
    {
        Remboursement::insertStatut($id, 2);
        $remboursement = Remboursement::findById($id);
        Remboursement::inserEntrant($remboursement['echeance'], 3);
    }
}
