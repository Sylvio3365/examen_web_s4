<?php
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../helpers/Utils.php';
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../models/Sortant.php';
require_once __DIR__ . '/../models/PretStatut.php';

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

    public static function pendingPret()
    {
        header('Content-Type: application/json'); // ✅ Spécifie que c'est du JSON
        try {
            $prets = Pret::listPendingPret();
            echo json_encode($prets);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public static function goPendingPage()
    {
        Flight::render('pret/attentePret');    
    }

    public static function validerPret() {
        try {
            $rawData = file_get_contents("php://input");
            $data = json_decode($rawData, true);
            
            if (!isset($data['idpret']) || !isset($data['montant'])) {
                Flight::json(['error' => 'Données manquantes (idpret et montant requis)'], 400);
                return;
            }
            
            $db = getDB();
            $db->beginTransaction();
            
            try {
                // Insérer dans sortant
                $sortantData = (object) [
                    'date_' => date('Y-m-d'),
                    'montant' => $data['montant'],
                    'idmotif' => 1,
                    'idpret' => $data['idpret']
                ];
                $sortantId = Sortant::insertSortant($sortantData);
                
                // Préparer les données pour pret_statut
                $pretStatutData = (object) [
                    'idpret' => $data['idpret'],
                    'idstatut' => 2,
                    'date_modif' => date('Y-m-d')
                ];
                
                // Appeler directement le modèle
                $pretStatutId = PretStatut::insertPretStatut($pretStatutData);
                
                $db->commit();
                Flight::json(['message' => 'Prêt validé avec succès', 'sortant_id' => $sortantId]);
                
            } catch (Exception $e) {
                $db->rollBack();
                Flight::json(['error' => 'Erreur transaction : ' . $e->getMessage()], 500);
            }
            
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la validation: ' . $e->getMessage()], 500);
        }
    }
    
    
    // Méthode pour annuler un prêt
    public static function annulerPret() {
        try {
            $data = Flight::request()->data;
            
            // Validation des données
            if (!isset($data->idpret)) {
                Flight::json(['error' => 'ID prêt manquant'], 400);
                return;
            }
            
            // Insérer dans pret_statut avec idstatut = 3 (annulé)
            $pretStatutData = (object) [
                'idpret' => $data->idpret,
                'idstatut' => 3,
                'date_modif' => date('Y-m-d')
            ];
            
            PretStatut::insertPretStatut($pretStatutData);
            Flight::json(['message' => 'Prêt annulé avec succès']);
            
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de l\'annulation: ' . $e->getMessage()], 500);
        }
    }


}
