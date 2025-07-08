<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intérêts gagnés</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 10px;
        }

        .header p {
            color: #7f8c8d;
            font-size: 1.1rem;
        }

        .filter-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .filter-section h3 {
            color: #2c3e50;
            font-size: 1.3rem;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .filter-form {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 20px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-control {
            padding: 12px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .results-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .results-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e1e8ed;
        }

        .results-header h3 {
            color: #2c3e50;
            font-size: 1.3rem;
            font-weight: 500;
        }

        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e1e8ed;
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid #f1f3f4;
        }

        .table tr:hover {
            background-color: #f8f9fa;
        }

        .table tfoot th {
            background-color: #3498db;
            color: white;
            font-weight: 600;
        }

        .text-end {
            text-align: right;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
            font-size: 1.1rem;
        }

        .chart-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .chart-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e1e8ed;
        }

        .chart-header h3 {
            color: #2c3e50;
            font-size: 1.3rem;
            font-weight: 500;
        }

        .chart-container {
            padding: 20px;
            height: 400px;
            position: relative;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .filter-form {
                grid-template-columns: 1fr;
            }

            .table {
                font-size: 0.9rem;
            }

            .table th,
            .table td {
                padding: 10px 8px;
            }

            .chart-container {
                height: 300px;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Intérêts gagnés</h1>
            <p>Analyse des intérêts par période</p>
        </div>

        <div class="filter-section">
            <h3>Filtres</h3>
            <div class="filter-form">
                <div class="form-group">
                    <label for="date_debut">Date début</label>
                    <input type="month" id="date_debut" class="form-control">
                </div>
                <div class="form-group">
                    <label for="date_fin">Date fin</label>
                    <input type="month" id="date_fin" class="form-control">
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary" onclick="filtrerInterets()">Filtrer</button>
                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">Réinitialiser</button>
                </div>
            </div>
        </div>

        <div class="results-section">
            <div class="results-header">
                <h3>Résultats</h3>
            </div>
            <div class="table-container">
                <table class="table" id="table-interets">
                    <thead>
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
                        <tr>
                            <th>Total</th>
                            <th class="text-end" id="total-remboursements">0</th>
                            <th class="text-end" id="total-interets">0,00 Ar</th>
                        </tr>
                    </tfoot>
                </table>
                <div id="no-data-message" class="no-data" style="display: none;">
                    <p>Aucun résultat trouvé</p>
                </div>
            </div>
        </div>

        <div class="chart-section">
            <div class="chart-header">
                <h3>Visualisation graphique</h3>
            </div>
            <div class="chart-container">
                <canvas id="interetsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="table-responsive mb-5">
        <table class="table table-striped table-hover" id="table-interets">
            <thead class="table-dark">
                <tr>
                    <th>Mois/Année</th>
                    <th class="text-end">Nombre remboursements</th>
                    <th class="text-end">Total des intérêts</th>
                </tr>
            </thead>
            <tbody></tbody>
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

    <!-- Chart.js (déjà chargé si layout global inclut) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let interetsChart = null;
        let currentData = [];

        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            callback(JSON.parse(xhr.responseText));
                        } catch (e) {
                            alert("Erreur lors de la lecture de la réponse.");
                        }
                    } else {
                        alert("Erreur API: " + xhr.status);
                    }
                }
            };
            xhr.send(data ? JSON.stringify(data) : null);
        }

        function initializeDates() {
            const today = new Date();
            const currentMonth = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');
            const lastYear = (today.getFullYear() - 1) + '-' + String(today.getMonth() + 1).padStart(2, '0');
            document.getElementById('date_fin').value = currentMonth;
            document.getElementById('date_debut').value = lastYear;
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

            if (!dateDebut || !dateFin) return alert('Veuillez sélectionner une date de début et de fin.');
            if (dateDebut > dateFin) return alert('La date de début ne peut pas dépasser la date de fin.');

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

            if (!data.length) {
                tableFooter.style.display = 'none';
                noDataMessage.style.display = 'block';
                return;
            }

            tableFooter.style.display = 'table-footer-group';
            noDataMessage.style.display = 'none';

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
                })} Ar</td>`;
                tbody.appendChild(tr);

                totalRemboursements += parseInt(interet.nombre_remboursements);
                totalInterets += parseFloat(interet.total_interets);
            });

            document.getElementById('total-remboursements').textContent = totalRemboursements;
            document.getElementById('total-interets').textContent = totalInterets.toLocaleString('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' Ar';
        }

        function updateChart(data) {
            const canvas = document.getElementById('interetsChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            if (!ctx) return;

            if (interetsChart) interetsChart.destroy();

            if (!data.length) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.font = '16px Arial';
                ctx.fillStyle = '#666';
                ctx.textAlign = 'center';
                ctx.fillText('Aucune donnée à afficher', canvas.width / 2, canvas.height / 2);
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
                                label: ctx => ctx.parsed.y.toLocaleString('fr-FR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }) + ' Ar'
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
                                callback: val => val.toLocaleString('fr-FR')
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

        // Initialisation sécurisée après insertion HTML
        setTimeout(() => {
            initializeDates();
            chargerInterets();
            document.getElementById('btn-filtrer').addEventListener('click', filtrerInterets);
            document.getElementById('btn-reset').addEventListener('click', resetFilters);
        }, 100);
    </script>