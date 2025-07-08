<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des types de prêt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card p-4 mb-4 text-center">
            <h2 class="mb-2">Gestion des types de prêt</h2>
            <p class="text-muted">Administration des types de prêt disponibles</p>
        </div>

        <!-- Formulaire -->
        <div class="card p-4 mb-4">
            <div class="row g-3">
                <input type="hidden" id="idtypepret">

                <div class="col-md-4">
                    <input type="text" id="nom" class="form-control" placeholder="Nom du type">
                </div>
                <div class="col-md-4">
                    <input type="number" id="taux_annuel" class="form-control" placeholder="Taux annuel (%)" step="0.01">
                </div>
                <div class="col-md-4">
                    <input type="number" id="montant_min" class="form-control" placeholder="Montant minimum">
                </div>
                <div class="col-md-4">
                    <input type="number" id="montant_max" class="form-control" placeholder="Montant maximum">
                </div>
                <div class="col-md-4">
                    <input type="number" id="duree_max" class="form-control" placeholder="Durée max (mois)">
                </div>
                <div class="col-md-4">
                    <input type="number" id="taux_assurance" class="form-control" placeholder="Taux d'assurance">
                </div>
            </div>
            <div class="mt-4 text-end">
                <button class="btn btn-primary" onclick="ajouterOuModifier()">Ajouter / Modifier</button>
            </div>
        </div>

        <!-- Tableau -->
        <div class="card p-4">
            <h5>Liste des types de prêt</h5>
            <div class="table-responsive mt-3">
                <table id="table-typeprets" class="table table-bordered table-striped">
                    <thead class="table-light">
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
    </div>

    <!-- Script -->
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
                        <button class="btn btn-sm btn-primary me-1" onclick='remplirFormulaire(${JSON.stringify(t)})'>✏️</button>
                        <button class="btn btn-sm btn-secondary" onclick='supprimerTypePret(${t.idtypepret})'>🗑️</button>
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