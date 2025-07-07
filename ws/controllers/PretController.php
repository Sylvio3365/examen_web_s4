<?php
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../helpers/Utils.php';
require_once  __DIR__ . '/../models/Pret.php';
require_once  __DIR__ . '/../models/Remboursement.php';
class PretController
{
    public static function goIndex()
    {
        Flight::render('pret/index');
    }

    public static function goInteret()
    {
        Flight::render('pret/interets');
    }

    public static function interets()
    {
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

    public static function addRemboursement()
    {
        Flight::json(Pret::insertIntoRemboursement(1));
    }

    public static function addPret()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = (object) [
                'montant'        => $_POST['montant'] ?? null,
                'duree'          => $_POST['duree'] ?? null,
                'idtypepret'     => $_POST['idtypepret'] ?? null,
                'idclient'       => $_POST['idclient'] ?? null,
                'delais'         => $_POST['delais'] ?? 0,
                'misyassurance'  => isset($_POST['assurance']) && $_POST['assurance'] == 1 ? 1 : 0
            ];

            try {
                $id = Pret::create($data); // insert du prêt
                Pret::insertPretEnAttente($id); // statut d'attente
                Pret::insertIntoRemboursement($id); // échéancier
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
