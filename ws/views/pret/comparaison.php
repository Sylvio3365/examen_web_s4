<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparaison des Prêts</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-primary:disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
        }

        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .section h2 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .checkbox-cell {
            width: 40px;
            text-align: center;
        }

        .checkbox-cell input[type="checkbox"] {
            transform: scale(1.2);
        }

        .amount {
            text-align: right;
            font-weight: 500;
        }

        .comparison-result {
            display: none;
        }

        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .pret-card {
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            padding: 20px;
            background: #f8f9fa;
        }

        .pret-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .pret-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .pret-info .label {
            font-weight: 500;
            color: #555;
        }

        .pret-info .value {
            color: #2c3e50;
        }

        .winner {
            background: #d5f4e6;
            border-color: #27ae60;
        }

        .winner h3 {
            color: #27ae60;
        }

        .metrics-table {
            margin-top: 20px;
        }

        .metrics-table th {
            background-color: #34495e;
            color: white;
        }

        .better {
            background-color: #d5f4e6;
            font-weight: 600;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .selected-row {
            background-color: #e3f2fd !important;
            border-left: 4px solid #2196f3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Comparaison des Prêts</h1>
            <p>Sélectionnez exactement 2 prêts pour les comparer</p>
        </div>

        <div id="message"></div>

        <div class="actions">
            <button class="btn btn-primary" id="compareBtn" disabled>
                Comparer les prêts sélectionnés
            </button>
            <button class="btn btn-secondary" id="resetBtn">
                Réinitialiser
            </button>
            <button class="btn btn-secondary" id="exportBtn" style="display: none;">
                Exporter en PDF
            </button>
        </div>

        <div class="section">
            <h2>Liste des prêts validés</h2>
            <div class="table-container">
                <table id="pretsTable">
                    <thead>
                        <tr>
                            <th class="checkbox-cell">Sélection</th>
                            <th>N° Prêt</th>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Durée</th>
                            <th>Taux</th>
                            <th>Date validation</th>
                            <th>Assurance</th>
                        </tr>
                    </thead>
                    <tbody id="pretsTableBody">
                        <tr>
                            <td colspan="9" class="loading">Chargement des prêts...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section comparison-result" id="comparisonResult">
            <h2>Résultat de la comparaison</h2>
            <div id="comparisonContent"></div>
        </div>
    </div>

    <script>
        class PretComparator {
            constructor() {
                this.selectedPrets = [];
                this.allPrets = [];
                this.lastComparison = null;
                this.init();
            }

            init() {
                this.loadPrets();
                this.bindEvents();
            }

            bindEvents() {
                document.getElementById('compareBtn').addEventListener('click', () => this.comparePrets());
                document.getElementById('resetBtn').addEventListener('click', () => this.reset());
                document.getElementById('exportBtn').addEventListener('click', () => this.exportPdf());
            }

            async loadPrets() {
                try {
                    // Utiliser l'URL correcte basée sur votre structure
                    const response = await fetch('./api/prets/valides', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`Erreur HTTP: ${response.status} - ${response.statusText}`);
                    }

                    const responseText = await response.text();
                    console.log('Réponse brute:', responseText);

                    let prets;
                    try {
                        prets = JSON.parse(responseText);
                    } catch (parseError) {
                        throw new Error('Erreur de parsing JSON: ' + parseError.message);
                    }

                    // Vérifier si la réponse contient une erreur
                    if (prets.error) {
                        throw new Error(prets.error);
                    }

                    // Si prets est un objet avec une propriété data, l'utiliser
                    if (prets.data && Array.isArray(prets.data)) {
                        this.allPrets = prets.data;
                    } else if (Array.isArray(prets)) {
                        this.allPrets = prets;
                    } else {
                        throw new Error('Format de données inattendu');
                    }

                    console.log('Prêts chargés:', this.allPrets);
                    this.renderPretsTable();
                } catch (error) {
                    console.error('Erreur détaillée:', error);
                    this.showMessage('Erreur lors du chargement des prêts: ' + error.message, 'error');
                    
                    // Afficher un message d'erreur plus détaillé dans le tableau
                    const tbody = document.getElementById('pretsTableBody');
                    tbody.innerHTML = `<tr><td colspan="9" class="error">Erreur: ${error.message}</td></tr>`;
                }
            }

            renderPretsTable() {
                const tbody = document.getElementById('pretsTableBody');

                if (!this.allPrets || this.allPrets.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="9" class="no-data">Aucun prêt validé trouvé</td></tr>';
                    return;
                }

                tbody.innerHTML = this.allPrets.map(pret => `
                    <tr data-pret-id="${pret.idpret}">
                        <td class="checkbox-cell">
                            <input type="checkbox" value="${pret.idpret}" onchange="comparator.handlePretSelection(this)">
                        </td>
                        <td>${pret.idpret}</td>
                        <td>${pret.client_prenom} ${pret.client_nom}</td>
                        <td>${pret.type_pret}</td>
                        <td class="amount">${new Intl.NumberFormat('fr-FR').format(pret.montant)} MGA</td>
                        <td>${pret.duree} mois</td>
                        <td>${pret.taux_annuel}%</td>
                        <td>${new Date(pret.date_validation).toLocaleDateString('fr-FR')}</td>
                        <td>${pret.misyassurance ? 'Oui' : 'Non'}</td>
                    </tr>
                `).join('');
            }

            handlePretSelection(checkbox) {
                const pretId = parseInt(checkbox.value);
                const row = checkbox.closest('tr');

                if (checkbox.checked) {
                    if (this.selectedPrets.length >= 2) {
                        checkbox.checked = false;
                        this.showMessage('Vous ne pouvez sélectionner que 2 prêts maximum', 'error');
                        return;
                    }
                    this.selectedPrets.push(pretId);
                    row.classList.add('selected-row');
                } else {
                    this.selectedPrets = this.selectedPrets.filter(id => id !== pretId);
                    row.classList.remove('selected-row');
                }

                this.updateCompareButton();
            }

            updateCompareButton() {
                const compareBtn = document.getElementById('compareBtn');
                compareBtn.disabled = this.selectedPrets.length !== 2;
                
                if (this.selectedPrets.length === 2) {
                    compareBtn.textContent = 'Comparer les prêts sélectionnés';
                } else {
                    compareBtn.textContent = `Comparer les prêts sélectionnés (${this.selectedPrets.length}/2)`;
                }
            }

            async comparePrets() {
                if (this.selectedPrets.length !== 2) {
                    this.showMessage('Veuillez sélectionner exactement 2 prêts', 'error');
                    return;
                }

                try {
                    const response = await fetch('./api/prets/comparer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            idpret1: this.selectedPrets[0],
                            idpret2: this.selectedPrets[1]
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`Erreur HTTP: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.error) {
                        throw new Error(result.error);
                    }

                    this.lastComparison = result;
                    this.renderComparison(result);
                    this.showMessage('Comparaison effectuée avec succès', 'success');

                    document.getElementById('exportBtn').style.display = 'inline-block';

                } catch (error) {
                    console.error('Erreur lors de la comparaison:', error);
                    this.showMessage('Erreur lors de la comparaison: ' + error.message, 'error');
                }
            }

            renderComparison(result) {
                const content = document.getElementById('comparisonContent');
                const winner = result.meilleur.gagnant;

                content.innerHTML = `
                    <div class="comparison-grid">
                        <div class="pret-card ${winner === 'pret1' ? 'winner' : ''}">
                            <h3>Prêt N°${result.pret1.idpret} ${winner === 'pret1' ? '🏆 RECOMMANDÉ' : ''}</h3>
                            <div class="pret-info">
                                <span class="label">Client:</span>
                                <span class="value">${result.pret1.client_prenom} ${result.pret1.client_nom}</span>
                            </div>
                            <div class="pret-info">
                                <span class="label">Type:</span>
                                <span class="value">${result.pret1.type_pret}</span>
                            </div>
                            <div class="pret-info">
                                <span class="label">Montant:</span>
                                <span class="value">${new Intl.NumberFormat('fr-FR').format(result.pret1.montant)} MGA</span>
                            </div>
                            <div class="pret-info">
                                <span class="label">Durée:</span>
                                <span class="value">${result.pret1.duree} mois</span>
                            </div>
                            <div class="pret-info">
                                <span class="label">Taux:</span>
                                <span class="value">${result.pret1.taux_annuel}%</span>
                            </div>
                        </div>
                        
                        <div class="pret-card ${winner === 'pret2' ? 'winner' : ''}">
                            <h3>Prêt N°${result.pret2.idpret} ${winner === 'pret2' ? '🏆 RECOMMANDÉ' : ''}</h3>
                            <div class="pret-info">
                                <span class="label">Client:</span>
                                <span class="value">${result.pret2.client_prenom} ${result.pret2.client_nom}</span>
                            </div>
                            <div class="pret-info">
                                <span class="label">Type:</span>
                                <span class="value">${result.pret2.type_pret}</span>
                            </div>
                            <div class="pret-info">
                                <span class="label">Montant:</span>
                                <span class="value">${new Intl.NumberFormat('fr-FR').format(result.pret2.montant)} MGA</span>
                            </div>
                            <div class="pret-info">
                                <span class="label">Durée:</span>
                                <span class="value">${result.pret2.duree} mois</span>
                            </div>
                            <div class="pret-info">
                                <span class="label">Taux:</span>
                                <span class="value">${result.pret2.taux_annuel}%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="metrics-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Métrique</th>
                                    <th>Prêt N°${result.pret1.idpret}</th>
                                    <th>Prêt N°${result.pret2.idpret}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Revenus totaux</td>
                                    <td class="${result.pret1.revenus_totaux > result.pret2.revenus_totaux ? 'better' : ''}">${new Intl.NumberFormat('fr-FR').format(result.pret1.revenus_totaux)} MGA</td>
                                    <td class="${result.pret2.revenus_totaux > result.pret1.revenus_totaux ? 'better' : ''}">${new Intl.NumberFormat('fr-FR').format(result.pret2.revenus_totaux)} MGA</td>
                                </tr>
                                <tr>
                                    <td>Rentabilité</td>
                                    <td class="${result.pret1.rentabilite > result.pret2.rentabilite ? 'better' : ''}">${result.pret1.rentabilite}%</td>
                                    <td class="${result.pret2.rentabilite > result.pret1.rentabilite ? 'better' : ''}">${result.pret2.rentabilite}%</td>
                                </tr>
                                <tr>
                                    <td>Ratio risque/rendement</td>
                                    <td class="${result.pret1.ratio_risque_rendement > result.pret2.ratio_risque_rendement ? 'better' : ''}">${result.pret1.ratio_risque_rendement}</td>
                                    <td class="${result.pret2.ratio_risque_rendement > result.pret1.ratio_risque_rendement ? 'better' : ''}">${result.pret2.ratio_risque_rendement}</td>
                                </tr>
                                <tr>
                                    <td>Revenus mensuels moyens</td>
                                    <td class="${result.pret1.revenus_mensuels_moyens > result.pret2.revenus_mensuels_moyens ? 'better' : ''}">${new Intl.NumberFormat('fr-FR').format(result.pret1.revenus_mensuels_moyens)} MGA</td>
                                    <td class="${result.pret2.revenus_mensuels_moyens > result.pret1.revenus_mensuels_moyens ? 'better' : ''}">${new Intl.NumberFormat('fr-FR').format(result.pret2.revenus_mensuels_moyens)} MGA</td>
                                </tr>
                                <tr>
                                    <td><strong>Score final</strong></td>
                                    <td class="${result.meilleur.score1 > result.meilleur.score2 ? 'better' : ''}">${result.meilleur.score1}/100</td>
                                    <td class="${result.meilleur.score2 > result.meilleur.score1 ? 'better' : ''}">${result.meilleur.score2}/100</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                `;

                document.getElementById('comparisonResult').style.display = 'block';
                document.getElementById('comparisonResult').scrollIntoView({ behavior: 'smooth' });
            }

            async exportPdf() {
                if (!this.lastComparison) {
                    this.showMessage('Aucune comparaison à exporter', 'error');
                    return;
                }

                try {
                    const response = await fetch('./api/prets/comparaison/pdf', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            idpret1: this.selectedPrets[0],
                            idpret2: this.selectedPrets[1]
                        })
                    });

                    if (response.ok) {
                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'comparaison_prets.pdf';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                    } else {
                        throw new Error('Erreur lors de la génération du PDF');
                    }
                } catch (error) {
                    this.showMessage('Erreur lors de l\'export PDF: ' + error.message, 'error');
                }
            }

            reset() {
                this.selectedPrets = [];
                this.lastComparison = null;
                document.getElementById('comparisonResult').style.display = 'none';
                document.getElementById('exportBtn').style.display = 'none';
                
                // Décocher toutes les cases et retirer la classe selected-row
                document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                    cb.checked = false;
                    cb.closest('tr').classList.remove('selected-row');
                });
                
                this.updateCompareButton();
                this.showMessage('Sélection réinitialisée', 'success');
            }

            showMessage(message, type = 'info') {
                const messageDiv = document.getElementById('message');
                messageDiv.innerHTML = `<div class="${type}">${message}</div>`;

                setTimeout(() => {
                    messageDiv.innerHTML = '';
                }, 5000);
            }
        }

        // Initialisation
        const comparator = new PretComparator();
    </script>
</body>
</html>