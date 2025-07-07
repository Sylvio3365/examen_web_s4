<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <h2>Intérêts gagnés par mois</h2>

        <!-- Formulaire de filtrage -->
        <form method="post" class="mb-4">
            <div class="row">
                <div class="col-md-5">
                    <label for="date_debut">Date début</label>
                    <input type="date" name="date_debut" id="date_debut" value="<?= htmlspecialchars($dateDebut) ?>"
                        class="form-control">
                </div>
                <div class="col-md-5">
                    <label for="date_fin">Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" value="<?= htmlspecialchars($dateFin) ?>"
                        class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                </div>
            </div>
        </form>

        <!-- Tableau des résultats -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mois/Année</th>
                        <th>Nombre de remboursements</th>
                        <th>Total des intérêts</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($interets as $ligne): ?>
                        <tr>
                            <td><?= $ligne['mois'] ?>/<?= $ligne['annee'] ?></td>
                            <td><?= $ligne['nombre_remboursements'] ?></td>
                            <td><?= number_format($ligne['total_interets'], 2) ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Graphique -->
        <div class="mt-5">
            <h3>Visualisation graphique</h3>
            <canvas id="interetsChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Inclusion de Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('interetsChart').getContext('2d');
            const labels = <?= json_encode(array_map(function ($item) {
                return $item['mois'] + '/' + $item['annee'];
            }, $interets)) ?>;
            const data = <?= json_encode(array_map(function ($item) {
                return $item['total_interets'];
            }, $interets)) ?>;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Intérêts gagnés (€)',
                        data: data,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Montant (€)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mois/Année'
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>