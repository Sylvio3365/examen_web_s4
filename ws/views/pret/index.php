<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Simulation de prêt</title>
    <link rel="stylesheet" href="public/css/style_pret.css">
</head>

<body>
    <div class="container">
    <a href="interets" class="admin-link">Voir les intérêts</a>
        <h2>Simulez votre prêt</h2>

        <label for="type_pret">Type de prêt</label>
        <select id="type_pret">
            <option selected>Crédit Commerce/Services et Production</option>
            <option>Prêt personnel</option>
            <option>Prêt agricole</option>
        </select>

        <label for="montant">Montant (en MGA)</label>
        <input type="number" id="montant" value="200000" step="100000" min="200000" max="150000000" />
        <input type="range" id="amountRange" min="200000" max="150000000" step="100000" value="200000" />

        <label for="duree">Durée (mois)</label>
        <input type="number" id="duree" value="36" min="1" max="60" />
        <input type="range" id="dureeRange" min="1" max="60" value="36" />

        <div style="margin-top: 20px;">
            Payer après :
            <input type="number" name="delai" id="delai" value="0" min="0" max="12" style="width: 60px;" /> mois
        </div>

        <div class="result-box" id="resultat">
            Échéance : <span id="echeance">9 095</span> MGA
        </div>

        <button class="simulate-btn" onclick="calculer()">SIMULER UN PRÊT</button>
    </div>

    <script>
        const montantInput = document.getElementById("montant");
        const montantRange = document.getElementById("amountRange");
        const dureeInput = document.getElementById("duree");
        const dureeRange = document.getElementById("dureeRange");
        const echeanceAffiche = document.getElementById("echeance");

        // Synchronisation sliders et inputs
        montantRange.addEventListener("input", () => {
            montantInput.value = montantRange.value;
            calculer();
        });

        montantInput.addEventListener("input", () => {
            montantRange.value = montantInput.value;
            calculer();
        });

        dureeRange.addEventListener("input", () => {
            dureeInput.value = dureeRange.value;
            calculer();
        });

        dureeInput.addEventListener("input", () => {
            dureeRange.value = dureeInput.value;
            calculer();
        });

        function calculer() {
            const montant = parseFloat(montantInput.value);
            const duree = parseInt(dureeInput.value);
            const tauxAnnuel = 0.12; // 12% annuel
            const tauxMensuel = tauxAnnuel / 12;

            const echeance = (montant * tauxMensuel) / (1 - Math.pow(1 + tauxMensuel, -duree));
            echeanceAffiche.textContent = Math.round(echeance).toLocaleString('fr-FR');
        }

        window.onload = calculer;
    </script>
</body>

</html>