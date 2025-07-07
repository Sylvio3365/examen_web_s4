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

    public function interets()
    {
        $pretModel = new Pret();

        // Valeurs par défaut (derniers 12 mois)
        $dateDebut = date('Y-m-d', strtotime('-12 months'));
        $dateFin = date('Y-m-d');

        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dateDebut = $_POST['date_debut'] ?? $dateDebut;
            $dateFin = $_POST['date_fin'] ?? $dateFin;
        }

        $interets = $pretModel->getInteretsParPeriode($dateDebut, $dateFin);

        Flight::render('interets', [
            'interets' => $interets,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin
        ]);
    }

    public static function addPret()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = (object) [
                'montant'     => $_POST['montant'] ?? null,
                'duree'       => $_POST['duree'] ?? null,
                'idtypepret'  => $_POST['idtypepret'] ?? null,
                'idclient'    => $_POST['idclient'] ?? null,
                'delais'      => $_POST['delais'] ?? 0
            ];
            try {
                $id = Pret::create($data);
                Pret::insertPretEnAttente($id);
                Flight::json([
                    'status' => 'success',
                    'id' => $id
                ]);
            } catch (Exception $e) {
                Flight::json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }
        } else {
            Flight::json(['status' => 'error', 'message' => 'Méthode invalide'], 405);
        }
    }
}
