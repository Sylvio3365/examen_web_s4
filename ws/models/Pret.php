<?php
require_once __DIR__ . '/../db.php';
require_once('Client.php');

class Pret
{
    public static function getAll()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insertIntoRemboursement($idpret)
    {
        $pret = Pret::getById($idpret);
        if (!$pret) {
            throw new Exception("Prêt introuvable !");
        }

        $montant = $pret['montant'];
        $duree = $pret['duree'];
        $taux_annuel = $pret['taux_annuel'];
        $taux_mensuel = $taux_annuel / 100 / 12;
        $delais = $pret['delais'] ?? 0;
        $misyassurance = $pret['misyassurance'];

        $assurance_taux = ($misyassurance == 1) ? ($pret['taux_assurance'] ?? 0) : 0;
        $assurance_totale = $montant * $assurance_taux / 100;
        $assurance_mensuelle = $assurance_totale / $duree;

        $capitalRestant = $montant;
        $mensualite_base = Utils::pmt($taux_mensuel, $duree, $montant);

        $mois_actuel = (int) date('n');
        $annee_actuelle = (int) date('Y');
        $mois_actuel = (int) date('n');
        $annee_actuelle = (int) date('Y');

        $tableau = [];

        for ($i = 1; $i <= ($delais + $duree); $i++) {
            $index = $i - 1;
            $mois = ($mois_actuel + $index - 1) % 12 + 1;
            $annee = $annee_actuelle + floor(($mois_actuel + $index - 1) / 12);

            if ($i > $delais) {
                $interet = $capitalRestant * $taux_mensuel;
                $amortissement = $mensualite_base - $interet;
                $echeance = $mensualite_base + $assurance_mensuelle;
                $valeur_nette = $capitalRestant - $amortissement;

                $tableau[] = [
                    'mois' => $mois,
                    'annee' => $annee,
                    'emprunt_restant' => round($capitalRestant, 2),
                    'interet_mensuel' => round($interet, 2),
                    'assurance' => round($assurance_mensuelle, 2),
                    'amortissement' => round($amortissement, 2),
                    'echeance' => round($echeance, 2),
                    'valeur_nette' => round(max($valeur_nette, 0), 2),
                    'idpret' => $idpret
                ];

                $capitalRestant -= $amortissement;
                if ($capitalRestant < 0)
                    $capitalRestant = 0;
                if ($capitalRestant < 0)
                    $capitalRestant = 0;
            }
        }

        foreach ($tableau as $ligne) {
            $id = Remboursement::insert($ligne);
            Remboursement::insertStatut($id, 1);
        }

        return $tableau;
    }

    public static function getById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT p.*, tp.nom AS type_pret, tp.taux_annuel, tp.taux_assurance as taux_assurance, p.misyassurance 
                              FROM pret p 
                              JOIN typepret tp ON p.idtypepret = tp.idtypepret 
                              WHERE p.idpret = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = getDB();
        $stmt = $db->prepare("
        INSERT INTO pret (duree, montant, idtypepret, idclient, delais, misyassurance)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
        $stmt->execute([
            $data->duree,
            $data->montant,
            $data->idtypepret,
            $data->idclient,
            $data->delais,
            $data->misyassurance // ← c’est ça qu’il manquait
        ]);
        return $db->lastInsertId();
    }

    public static function insertStatut($idpret, $idstatut)
    {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO pret_statut (idpret, idstatut, date_modif) VALUES (?, ?, CURDATE())");
        $stmt->execute([$idpret, $idstatut]);
    }

    public static function insertPretEnAttente($idpret)
    {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO pret_statut (idpret, idstatut, date_modif) VALUES (?, ?, CURDATE())");
        $stmt->execute([$idpret, 1]);
    }

    public static function update($id, $data)
    {
        $db = getDB();
        $stmt = $db->prepare("UPDATE pret SET duree = ?, montant = ?, idtypepret = ?, idclient = ?,delais = ? WHERE idpret = ?");
        $stmt->execute([
            $data->duree,
            $data->montant,
            $data->idtypepret,
            $data->idclient,
            $data->delais ?? 0,
            $id
        ]);
    }

