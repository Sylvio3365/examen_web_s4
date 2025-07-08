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
    <div class="container mt-4">
        <h2 class="mb-4">Liste des clients avec leurs prêts</h2>
        
        <div class="accordion" id="clientsAccordion">
            <!-- Les clients seront chargés dynamiquement ici -->
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        const apiBase = "<?php echo $apiBase ?>";

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

        function chargerClientsAvecPrets() {
            // Utiliser la nouvelle route API pour récupérer les données JSON
            ajax("GET", "/api/clients/avec-prets", null, (response) => {
                if (response.status === 'success') {
                    const clients = response.data.clients;
                    const accordion = document.getElementById("clientsAccordion");
                    accordion.innerHTML = "";

                    clients.forEach(client => {
                        const accordionItem = document.createElement("div");
                        accordionItem.className = "accordion-item";
                        
                        const accordionHeader = document.createElement("h2");
                        accordionHeader.className = "accordion-header";
                        accordionHeader.id = `heading${client.idclient}`;
                        
                        const accordionButton = document.createElement("button");
                        accordionButton.className = "accordion-button collapsed";
                        accordionButton.type = "button";
                        accordionButton.setAttribute("data-bs-toggle", "collapse");
                        accordionButton.setAttribute("data-bs-target", `#collapse${client.idclient}`);
                        accordionButton.setAttribute("aria-expanded", "false");
                        accordionButton.setAttribute("aria-controls", `collapse${client.idclient}`);
                        
                        // Contenu du bouton
                        accordionButton.innerHTML = `
                            ${client.prenom} ${client.nom}
                            <span class="badge bg-primary ms-2">${client.nombre_prets} prêt(s)</span>
                            <span class="badge bg-success ms-2">${formatNumber(client.total_montant)} MGA</span>
                        `;
                        
                        accordionHeader.appendChild(accordionButton);
                        
                        const accordionCollapse = document.createElement("div");
                        accordionCollapse.id = `collapse${client.idclient}`;
                        accordionCollapse.className = "accordion-collapse collapse";
                        accordionCollapse.setAttribute("aria-labelledby", `heading${client.idclient}`);
                        accordionCollapse.setAttribute("data-bs-parent", "#clientsAccordion");
                        
                        const accordionBody = document.createElement("div");
                        accordionBody.className = "accordion-body";
                        
                        // Informations client
                        accordionBody.innerHTML += `
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Téléphone:</strong> ${client.telephone || 'Non renseigné'}</p>
                                    <p><strong>Email:</strong> ${client.email || 'Non renseigné'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Adresse:</strong> ${client.adresse || 'Non renseignée'}</p>
                                    <p><strong>Date naissance:</strong> ${formatDate(client.dtn)}</p>
                                </div>
                            </div>
                        `;
                        
                        // Détails des prêts
                        if (client.prets && client.prets.length > 0) {
                            accordionBody.innerHTML += `
                                <h5 class="mb-3">Détails des prêts</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="table-prets-${client.idclient}">
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
                                            ${client.prets.map(pret => `
                                                <tr>
                                                    <td>${pret.idpret}</td>
                                                    <td>${escapeHtml(pret.type_pret)}</td>
                                                    <td>${formatNumber(pret.montant)} MGA</td>
                                                    <td>${pret.duree} mois</td>
                                                    <td>${formatDateTime(pret.derniere_modification)}</td>
                                                    <td>
                                                        <span class="badge ${getStatusClass(pret.statut)}">
                                                            ${getStatusText(pret.statut)}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button onclick="telechargerPdf(${pret.idpret})" 
                                                                class="btn btn-sm btn-primary">
                                                                PDF
                                                        </button>
                                                        <a href="/pret/details/${pret.idpret}" 
                                                           class="btn btn-sm btn-info">
                                                           Détails
                                                        </a>
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            `;
                        } else {
                            accordionBody.innerHTML += `
                                <div class="alert alert-info">Ce client n'a aucun prêt enregistré</div>
                            `;
                        }
                        
                        accordionCollapse.appendChild(accordionBody);
                        accordionItem.appendChild(accordionHeader);
                        accordionItem.appendChild(accordionCollapse);
                        accordion.appendChild(accordionItem);
                    });

                    // Initialiser DataTables pour chaque tableau
                    clients.forEach(client => {
                        if (client.prets && client.prets.length > 0) {
                            $(`#table-prets-${client.idclient}`).DataTable({
                                language: {
                                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                                }
                            });
                        }
                    });
                } else {
                    console.error("Erreur lors du chargement des clients:", response.message);
                }
            });
        }

        // Fonction pour télécharger le PDF
        function telechargerPdf(idPret) {
            const pdfUrl = `${apiBase}/pret/generate-pdf/${idPret}`;
            
            // Créer un lien temporaire pour forcer le téléchargement
            const link = document.createElement('a');
            link.href = pdfUrl;
            link.download = `pret_${idPret}.pdf`;
            link.target = '_blank';
            
            // Déclencher le téléchargement
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Fonctions utilitaires
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

        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function getStatusClass(status) {
            const classes = {
                1: 'bg-warning',
                2: 'bg-success',
                3: 'bg-danger'
            };
            return classes[status] || 'bg-secondary';
        }

        function getStatusText(status) {
            const texts = ['En attente', 'Validé', 'Annulé'];
            return texts[status - 1] || 'Inconnu';
        }

        // Charger les données au démarrage
        document.addEventListener('DOMContentLoaded', chargerClientsAvecPrets);
    </script>
