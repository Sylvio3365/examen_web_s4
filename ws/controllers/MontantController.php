<?php

require_once __DIR__ . '/../models/Montant.php';

class MontantController
{

    public static function montanttotal()
    {
        Flight::render('template/index', ['page' => 'montant/index']);
    }

    public static function getMontantParPeriode()
    {
        $moisDebut   = $_GET['mois_debut'] ?? null;
        $anneeDebut  = $_GET['annee_debut'] ?? null;
        $moisFin     = $_GET['mois_fin'] ?? null;
        $anneeFin    = $_GET['annee_fin'] ?? null;

        if (!$moisDebut || !$anneeDebut || !$moisFin || !$anneeFin) {
            Flight::json(["error" => "Tous les paramètres 'mois_debut', 'annee_debut', 'mois_fin', 'annee_fin' sont requis."], 400);
            return;
        }

        $dateDebut = new DateTime("$anneeDebut-$moisDebut-01");
        $dateFin   = new DateTime("$anneeFin-$moisFin-01");

        if ($dateDebut > $dateFin) {
            Flight::json(["error" => "La date de début doit être avant la date de fin."], 400);
            return;
        }

        $resultat = [];

        while ($dateDebut <= $dateFin) {
            $debutMois = $dateDebut->format('Y-m-01');
            $finMois   = $dateDebut->format('Y-m-t');
            $moisNom   = $dateDebut->format('F');
            $annee     = $dateDebut->format('Y');

            $entrant = Montant::getEntrant($debutMois, $finMois);
            $sortant = Montant::getSortant($debutMois, $finMois);

            $resultat[] = [
                "annee" => $annee,
                "mois" => $moisNom,
                "entrant" => (float)$entrant,
                "sortant" => (float)$sortant
            ];

            $dateDebut->modify('+1 month');
        }

        Flight::json($resultat);
    }
}