    public static function delete($id)
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM pret WHERE idpret = ?");
        $stmt->execute([$id]);
    }

    public static function getAllTypes()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM typepret WHERE deleted_at IS NULL");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function calculerAmortissement($idPret)
    {
        $pret = self::getById($idPret);
        if (!$pret) {
            throw new Exception("Prêt introuvable");
        }

        $tauxMensuel = $pret['taux_annuel'] / 100 / 12;
        $duree = $pret['duree'];
        $montant = $pret['montant'];
        $delais = $pret['delais'] ?? 0;

        // Gestion de l'assurance avec valeur par défaut si misyassurance n'existe pas
        $hasAssurance = isset($pret['misyassurance']) ? ($pret['misyassurance'] == 1) : false;
        $assuranceMensuelle = $hasAssurance ? ($montant * ($pret['taux_assurance'] ?? 0) / 100 / $duree) : 0;

        $mensualite = $montant * $tauxMensuel * pow(1 + $tauxMensuel, $duree) / (pow(1 + $tauxMensuel, $duree) - 1);

        $tableau = [];
        $capitalRestant = $montant;

        // Récupérer la date de création du prêt ou utiliser la date actuelle
        $dateCreation = isset($pret['date_creation']) ? $pret['date_creation'] : date('Y-m-d');
        $timestamp = strtotime($dateCreation);
        $moisDepart = (int) date('n', $timestamp);
        $anneeDepart = (int) date('Y', $timestamp);

        for ($i = 1; $i <= $duree + $delais; $i++) {
            // Calculer le mois et l'année en fonction de la date de création
            $moisCourant = (($moisDepart + $i - 2) % 12) + 1;
            $anneeCourante = $anneeDepart + floor(($moisDepart + $i - 2) / 12);

            if ($i <= $delais) {
                $tableau[] = [
                    'mois' => $moisCourant,
                    'annee' => $anneeCourante,
                    'echeance' => $assuranceMensuelle,
                    'interet' => 0,
                    'amortissement' => 0,
                    'assurance' => $assuranceMensuelle,
                    'capital_restant' => $capitalRestant
                ];
            } else {
                $moisEffectif = $i - $delais;
                $interet = $capitalRestant * $tauxMensuel;
                $amortissement = $mensualite - $interet;
                $capitalRestant -= $amortissement;

                $tableau[] = [
                    'mois' => $moisCourant,
                    'annee' => $anneeCourante,
                    'echeance' => $mensualite + $assuranceMensuelle,
                    'interet' => $interet,
                    'amortissement' => $amortissement,
                    'assurance' => $assuranceMensuelle,
                    'capital_restant' => max($capitalRestant, 0)
                ];
            }
        }

        return $tableau;
    }

    public static function getInteretsParPeriode($dateDebut, $dateFin)
    {
        $db = getDB();
        $sql = "SELECT 
                    r.mois, 
                    r.annee, 
                    SUM(r.interet_mensuel) AS total_interets,
                    COUNT(r.idremboursement) AS nombre_remboursements
                FROM remboursement r
                JOIN pret p ON r.idpret = p.idpret
                JOIN (
                    SELECT ps1.idpret, ps1.idstatut
                    FROM pret_statut ps1
                    INNER JOIN (
                        SELECT idpret, MAX(date_modif) AS max_date
                        FROM pret_statut
                        GROUP BY idpret
                    ) ps2 ON ps1.idpret = ps2.idpret AND ps1.date_modif = ps2.max_date
                ) latest_status ON p.idpret = latest_status.idpret
                WHERE latest_status.idstatut = 2
                AND (r.annee > YEAR(:dateDebut) OR (r.annee = YEAR(:dateDebut) AND r.mois >= MONTH(:dateDebut)))
                AND (r.annee < YEAR(:dateFin) OR (r.annee = YEAR(:dateFin) AND r.mois <= MONTH(:dateFin)))
                GROUP BY r.annee, r.mois
                ORDER BY r.annee, r.mois";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':dateDebut' => $dateDebut,
            ':dateFin' => $dateFin
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllInterets()
    {
        $db = getDB();
        $sql = "SELECT 
                r.mois, 
                r.annee, 
                SUM(r.interet_mensuel) AS total_interets,
                COUNT(r.idremboursement) AS nombre_remboursements
            FROM remboursement r
            JOIN pret p ON r.idpret = p.idpret
            JOIN (
                SELECT ps1.idpret, ps1.idstatut
                FROM pret_statut ps1
                INNER JOIN (
                    SELECT idpret, MAX(date_modif) AS max_date
                    FROM pret_statut
                    GROUP BY idpret
                ) ps2 ON ps1.idpret = ps2.idpret AND ps1.date_modif = ps2.max_date
            ) latest_status ON p.idpret = latest_status.idpret
            WHERE latest_status.idstatut = 2
            GROUP BY r.annee, r.mois
            ORDER BY r.annee, r.mois";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listPendingPret()
    {
        $db = getDB();
        $sql = '
        SELECT 
            c.nom AS nom_client,
            tp.nom AS nom_typepret,
            ps.date_modif,
            s.valeur AS statut_valeur,
            ps.idpret,
            p.montant
        FROM (
            SELECT *,
                   ROW_NUMBER() OVER (PARTITION BY idpret ORDER BY date_modif DESC, idpret_statut DESC) AS rn
            FROM pret_statut
        ) ps
        JOIN pret p ON ps.idpret = p.idpret
        JOIN client c ON p.idclient = c.idclient
        JOIN typepret tp ON p.idtypepret = tp.idtypepret
        JOIN statut s ON ps.idstatut = s.idstatut
        WHERE ps.rn = 1 AND ps.idstatut = 1
    ';

        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllValidatedPrets()
    {
        try {
            $db = getDB();
            
            $sql = "
                SELECT 
                    p.idpret,
                    p.montant,
                    p.duree,
                    p.delais,
                    p.misyassurance,
                    c.nom,
                    c.prenom,
                    tp.nom as type_pret,
                    tp.taux_annuel,
                    tp.taux_assurance,
                    ps.date_modif as date_validation
                FROM pret p
                INNER JOIN client c ON p.idclient = c.idclient
                INNER JOIN typepret tp ON p.idtypepret = tp.idtypepret
                INNER JOIN pret_statut ps ON p.idpret = ps.idpret
                WHERE ps.idstatut = 2
                ORDER BY ps.date_modif DESC
            ";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Erreur dans getAllValidatedPrets: " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des prêts validés");
        }
    }
    public static function comparerPrets($idPret1, $idPret2)
    {
        $pret1 = self::getById($idPret1);
        $pret2 = self::getById($idPret2);
    
        if (!$pret1 || !$pret2) {
            throw new Exception("Un ou plusieurs prêts introuvables");
        }
    
        // Récupérer les informations client
        $client1 = Client::getById($pret1['idclient']);
        $client2 = Client::getById($pret2['idclient']);
    
        // Calculer les métriques pour chaque prêt
        $metriques1 = self::calculerMetriquesPret($pret1);
        $metriques2 = self::calculerMetriquesPret($pret2);
    
        // Déterminer le meilleur prêt
        $meilleurPret = self::determinerMeilleurPret($metriques1, $metriques2);
    
        return [
            'pret1' => array_merge($pret1, $metriques1, [
                'client_nom' => $client1['nom'],
                'client_prenom' => $client1['prenom']
            ]),
            'pret2' => array_merge($pret2, $metriques2, [
                'client_nom' => $client2['nom'],
                'client_prenom' => $client2['prenom']
            ]),
            'meilleur' => $meilleurPret,
            'comparaison' => self::genererComparaison($metriques1, $metriques2)
        ];
    }

    private static function calculerMetriquesPret($pret)
    {
        $montant = $pret['montant'];
        $duree = $pret['duree'];
        $taux_annuel = $pret['taux_annuel'];
        $taux_mensuel = $taux_annuel / 100 / 12;
        $delais = $pret['delais'] ?? 0;

        // Gestion de l'assurance
        $hasAssurance = isset($pret['misyassurance']) ? ($pret['misyassurance'] == 1) : false;
        $taux_assurance = $hasAssurance ? ($pret['taux_assurance'] ?? 0) : 0;

        // Calcul des intérêts totaux
        $mensualite_base = $montant * $taux_mensuel * pow(1 + $taux_mensuel, $duree) / (pow(1 + $taux_mensuel, $duree) - 1);
        $total_interets = ($mensualite_base * $duree) - $montant;

        // Calcul de l'assurance
        $assurance_totale = $montant * $taux_assurance / 100;
        $assurance_mensuelle = $assurance_totale / $duree;

        // Revenus totaux pour la banque
        $revenus_totaux = $total_interets + $assurance_totale;

        // Rentabilité (revenus / montant prêté)
        $rentabilite = ($revenus_totaux / $montant) * 100;

        // Revenus mensuels moyens
        $revenus_mensuels_moyens = $revenus_totaux / ($duree + $delais);

        // Ratio risque/rendement (simple approximation)
        $ratio_risque_rendement = $rentabilite / ($duree / 12); // rentabilité annualisée

        return [
            'mensualite_base' => round($mensualite_base, 2),
            'total_interets' => round($total_interets, 2),
            'assurance_totale' => round($assurance_totale, 2),
            'revenus_totaux' => round($revenus_totaux, 2),
            'rentabilite' => round($rentabilite, 2),
            'revenus_mensuels_moyens' => round($revenus_mensuels_moyens, 2),
            'ratio_risque_rendement' => round($ratio_risque_rendement, 2),
            'duree_totale' => $duree + $delais
        ];
    }

    private static function determinerMeilleurPret($metriques1, $metriques2)
    {
        $score1 = 0;
        $score2 = 0;
        $criteres = [];

        // Critère 1: Revenus totaux (40% du poids)
        if ($metriques1['revenus_totaux'] > $metriques2['revenus_totaux']) {
            $score1 += 40;
            $criteres['revenus_totaux'] = 'pret1';
        } else {
            $score2 += 40;
            $criteres['revenus_totaux'] = 'pret2';
        }

        // Critère 2: Rentabilité (30% du poids)
        if ($metriques1['rentabilite'] > $metriques2['rentabilite']) {
            $score1 += 30;
            $criteres['rentabilite'] = 'pret1';
        } else {
            $score2 += 30;
            $criteres['rentabilite'] = 'pret2';
        }

        // Critère 3: Ratio risque/rendement (20% du poids)
        if ($metriques1['ratio_risque_rendement'] > $metriques2['ratio_risque_rendement']) {
            $score1 += 20;
            $criteres['ratio_risque_rendement'] = 'pret1';
        } else {
            $score2 += 20;
            $criteres['ratio_risque_rendement'] = 'pret2';
        }

        // Critère 4: Durée (10% du poids) - durée plus courte = meilleur
        if ($metriques1['duree_totale'] < $metriques2['duree_totale']) {
            $score1 += 10;
            $criteres['duree'] = 'pret1';
        } else {
            $score2 += 10;
            $criteres['duree'] = 'pret2';
        }

        return [
            'gagnant' => $score1 > $score2 ? 'pret1' : 'pret2',
            'score1' => $score1,
            'score2' => $score2,
            'criteres' => $criteres
        ];
    }
    private static function genererComparaison($metriques1, $metriques2)
    {
        $comparaisons = [];

        // Comparaison des revenus
        $diff_revenus = $metriques1['revenus_totaux'] - $metriques2['revenus_totaux'];
        $comparaisons['revenus'] = [
            'difference' => abs($diff_revenus),
            'pourcentage' => abs($diff_revenus) / min($metriques1['revenus_totaux'], $metriques2['revenus_totaux']) * 100,
            'meilleur' => $diff_revenus > 0 ? 'pret1' : 'pret2'
        ];

        // Comparaison de la rentabilité
        $diff_rentabilite = $metriques1['rentabilite'] - $metriques2['rentabilite'];
        $comparaisons['rentabilite'] = [
            'difference' => abs($diff_rentabilite),
            'meilleur' => $diff_rentabilite > 0 ? 'pret1' : 'pret2'
        ];

        // Comparaison de la durée
        $diff_duree = $metriques1['duree_totale'] - $metriques2['duree_totale'];
        $comparaisons['duree'] = [
            'difference' => abs($diff_duree),
            'meilleur' => $diff_duree < 0 ? 'pret1' : 'pret2' // durée plus courte = meilleur
        ];

        return $comparaisons;
    }
}
