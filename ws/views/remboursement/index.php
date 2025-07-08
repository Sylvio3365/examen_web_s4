<style>
    body {
        font-family: sans-serif;
        padding: 20px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
    }

    th,
    td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    button.valider {
        background-color: green;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
    }

    button.valider:hover {
        background-color: darkgreen;
    }
</style>

<h1>Liste des remboursements en attente</h1>

<table id="table-remboursements">
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
                        <td><button class="valider" onclick="valider(${r.idremboursement})">Valider</button></td>
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