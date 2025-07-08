<style>
    #table-remboursements {
        margin-top: 20px;
    }
</style>

<div class="container">
    <h1 class="mb-4">Liste des remboursements en attente</h1>

    <table id="table-remboursements" class="table table-striped">
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
                            <td><button class="btn btn-success btn-sm" onclick="valider(${r.idremboursement})">Valider</button></td>
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
