<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des clients</title>
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

        .search-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .search-box {
            position: relative;
            max-width: 400px;
            margin: 0 auto;
        }

        .search-box input {
            width: 100%;
            padding: 15px 20px;
            font-size: 16px;
            border: 2px solid #e1e8ed;
            border-radius: 25px;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .clients-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .client-item {
            border-bottom: 1px solid #f1f3f4;
        }

        .client-item:last-child {
            border-bottom: none;
        }

        .client-header {
            padding: 20px 25px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .client-header:hover {
            background-color: #f8f9fa;
        }

        .client-main-info {
            flex: 1;
        }

        .client-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .client-meta {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .client-badges {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge-primary {
            background-color: #3498db;
            color: white;
        }

        .badge-success {
            background-color: #2ecc71;
            color: white;
        }

        .toggle-icon {
            margin-left: 15px;
            font-size: 1.2rem;
            color: #7f8c8d;
            transition: transform 0.3s ease;
        }

        .toggle-icon.rotate {
            transform: rotate(180deg);
        }

        .client-details {
            padding: 0 25px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .client-details.show {
            max-height: 1000px;
            padding: 25px;
        }

        .pret-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .pret-table th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e1e8ed;
        }

        .pret-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f3f4;
        }

        .pret-table tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-en-attente {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-valide {
            background-color: #d1f2eb;
            color: #0f5132;
        }

        .status-annule {
            background-color: #f8d7da;
            color: #721c24;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 500;
            margin-right: 8px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
            font-size: 1.1rem;
        }

        .no-prets {
            text-align: center;
            padding: 40px 20px;
            color: #7f8c8d;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .client-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .client-badges {
                align-self: flex-end;
            }

            .pret-table {
                font-size: 0.9rem;
            }

            .pret-table th,
            .pret-table td {
                padding: 8px 6px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Liste des clients</h1>
            <p>Gestion des clients et de leurs prêts</p>
        </div>
        
        <div class="search-section">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Rechercher un client par nom ou prénom..." />
            </div>
        </div>
        
        <div class="clients-section">
            <div id="clientsList">
                <!-- Les clients seront chargés ici -->
            </div>
        </div>
        
        <div id="noResults" class="no-results" style="display: none;">
            <p>Aucun client trouvé</p>
        </div>
    </div>

    <script>
        const apiBase = "http://localhost/examen_web_s4/ws";
        let allClients = [];

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

        function toggleClient(clientId) {
            const details = document.getElementById(`details-${clientId}`);
            const icon = document.getElementById(`icon-${clientId}`);
            
            if (details.classList.contains('show')) {
                details.classList.remove('show');
                icon.classList.remove('rotate');
            } else {
                details.classList.add('show');
                icon.classList.add('rotate');
            }
        }

        function formatNumber(num) {
            return num ? num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") : '0';
        }

        function formatDate(dateStr) {
            if (!dateStr) return 'Non renseignée';
            const date = new Date(dateStr);
            return date.toLocaleDateString('fr-FR');
        }

        function formatDateTime(dateTimeStr) {
            if (!dateTimeStr) return 'Non renseignée';
            const date = new Date(dateTimeStr);
            return date.toLocaleString('fr-FR');
        }

        function getStatusText(status) {
            switch(parseInt(status)) {
                case 1: return 'En attente';
                case 2: return 'Validé';
                case 3: return 'Annulé';
                default: return 'Inconnu';
            }
        }

        function getStatusClass(status) {
            switch(parseInt(status)) {
                case 1: return 'status-en-attente';
                case 2: return 'status-valide';
                case 3: return 'status-annule';
                default: return 'status-en-attente';
            }
        }

        function telechargerPdf(idPret) {
            const pdfUrl = `${apiBase}/pret/generate-pdf/${idPret}`;
            const link = document.createElement('a');
            link.href = pdfUrl;
            link.download = `pret_${idPret}.pdf`;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function voirDetails(idPret) {
            window.location.href = `/pret/details/${idPret}`;
        }

        function renderClient(client) {
            return `
                <div class="client-item">
                    <div class="client-header" onclick="toggleClient(${client.idclient})">
                        <div class="client-main-info">
                            <div class="client-name">${client.prenom} ${client.nom}</div>
                            <div class="client-meta">Né(e) le: ${formatDate(client.dtn)}</div>
                        </div>
                        <div class="client-badges">
                            <span class="badge badge-primary">${client.nombre_prets} prêt(s)</span>
                            <span class="badge badge-success">${formatNumber(client.total_montant)} MGA</span>
                        </div>
                        <span class="toggle-icon" id="icon-${client.idclient}">▼</span>
                    </div>
                    
                    <div class="client-details" id="details-${client.idclient}">
                        ${client.prets && client.prets.length > 0 ? `
                            <table class="pret-table">
                                <thead>
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
                                    ${client.prets.map(pret => `
                                        <tr>
                                            <td>${pret.idpret}</td>
                                            <td>${pret.type_pret}</td>
                                            <td>${formatNumber(pret.montant)} MGA</td>
                                            <td>${pret.duree} mois</td>
                                            <td>${formatDateTime(pret.derniere_modification)}</td>
                                            <td>
                                                <span class="status-badge ${getStatusClass(pret.statut)}">
                                                    ${getStatusText(pret.statut)}
                                                </span>
                                            </td>
                                            <td>
                                                <button onclick="telechargerPdf(${pret.idpret})" class="btn btn-primary">
                                                    PDF
                                                </button>
                                                <button onclick="voirDetails(${pret.idpret})" class="btn btn-secondary">
                                                    Détails
                                                </button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        ` : `
                            <div class="no-prets">
                                Ce client n'a aucun prêt enregistré
                            </div>
                        `}
                    </div>
                </div>
            `;
        }

        function renderClients(clients) {
            const container = document.getElementById("clientsList");
            const noResults = document.getElementById("noResults");
            
            if (clients.length === 0) {
                container.innerHTML = "";
                noResults.style.display = "block";
            } else {
                container.innerHTML = clients.map(renderClient).join('');
                noResults.style.display = "none";
            }
        }

        function filterClients(searchTerm) {
            const filtered = allClients.filter(client => {
                const fullName = `${client.prenom} ${client.nom}`.toLowerCase();
                return fullName.includes(searchTerm.toLowerCase());
            });
            renderClients(filtered);
        }

        function chargerClients() {
            ajax("GET", "/api/clients/avec-prets", null, (response) => {
                if (response.status === 'success') {
                    allClients = response.data.clients;
                    renderClients(allClients);
                } else {
                    console.error("Erreur:", response.message);
                    document.getElementById("clientsList").innerHTML = `
                        <div style="text-align: center; color: #e74c3c; padding: 40px;">
                            <p>Erreur lors du chargement: ${response.message}</p>
                        </div>
                    `;
                }
            });
        }

        // Event listeners
        document.getElementById("searchInput").addEventListener("input", (e) => {
            filterClients(e.target.value);
        });

        // Charger les clients au démarrage
        chargerClients();
    </script>
</body>
</html>