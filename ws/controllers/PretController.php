<?php
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../helpers/Utils.php';
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../models/Remboursement.php';
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../helpers/PdfHelper.php';
require_once __DIR__ . '/../models/Client.php';

class PretController
{
    public static function goIndex()
    {
        $page = 'pret/index';
        Flight::render('template/index', ['page' => $page]);
    }

    public static function goInteret()
    {
        $page = 'pret/interets';
        Flight::render('template/index', ['page' => $page]);
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
                $id = Pret::create($data); // insert du pret
                Pret::insertPretEnAttente($id); // statut d'attente
                Pret::insertIntoRemboursement($id); // echeancier
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
            Flight::json(['status' => 'error', 'message' => 'Methode invalide'], 405);
        }
    }
    public static function generatePdf($idPret)
    {
        try {
            // Recuperer les donnees du pret
            $pret = Pret::getById($idPret);
            if (!$pret) {
                throw new Exception("Pret introuvable");
            }

            // Verifier et initialiser misyassurance si non defini
            if (!isset($pret['misyassurance'])) {
                $pret['misyassurance'] = 0;
            }

            $client = Client::getById($pret['idclient']);
            if (!$client) {
                throw new Exception("Client introuvable");
            }

            // Recuperer le type de pret
            $typePret = [
                'nom' => $pret['type_pret'],
                'taux_annuel' => $pret['taux_annuel'],
                'taux_assurance' => $pret['taux_assurance'] ?? 0
            ];

            // Calculer l'amortissement
            $amortissement = Pret::calculerAmortissement($idPret);

            // Recuperer les remboursements depuis la base
            $remboursements = Remboursement::getByPretId($idPret);

            // Creer le PDF
            $pdf = new PdfHelper(utf8_decode(('Pret N°') . $pret['idpret']), __DIR__ . '/../public/images/logo.png');
            $pdf->AliasNbPages();
            $pdf->AddPage();


            // 1. Informations de l'etablissement
            $pdf->SectionTitle(utf8_decode('1. Informations de l\'etablissement financier'));
            $pdf->InfoLine('Nom:', 'Banque SMD');
            $pdf->InfoLine('Adresse:', utf8_decode('123 Mahalavolona, Andoharanofotsy'));
            $pdf->InfoLine('Telephone:', '+261 34 00 000 00');
            $pdf->InfoLine('Email:', 'contact@banquesmd.mg');
            $pdf->InfoLine('NIF:', '1234567890');
            $pdf->InfoLine('STAT:', '987654321');
            $pdf->Ln(10);

            // 2. Informations du client
            $pdf->SectionTitle(utf8_decode('2. Informations du client'));
            $pdf->InfoLine('Nom complet:', utf8_decode($client['prenom'] . ' ' . $client['nom']));
            $pdf->InfoLine('Date de naissance:', date('d/m/Y', strtotime($client['dtn'])));
            $pdf->InfoLine('Adresse:', utf8_decode($client['adresse'] ?? 'Non renseignee'));
            $pdf->InfoLine('Telephone:', $client['telephone'] ?? 'Non renseigne');
            $pdf->InfoLine('Email:', $client['email'] ?? 'Non renseigne');
            $pdf->InfoLine('N° Client:', $client['idclient']);
            $pdf->Ln(10);

            // 3. Details du pret
            $pdf->SectionTitle(utf8_decode('3. Details du pret'));
            $pdf->InfoLine('Num Pret:', $pret['idpret']);
            $pdf->InfoLine('Date de creation:', date('d/m/Y', strtotime($pret['date_creation'] ?? 'now')));
            $pdf->InfoLine('Type de pret:', utf8_decode($typePret['nom']));
            $pdf->InfoLine('Montant emprunte:', number_format($pret['montant'], 0, ',', ' ') . ' MGA');
            $pdf->InfoLine('Taux d\'interet annuel:', $typePret['taux_annuel'] . '%');
            $pdf->InfoLine('Taux d\'assurance:', $typePret['taux_assurance'] . '%');
            $pdf->InfoLine('Duree du pret:', $pret['duree'] . ' mois');
            $pdf->InfoLine('Delai de grace:', $pret['delais'] . ' mois');
            $pdf->InfoLine('Assurance incluse:', $pret['misyassurance'] ? 'Oui' : 'Non');
            $pdf->Ln(10);

            // 4. Modalites financières
            $pdf->SectionTitle(utf8_decode('4. Modalites financières'));

            // Calcul des totaux
            $totalInterets = array_sum(array_column($amortissement, 'interet'));
            $totalAssurance = array_sum(array_column($amortissement, 'assurance'));
            $totalRemboursement = $pret['montant'] + $totalInterets + $totalAssurance;

            // Trouver la première mensualite après delai
            $mensualite = 0;
            foreach ($amortissement as $ligne) {
                if ($ligne['echeance'] > 0 && $ligne['echeance'] != $ligne['assurance']) {
                    $mensualite = $ligne['echeance'];
                    break;
                }
            }

            $pdf->InfoLine('Mensualite (hors delai):', number_format($mensualite, 0, ',', ' ') . ' MGA');
            $pdf->InfoLine('Total des interets:', number_format($totalInterets, 0, ',', ' ') . ' MGA');
            $pdf->InfoLine('Total de l\'assurance:', number_format($totalAssurance, 0, ',', ' ') . ' MGA');
            $pdf->InfoLine('Montant total a rembourser:', number_format($totalRemboursement, 0, ',', ' ') . ' MGA');
            $pdf->Ln(10);

            // 5. Periode
            $pdf->SectionTitle(utf8_decode('5. Periode de remboursement'));
            $dateDebut = date('d/m/Y', strtotime($pret['date_creation'] ?? 'now'));
            $dateFin = date('d/m/Y', strtotime(($pret['date_creation'] ?? 'now') . ' +' . ($pret['duree'] + $pret['delais']) . ' months'));
            $pdf->InfoLine('Date de debut:', $dateDebut);
            $pdf->InfoLine('Date de fin prevue:', $dateFin);
            $pdf->Ln(10);

            // 6. Tableau d'amortissement
            $pdf->SectionTitle(utf8_decode('6. Tableau d\'amortissement'));
            $pdf->SetFont('Arial', '', 8);

            // En-tetes du tableau
            $headers = [
                'Mois',
                'Annee',
                'Mensualite',
                'Interet',
                'Capital',
                'Assurance',
                'Reste du'
            ];

            // Preparation des donnees
            $data = [];
            foreach ($amortissement as $ligne) {
                $data[] = [
                    $ligne['mois'],
                    $ligne['annee'],
                    number_format($ligne['echeance'], 0, ',', ' '),
                    number_format($ligne['interet'], 0, ',', ' '),
                    number_format($ligne['amortissement'], 0, ',', ' '),
                    number_format($ligne['assurance'], 0, ',', ' '),
                    number_format($ligne['capital_restant'], 0, ',', ' ')
                ];
            }

            // Affichage du tableau
            $pdf->SetWidths([10, 10, 25, 25, 25, 25, 30]); // Largeurs des colonnes
            $pdf->SetAligns(['C', 'C', 'R', 'R', 'R', 'R', 'R']); // Alignements
            $pdf->ImprovedTable($headers, $data);
            $pdf->Ln(10);


            // Signature
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->Cell(0, 6, utf8_decode('Fait a Antananarivo, le ') . date('d/m/Y'), 0, 1, 'R');
            $pdf->Cell(0, 20, 'Pour la Banque SMD', 0, 1, 'R');
            $pdf->Cell(0, 6, '_________________________', 0, 1, 'R');
            $pdf->Cell(0, 6, utf8_decode('Le Responsable Credit'), 0, 1, 'R');
            $pdf->Ln(20);
            $pdf->Cell(0, 6, 'Le Client', 0, 1, 'R');
            $pdf->Cell(0, 6, '_________________________', 0, 1, 'R');
            $pdf->Cell(0, 6, utf8_decode($client['prenom'] . ' ' . $client['nom']), 0, 1, 'R');

            // Generation du PDF
            $pdf->Output('I', 'pret_' . $pret['idpret'] . '.pdf');
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors de la generation du PDF: ' . $e->getMessage());
        }
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
        $page = 'pret/attentePret';
        Flight::render('template/index', ['page' => $page]);
    }

    public static function validerPret()
    {
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
    public static function annulerPret()
    {
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
