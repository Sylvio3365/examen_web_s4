<div class="container mt-5">
    <h2 class="mb-4">Montant disponible par période</h2>

    <!-- Formulaire -->
    <div class="card p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="mois_debut" class="form-label">Mois début</label>
                <select id="mois_debut" class="form-select">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>"><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="annee_debut" class="form-label">Année début</label>
                <input type="number" id="annee_debut" class="form-control" value="<?= date('Y') - 1 ?>">
            </div>
            <div class="col-md-3">
                <label for="mois_fin" class="form-label">Mois fin</label>
                <select id="mois_fin" class="form-select">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="annee_fin" class="form-label">Année fin</label>
                <input type="number" id="annee_fin" class="form-control" value="<?= date('Y') ?>">
            </div>
        </div>
        <div class="mt-3 text-end">
            <button id="btn-filtrer" class="btn btn-primary">Filtrer</button>
        </div>
    </div>

    <!-- Résultat en tableau -->
    <div class="card p-4 mb-4">
        <h5>Résultats</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="resultats-table">
                <thead>
                    <tr>
                        <th>Année</th>
                        <th>Mois</th>
                        <th>Montant non emprunté (Ar)</th>
                        <th>Remboursement des clients (Ar)</th>
                        <th><strong>Disponible</strong> (Ar)</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <tr>
                        <td colspan="5" class="text-center">Aucun résultat</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Graphique -->
    <div class="card p-4">
        <h5>Graphique : Montant disponible</h5>
        <canvas id="chartDisponible" height="120"></canvas>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let chartDisponible = null;

    document.getElementById("btn-filtrer").addEventListener("click", function() {
        const moisDebut = document.getElementById("mois_debut").value;
        const anneeDebut = document.getElementById("annee_debut").value;
        const moisFin = document.getElementById("mois_fin").value;
        const anneeFin = document.getElementById("annee_fin").value;

        const baseUrl = "<?= Flight::get('base_url') ?>";
        const url = `${baseUrl}/montant?mois_debut=${moisDebut}&annee_debut=${anneeDebut}&mois_fin=${moisFin}&annee_fin=${anneeFin}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById("table-body");
                tbody.innerHTML = "";

                if (!data || data.length === 0) {
                    tbody.innerHTML = "<tr><td colspan='5' class='text-center'>Aucun résultat</td></tr>";
                    return;
                }

                const labels = [];
                const disponibleData = [];

                data.forEach(row => {
                    const disponible = row.entrant - row.sortant;
                    labels.push(`${row.mois} ${row.annee}`);
                    disponibleData.push(disponible);

                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${row.annee}</td>
                        <td>${row.mois}</td>
                        <td>${row.entrant.toLocaleString('fr-FR', { minimumFractionDigits: 2 })}</td>
                        <td>${row.sortant.toLocaleString('fr-FR', { minimumFractionDigits: 2 })}</td>
                        <td>${disponible.toLocaleString('fr-FR', { minimumFractionDigits: 2 })}</td>
                    `;
                    tbody.appendChild(tr);
                });

                // Mise à jour du graphique
                if (chartDisponible) {
                    chartDisponible.destroy();
                }

                const ctx = document.getElementById('chartDisponible').getContext('2d');
                chartDisponible = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Montant disponible (Ar)',
                            data: disponibleData,
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Montant (Ar)'
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                alert("Erreur lors de la récupération des données.");
                console.error(error);
            });
    });
</script>