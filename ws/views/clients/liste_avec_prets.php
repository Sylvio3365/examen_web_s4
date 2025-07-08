<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des clients avec prêts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .loan-details {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .accordion-button:not(.collapsed) {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Liste des clients avec leurs prêts</h2>
        
        <div class="accordion" id="clientsAccordion">
            <?php foreach ($clients as $client): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?= $client['idclient'] ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#collapse<?= $client['idclient'] ?>" 
                                aria-expanded="false" aria-controls="collapse<?= $client['idclient'] ?>">
                            <?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?>
                            <span class="badge bg-primary ms-2"><?= $client['nombre_prets'] ?> prêt(s)</span>
                            <span class="badge bg-success ms-2"><?= number_format($client['total_montant'], 0, ',', ' ') ?> MGA</span>
                        </button>
                    </h2>
                    <div id="collapse<?= $client['idclient'] ?>" class="accordion-collapse collapse" 
                         aria-labelledby="heading<?= $client['idclient'] ?>" data-bs-parent="#clientsAccordion">
                        <div class="accordion-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Téléphone:</strong> <?= htmlspecialchars($client['telephone'] ?? 'Non renseigné') ?></p>
                                    <p><strong>Email:</strong> <?= htmlspecialchars($client['email'] ?? 'Non renseigné') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Adresse:</strong> <?= htmlspecialchars($client['adresse'] ?? 'Non renseignée') ?></p>
                                    <p><strong>Date naissance:</strong> <?= date('d/m/Y', strtotime($client['dtn'])) ?></p>
                                </div>
                            </div>

                            <?php if (!empty($client['prets'])): ?>
                                <h5 class="mb-3">Détails des prêts</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>N° Prêt</th>
                                                <th>Type</th>
                                                <th>Montant</th>
                                                <th>Durée</th>
                                                <th>Date</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($client['prets'] as $pret): ?>
                                                <tr>
                                                    <td><?= $pret['idpret'] ?></td>
                                                    <td><?= htmlspecialchars($pret['type_pret']) ?></td>
                                                    <td><?= number_format($pret['montant'], 0, ',', ' ') ?> MGA</td>
                                                    <td><?= $pret['duree'] ?> mois</td>
                                                    <td><?= date('d/m/Y', strtotime($pret['date_creation'])) ?></td>
                                                    <td>
                                                        <?php 
                                                        $statutClass = [
                                                            1 => 'bg-warning',
                                                            2 => 'bg-success',
                                                            3 => 'bg-danger'
                                                        ][$pret['statut'] ?? 1];
                                                        ?>
                                                        <span class="badge <?= $statutClass ?>">
                                                            <?= ['En attente', 'Validé', 'Annulé'][$pret['statut'] ?? 0] ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="/pret/generate-pdf/<?= $pret['idpret'] ?>" 
                                                           class="btn btn-sm btn-primary" target="_blank">
                                                           PDF
                                                        </a>
                                                        <a href="/pret/details/<?= $pret['idpret'] ?>" 
                                                           class="btn btn-sm btn-info">
                                                           Détails
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">Ce client n'a aucun prêt enregistré</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                }
            });
        });
    </script>
</body>
</html>