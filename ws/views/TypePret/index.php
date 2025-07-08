<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des types de prêt</title>
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

        .form-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            font-size: 16px;
            border: 2px solid #e1e8ed;
            border-radius: 25px;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
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

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .form-group input {
                padding: 12px 15px;
                font-size: 14px;
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
            <h1>Gestion des types de prêt</h1>
            <p>Administration des types de prêt disponibles</p>
        </div>

        <div class="form-section">
            <div class="form-group">
                <input type="hidden" id="idtypepret">
                <input type="text" id="nom" class="form-control" placeholder="Nom du type">
            </div>
            <div class="form-group">
                <input type="number" id="taux_annuel" class="form-control" placeholder="Taux annuel (%)" step="0.01">
            </div>
            <div class="form-group">
                <input type="number" id="montant_min" class="form-control" placeholder="Montant minimum">
            </div>
            <div class="form-group">
                <input type="number" id="montant_max" class="form-control" placeholder="Montant maximum">
            </div>
            <div class="form-group">
                <input type="number" id="duree_max" class="form-control" placeholder="Durée max (mois)">
            </div>
            <div class="form-group">
                <input type="number" id="taux_assurance" class="form-control" placeholder="Taux d'assurance">
            </div>
            <button class="btn btn-primary" onclick="ajouterOuModifier()">Ajouter / Modifier</button>
        </div>

        <div class="table-section">
            <table id="table-typeprets" class="pret-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Taux annuel</th>
                        <th>Montant min</th>
                        <th>Montant max</th>
                        <th>Durée max</th>
                        <th>Taux assurance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script>
        const apiBase = "<?php echo $apiBase ?>";

        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    callback(JSON.parse(xhr.responseText));
                }
            };
            xhr.send(data);
        }

        function chargerTypePrets() {
            ajax("GET", "/typeprets", null, (data) => {
                const tbody = document.querySelector("#table-typeprets tbody");
                tbody.innerHTML = "";
                data.forEach(t => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${t.idtypepret}</td>
                        <td>${t.nom}</td>
                        <td>${t.taux_annuel}</td>
                        <td>${t.montant_min}</td>
                        <td>${t.montant_max}</td>
                        <td>${t.duree_max}</td>
                        <td>${t.taux_assurance}</td>
                        <td>
                            <button class="btn btn-primary" onclick='remplirFormulaire(${JSON.stringify(t)})'>✏️</button>
                            <button class="btn btn-secondary" onclick='supprimerTypePret(${t.idtypepret})'>🗑️</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            });
        }

        function ajouterOuModifier() {
            const idtypepret = document.getElementById("idtypepret").value;
            const nom = document.getElementById("nom").value;
            const taux_annuel = document.getElementById("taux_annuel").value;
            const montant_min = document.getElementById("montant_min").value;
            const montant_max = document.getElementById("montant_max").value;
            const duree_max = document.getElementById("duree_max").value;
            const taux_assurance = document.getElementById("taux_assurance").value;

            const data = {
                nom: nom,
                taux_annuel: parseFloat(taux_annuel),
                montant_min: parseFloat(montant_min),
                montant_max: parseFloat(montant_max),
                duree_max: parseFloat(duree_max),
                taux_assurance: parseFloat(taux_assurance)
            };

            const xhr = new XMLHttpRequest();
            xhr.open(idtypepret ? "PUT" : "POST", apiBase + `/typeprets/${idtypepret || ''}`, true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        resetForm();
                        chargerTypePrets();
                    } else {
                        console.error("Erreur:", xhr.status, xhr.statusText);
                    }
                }
            };
            xhr.send(JSON.stringify(data));
        }

        function remplirFormulaire(t) {
            document.getElementById("idtypepret").value = t.idtypepret;
            document.getElementById("nom").value = t.nom;
            document.getElementById("taux_annuel").value = t.taux_annuel;
            document.getElementById("montant_min").value = t.montant_min;
            document.getElementById("montant_max").value = t.montant_max;
            document.getElementById("duree_max").value = t.duree_max;
            document.getElementById("taux_assurance").value = t.taux_assurance;
        }

        function supprimerTypePret(id) {
            if (confirm("Supprimer ce type de prêt ?")) {
                ajax("DELETE", `/typeprets/${id}`, null, () => {
                    chargerTypePrets();
                });
            }
        }

        function resetForm() {
            document.getElementById("idtypepret").value = "";
            document.getElementById("nom").value = "";
            document.getElementById("taux_annuel").value = "";
            document.getElementById("montant_min").value = "";
            document.getElementById("montant_max").value = "";
            document.getElementById("duree_max").value = "";
            document.getElementById("taux_assurance").value = "";
        }

        chargerTypePrets();
    </script>
</body>
</html>