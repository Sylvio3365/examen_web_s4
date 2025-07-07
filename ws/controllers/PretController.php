<?php
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../helpers/Utils.php';
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../helpers/PdfHelper.php';

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
        // Récupérer les données du prêt
        $pret = Pret::getById($idPret);
        $client = Client::getById($pret['idclient']);
        $typePret = TypePret::getById($pret['idtypepret']);

        // Calculer l'amortissement
        $amortissement = Pret::calculerAmortissement($idPret);

        // Créer le PDF
        $pdf = new PdfHelper('Contrat de Prêt', __DIR__ . '/../public/images/logo.png');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        // Informations de l'établissement
        $pdf->SectionTitle('Informations de l\'établissement financier');
        $pdf->InfoLine('Nom:', 'Banque SMD');
        $pdf->InfoLine('Adresse:', '123 Avenue des Prêts, Antananarivo');
        $pdf->InfoLine('Contact:', 'contact@banquesmd.mg | +261 34 00 000 00');
        $pdf->Ln(10);

        // Informations du client
        $pdf->SectionTitle('Informations du client');
        $pdf->InfoLine('Nom complet:', $client['prenom'] . ' ' . $client['nom']);
        $pdf->InfoLine('Date de naissance:', $client['dtn']);
        $pdf->InfoLine('ID Client:', $pret['idclient']);
        $pdf->Ln(10);

        // Détails du prêt
        $pdf->SectionTitle('Détails du prêt');
        $pdf->InfoLine('Numéro de prêt:', $pret['idpret']);
        $pdf->InfoLine('Date de création:', date('d/m/Y'));
        $pdf->InfoLine('Type de prêt:', $typePret['nom']);
        $pdf->InfoLine('Montant emprunté:', number_format($pret['montant'], 2, ',', ' ') . ' MGA');
        $pdf->InfoLine('Taux d\'intérêt annuel:', $typePret['taux_annuel'] . '%');
        $pdf->InfoLine('Durée du prêt:', $pret['duree'] . ' mois');
        $pdf->InfoLine('Délai de remboursement:', $pret['delais'] . ' mois');
        $pdf->Ln(10);

        // Modalités financières
        $pdf->SectionTitle('Modalités financières');
        $totalInterets = array_sum(array_column($amortissement, 'interet'));
        $pdf->InfoLine('Mensualité fixe:', number_format($amortissement[$pret['delais']]['echeance'], 2, ',', ' ') . ' MGA');
        $pdf->InfoLine('Total des intérêts:', number_format($totalInterets, 2, ',', ' ') . ' MGA');
        $pdf->InfoLine('Montant total à rembourser:', number_format($pret['montant'] + $totalInterets, 2, ',', ' ') . ' MGA');
        $pdf->Ln(10);

        // Période
        $pdf->SectionTitle('Période');
        $pdf->InfoLine('Date de début:', date('d/m/Y'));
        $pdf->InfoLine('Date de fin prévue:', date('d/m/Y', strtotime('+' . ($pret['duree'] + $pret['delais']) . ' months')));
        $pdf->Ln(10);

        // Tableau d'amortissement (3 premières mensualités après délai)
        $pdf->SectionTitle('Échéancier (extrait)');
        $headers = ['Mois', 'Année', 'Échéance', 'Intérêt', 'Capital', 'Reste'];
        $data = [];

        for ($i = $pret['delais']; $i < min($pret['delais'] + 3, count($amortissement)); $i++) {
            $data[] = [
                $amortissement[$i]['mois'],
                $amortissement[$i]['annee'],
                number_format($amortissement[$i]['echeance'], 2, ',', ' '),
                number_format($amortissement[$i]['interet'], 2, ',', ' '),
                number_format($amortissement[$i]['amortissement'], 2, ',', ' '),
                number_format($amortissement[$i]['capital_restant'], 2, ',', ' ')
            ];
        }

        $pdf->SimpleTable($headers, $data);
        $pdf->Ln(10);

        // Mentions légales
        $pdf->SectionTitle('Mentions légales');
        $pdf->MultiCell(0, 6, 'Ce document constitue une confirmation de votre prêt auprès de notre établissement. Les informations contenues dans ce document sont valables sous réserve d\'acceptation définitive par nos services. En cas de retard de paiement, des pénalités de 2% du montant dû seront appliquées.');
        $pdf->Ln(15);

        // Signature
        $pdf->Cell(0, 6, 'Fait à Antananarivo, le ' . date('d/m/Y'), 0, 1, 'R');
        $pdf->Cell(0, 20, 'Pour la Banque SMD', 0, 1, 'R');
        $pdf->Cell(0, 6, '_________________________', 0, 1, 'R');

        // Output
        $pdf->Output('I', 'pret_' . $pret['idpret'] . '.pdf');
    }
}