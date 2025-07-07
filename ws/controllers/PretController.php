<?php
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../helpers/Utils.php';
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../helpers/PdfHelper.php';
require_once __DIR__ . '/../models/Client.php';

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
    public static function generatePdf($idPret)
    {
        try {
            // Récupérer les données du prêt
            $pret = Pret::getById($idPret);
            if (!$pret) {
                Flight::halt(404, 'Prêt non trouvé');
                return;
            }

            $client = Client::getById($pret['idclient']);
            if (!$client) {
                Flight::halt(404, 'Client non trouvé');
                return;
            }

            // Récupérer les informations du type de prêt depuis la requête JOIN dans Pret::getById()
            // Les données sont déjà disponibles dans $pret
            $typePret = [
                'nom' => $pret['type_pret'],
                'taux_annuel' => $pret['taux_annuel']
            ];

            // Calculer l'amortissement
            $amortissement = Pret::calculerAmortissement($idPret);

            // Créer le PDF
            $pdf = new PdfHelper(utf8_decode('Contrat de Prêt'), __DIR__ . '/../public/images/logo.png');
            $pdf->AliasNbPages();
            $pdf->AddPage();

            // Informations de l'établissement
            $pdf->SectionTitle(utf8_decode('Informations de l\'établissement financier'));
            $pdf->InfoLine('Nom:', 'Banque SMD');
            $pdf->InfoLine('Adresse:', utf8_decode('123 Avenue des Prêts, Antananarivo'));
            $pdf->InfoLine('Contact:', 'contact@banquesmd.mg | +261 34 00 000 00');
            $pdf->Ln(10);

            // Informations du client
            $pdf->SectionTitle('Informations du client');
            $pdf->InfoLine('Nom complet:', utf8_decode($client['prenom'] . ' ' . $client['nom']));
            $pdf->InfoLine('Date de naissance:', $client['dtn']);
            $pdf->InfoLine('ID Client:', $pret['idclient']);
            $pdf->Ln(10);

            // Détails du prêt
            $pdf->SectionTitle(utf8_decode('Détails du prêt'));
            $pdf->InfoLine(utf8_decode('Numéro de prêt:'), $pret['idpret']);
            $pdf->InfoLine(utf8_decode('Date de création:'), date('d/m/Y'));
            $pdf->InfoLine(utf8_decode('Type de prêt:'), utf8_decode($typePret['nom']));
            $pdf->InfoLine(utf8_decode('Montant emprunté:'), number_format($pret['montant'], 2, ',', ' ') . ' MGA');
            $pdf->InfoLine(utf8_decode('Taux d\'intérêt annuel:'), $typePret['taux_annuel'] . '%');
            $pdf->InfoLine(utf8_decode('Durée du prêt:'), $pret['duree'] . ' mois');
            $pdf->InfoLine(utf8_decode('Délai de remboursement:'), $pret['delais'] . ' mois');
            $pdf->Ln(10);

            // Modalités financières
            $pdf->SectionTitle(utf8_decode('Modalités financières'));
            $totalInterets = array_sum(array_column($amortissement, 'interet'));
            
            // Trouver la première mensualité non-nulle (après le délai)
            $mensualite = 0;
            foreach ($amortissement as $ligne) {
                if ($ligne['echeance'] > 0) {
                    $mensualite = $ligne['echeance'];
                    break;
                }
            }
            
            $pdf->InfoLine(utf8_decode('Mensualité fixe:'), number_format($mensualite, 2, ',', ' ') . ' MGA');
            $pdf->InfoLine(utf8_decode('Total des intérêts:'), number_format($totalInterets, 2, ',', ' ') . ' MGA');
            $pdf->InfoLine(utf8_decode('Montant total à rembourser:'), number_format($pret['montant'] + $totalInterets, 2, ',', ' ') . ' MGA');
            $pdf->Ln(10);

            // Période
            $pdf->SectionTitle(utf8_decode('Période'));
            $pdf->InfoLine(utf8_decode('Date de début:'), date('d/m/Y'));
            $pdf->InfoLine(utf8_decode('Date de fin prévue:'), date('d/m/Y', strtotime('+' . ($pret['duree'] + $pret['delais']) . ' months')));
            $pdf->Ln(10);

            // Tableau d'amortissement (3 premières mensualités après délai)
            $pdf->SectionTitle(utf8_decode('Échéancier (extrait)'));
            $headers = ['Mois', utf8_decode('Année'), utf8_decode('Échéance'), utf8_decode('Intérêt'), 'Capital', 'Reste'];
            $data = [];

            $compteur = 0;
            foreach ($amortissement as $i => $ligne) {
                if ($ligne['echeance'] > 0 && $compteur < 3) {
                    $data[] = [
                        $ligne['mois'],
                        $ligne['annee'],
                        number_format($ligne['echeance'], 2, ',', ' '),
                        number_format($ligne['interet'], 2, ',', ' '),
                        number_format($ligne['amortissement'], 2, ',', ' '),
                        number_format($ligne['capital_restant'], 2, ',', ' ')
                    ];
                    $compteur++;
                }
            }

            $pdf->SimpleTable($headers, $data);
            $pdf->Ln(10);

            // Mentions légales
            $pdf->SectionTitle(utf8_decode('Mentions légales'));
            $pdf->MultiCell(0, 6, utf8_decode('Ce document constitue une confirmation de votre prêt auprès de notre établissement. Les informations contenues dans ce document sont valables sous réserve d\'acceptation définitive par nos services. En cas de retard de paiement, des pénalités de 2% du montant dû seront appliquées.'));
            $pdf->Ln(15);

            // Signature
            $pdf->Cell(0, 6, utf8_decode('Fait à Antananarivo, le ') . date('d/m/Y'), 0, 1, 'R');
            $pdf->Cell(0, 20, 'Pour la Banque SMD', 0, 1, 'R');
            $pdf->Cell(0, 6, '_________________________', 0, 1, 'R');

            // Output
            $pdf->Output('I', 'pret_' . $pret['idpret'] . '.pdf');
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }
}