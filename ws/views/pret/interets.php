<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intérêts gagnés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 1000px;
            margin-top: 30px;
        }
        .chart-container {
            height: 400px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Intérêts gagnés par mois</h2>

        <!-- Formulaire de filtrage -->
        <div class="mb-4 card p-3">
            <div class="row g-3">
                <div class="col-md-5">
                    <label for="date_debut" class="form-label">Date début</label>
                    <input type="month" id="date_debut" class="form-control">
                </div>
                <div class="col-md-5">
                    <label for="date_fin" class="form-label">Date fin</label>
                    <input type="month" id="date_fin" class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100" onclick="filtrerInterets()">Filtrer</button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">Réinitialiser</button>
                </div>
            </div>
        </div>

        <!-- Tableau des résultats -->
        <div class="table-responsive mb-5">
            <table class="table table-striped table-hover" id="table-interets">
                <thead class="table-dark">
                    <tr>
                        <th>Mois/Année</th>
                        <th class="text-end">Nombre remboursements</th>
                        <th class="text-end">Total des intérêts</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Les données seront ajoutées ici par JavaScript -->
                </tbody>
                <tfoot id="table-footer" style="display: none;">
                    <tr class="table-active">
                        <th>Total</th>
                        <th class="text-end" id="total-remboursements">0</th>
                        <th class="text-end" id="total-interets">0,00 Ar</th>
                    </tr>
                </tfoot>
            </table>
            <div id="no-data-message" class="text-center" style="display: none;">
                <p class="text-muted">Aucun résultat trouvé</p>
            </div>
        </div>

        <!-- Graphique -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Visualisation graphique</h3>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="interetsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Inclusion des librairies JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const apiBase = "http://localhost/examen_web_s4/ws";
        let interetsChart = null;
        let currentData = [];

        const moisNoms = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];

        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    callback(JSON.parse(xhr.responseText));
                }
            };
            xhr.send(data ? JSON.stringify(data) : null);
        }

        function chargerInterets() {
            ajax("POST", "/api/interets", {}, (response) => {
                currentData = response.interets || [];
                afficherDonnees(currentData);
                updateChart(currentData);
            });
        }

        function filtrerInterets() {
            const dateDebut = document.getElementById('date_debut').value;
            const dateFin = document.getElementById('date_fin').value;

            if (!dateDebut || !dateFin) {
                alert('Veuillez sélectionner une date de début et une date de fin.');
                return;
            }

            if (dateDebut > dateFin) {
                alert('La date de début ne peut pas être supérieure à la date de fin.');
                return;
            }

            const data = {
                date_debut: dateDebut + '-01',
                date_fin: dateFin + '-31'
            };

            ajax("POST", "/api/interets", data, (response) => {
                currentData = response.interets || [];
                afficherDonnees(currentData);
                updateChart(currentData);
            });
        }

        function resetFilters() {
            document.getElementById('date_debut').value = '';
            document.getElementById('date_fin').value = '';
            chargerInterets();
        }

        function afficherDonnees(data) {
            const tbody = document.querySelector("#table-interets tbody");
            const tableFooter = document.getElementById('table-footer');
            const noDataMessage = document.getElementById('no-data-message');
            
            tbody.innerHTML = "";
            
            if (data.length === 0) {
                noDataMessage.style.display = 'block';
                tableFooter.style.display = 'none';
                return;
            }

            noDataMessage.style.display = 'none';
            tableFooter.style.display = 'table-footer-group';

            let totalRemboursements = 0;
            let totalInterets = 0;

            data.forEach(interet => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${String(interet.mois).padStart(2, '0')}/${interet.annee}</td>
                    <td class="text-end">${interet.nombre_remboursements}</td>
                    <td class="text-end">${parseFloat(interet.total_interets).toLocaleString('fr-FR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })} Ar</td>
                `;
                tbody.appendChild(tr);

                totalRemboursements += parseInt(interet.nombre_remboursements);
                totalInterets += parseFloat(interet.total_interets);
            });

            // Mettre à jour le footer avec les totaux
            document.getElementById('total-remboursements').textContent = totalRemboursements;
            document.getElementById('total-interets').textContent = totalInterets.toLocaleString('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' Ar';
        }

        function updateChart(data) {
            const ctx = document.getElementById('interetsChart').getContext('2d');
            
            // Détruire le graphique existant s'il y en a un
            if (interetsChart) {
                interetsChart.destroy();
            }

            if (data.length === 0) {
                // Afficher un message si pas de données
                ctx.font = '16px Arial';
                ctx.fillStyle = '#666';
                ctx.textAlign = 'center';
                ctx.fillText('Aucune donnée à afficher', ctx.canvas.width / 2, ctx.canvas.height / 2);
                return;
            }

            const labels = data.map(item => String(item.mois).padStart(2, '0') + '/' + item.annee);
            const interetsData = data.map(item => parseFloat(item.total_interets));

            interetsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Intérêts gagnés (Ar)',
                        data: interetsData,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y.toLocaleString('fr-FR', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }) + ' Ar';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Montant (Ar)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('fr-FR');
                                }
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
        }

        // Initialiser les dates par défaut (dernier mois et mois actuel)
        function initializeDates() {
            const today = new Date();
            const currentMonth = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');
            const lastYear = (today.getFullYear() - 1) + '-' + String(today.getMonth() + 1).padStart(2, '0');
            
            document.getElementById('date_fin').value = currentMonth;
            document.getElementById('date_debut').value = lastYear;
        }

        // Charger les données au démarrage
        document.addEventListener('DOMContentLoaded', function() {
            initializeDates();
            chargerInterets();
        });
    </script>
</body>
</html>