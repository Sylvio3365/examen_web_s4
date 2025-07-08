<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des remboursements en attente</title>
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

        .table-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
            background-color: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Liste des remboursements en attente</h1>
            <p>Gestion des remboursements en attente de validation</p>
        </div>

        <div class="table-section">
            <table id="table-remboursements" class="pret-table">
                <thead>
                    <tr>
                        <th>ID prêt</th>
                        <th>Mois</th>
                        <th>Année</th>
                        <th>Capital restant</th>
                        <th>Intérêt</th>
                        <th>Assurance</th>
                        <th>Amortissement</th>
                        <th>Échéance</th>
                        <th>Valeur nette</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script>
        const apiBase = "<?php echo $apiBase ?>";

        function chargerRemboursementsEnAttente() {
            fetch(apiBase + "/remboursements/attente")
                .then(res => res.json())
                .then(remboursements => {
                    const tbody = document.querySelector("#table-remboursements tbody");
                    tbody.innerHTML = "";
                    remboursements.forEach(r => {
                        const tr = document.createElement("tr");
                        tr.innerHTML = `
                            <td>${r.idpret}</td>
                            <td>${r.mois}</td>
                            <td>${r.annee}</td>
                            <td>${r.emprunt_restant.toLocaleString('fr-FR')}</td>
                            <td>${r.interet_mensuel.toLocaleString('fr-FR')}</td>
                            <td>${r.assurance.toLocaleString('fr-FR')}</td>
                            <td>${r.amortissement.toLocaleString('fr-FR')}</td>
                            <td>${r.echeance.toLocaleString('fr-FR')}</td>
                            <td>${r.valeur_nette.toLocaleString('fr-FR')}</td>
                            <td><button class="btn btn-primary" onclick="valider(${r.idremboursement})">Valider</button></td>
                        `;
                        tbody.appendChild(tr);
                    });
                });
        }

        function valider(idremboursement) {
            if (!confirm("Confirmer la validation du remboursement ?")) return;

            fetch(`${apiBase}/remboursements/${idremboursement}/statut`, {
                method: "POST"
            })
                .then(res => {
                    if (res.ok) {
                        alert("✅ Remboursement validé !");
                        chargerRemboursementsEnAttente();
                    } else {
                        alert("❌ Erreur lors de la validation !");
                    }
                })
                .catch(() => {
                    alert("❌ Erreur réseau !");
                });
        }

        window.onload = chargerRemboursementsEnAttente;
    </script>
</body>
</html>