<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des prêts en attente</title>
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

        .table-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .pret-table {
            width: 100%;
            border-collapse: collapse;
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
            background-color: #2ecc71;
            color: white;
        }

        .btn-primary:hover {
            background-color: #27ae60;
        }

        .btn-secondary {
            background-color: #e74c3c;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #c0392b;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .message {
            margin-top: 20px;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 1rem;
            text-align: center;
        }

        .message.success {
            background-color: #d1f2eb;
            color: #0f5132;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .pret-table {
                font-size: 0.9rem;
            }

            .pret-table th,
            .pret-table td {
                padding: 8px 6px;
            }

            .btn {
                padding: 6px 12px;
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Liste des prêts en attente</h1>
            <p>Gestion des prêts en attente de validation ou d'annulation</p>
        </div>

        <div class="table-section">
            <table class="pret-table">
                <thead>
                    <tr>
                        <th>ID Prêt</th>
                        <th>Client</th>
                        <th>Type de prêt</th>
                        <th>Montant</th>
                        <th>Date statut</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <!-- Les lignes seront ajoutées ici -->
                </tbody>
            </table>
        </div>

        <p id="message" class="message"></p>
    </div>
    <?php $apiBase = Flight::get('base_url'); ?>

    <script>
        const apiUrl = "<?php echo $apiBase ?>/pendingPret";
        const apiBase = "<?php echo $apiBase ?>";

        function chargerPrets() {
            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Erreur réseau");
                    }
                    return response.json();
                })
                .then(data => {
                    const tbody = document.getElementById("table-body");
                    tbody.innerHTML = "";

                    if (data.length === 0) {
                        tbody.innerHTML = "<tr><td colspan='7' style='text-align: center; padding: 40px; color: #7f8c8d; font-style: italic;'>Aucun prêt en attente</td></tr>";
                    } else {
                        data.forEach(pret => {
                            const tr = document.createElement("tr");
                            tr.innerHTML = `
                                <td>${pret.idpret}</td>
                                <td>${sanitize(pret.nom_client)}</td>
                                <td>${sanitize(pret.nom_typepret)}</td>
                                <td>${pret.montant ? pret.montant.toLocaleString('fr-FR') : 'N/A'}</td>
                                <td>${sanitize(pret.date_modif)}</td>
                                <td>${sanitize(pret.statut_valeur)}</td>
                                <td>
                                    <button class="btn btn-primary" onclick="validerPret(${pret.idpret}, ${pret.montant || 0})">
                                        Valider
                                    </button>
                                    <button class="btn btn-secondary" onclick="annulerPret(${pret.idpret})">
                                        Annuler
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                })
                .catch(error => {
                    afficherMessage("Erreur lors du chargement : " + error.message, "error");
                });
        }

        function validerPret(idpret, montant) {
            if (!montant || montant <= 0) {
                afficherMessage("Montant invalide pour ce prêt", "error");
                return;
            }

            const buttons = document.querySelectorAll('button');
            buttons.forEach(btn => btn.disabled = true);

            const data = {
                idpret: idpret,
                montant: montant
            };

            fetch(apiBase + "/validerPret", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Erreur réseau");
                    }
                    return response.json();
                })
                .then(result => {
                    if (result.error) {
                        afficherMessage("Erreur : " + result.error, "error");
                    } else {
                        afficherMessage(result.message || "Prêt validé avec succès", "success");
                        setTimeout(() => {
                            chargerPrets();
                        }, 1000);
                    }
                })
                .catch(error => {
                    afficherMessage("Erreur lors de la validation : " + error.message, "error");
                })
                .finally(() => {
                    buttons.forEach(btn => btn.disabled = false);
                });
        }

        function annulerPret(idpret) {
            if (!confirm("Êtes-vous sûr de vouloir annuler ce prêt ?")) {
                return;
            }

            const buttons = document.querySelectorAll('button');
            buttons.forEach(btn => btn.disabled = true);

            const data = {
                idpret: idpret
            };

            fetch(apiBase + "/annulerPret", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Erreur réseau");
                    }
                    return response.json();
                })
                .then(result => {
                    if (result.error) {
                        afficherMessage("Erreur : " + result.error, "error");
                    } else {
                        afficherMessage(result.message || "Prêt annulé avec succès", "success");
                        setTimeout(() => {
                            chargerPrets();
                        }, 1000);
                    }
                })
                .catch(error => {
                    afficherMessage("Erreur lors de l'annulation : " + error.message, "error");
                })
                .finally(() => {
                    buttons.forEach(btn => btn.disabled = false);
                });
        }

        function afficherMessage(message, type) {
            const messageEl = document.getElementById("message");
            messageEl.textContent = message;
            messageEl.className = `message ${type}`;

            setTimeout(() => {
                messageEl.textContent = "";
                messageEl.className = "message";
            }, 5000);
        }

        function sanitize(str) {
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#39;");
        }

        chargerPrets();
    </script>
</body>

</html>