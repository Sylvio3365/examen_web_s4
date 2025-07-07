<?php
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../helpers/Utils.php';
require_once  __DIR__ . '/../models/Pret.php';

class PretController
{
    public static function goIndex()
    {
        Flight::render('pret/index');
    }

    public static function goInteret() {
        Flight::render('pret/interets');
    }

    public static function interets() {
        $dateDebut = Flight::request()->data->date_debut ?? '';
        $dateFin = Flight::request()->data->date_fin ?? '';
        
        if (!empty($dateDebut) && !empty($dateFin)) {
            $interets = Pret::getInteretsParPeriode($dateDebut, $dateFin);
        } else {
            $interets = Pret::getAllInterets();
        }
        
        Flight::json([
            'interets' => $interets,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin
        ]);
    }
}