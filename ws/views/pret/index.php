<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Simulation de prêt</title>
    <link rel="stylesheet" href="public/css/style_pret.css" />
</head>

<body>
    <div class="container">
        <h2>Simulez votre prêt</h2>

        <label for="type_pret">Type de prêt</label>
        <select id="type_pret"></select>

        <label for="montant">Montant (en Ar)</label>
        <input type="number" id="montant" step="50" />
        <input type="range" id="amountRange" step="50" />

        <label for="duree">Durée (mois)</label>
        <input type="number" id="duree" />
        <input type="range" id="dureeRange" />

        <div style="margin-top: 20px;">
            Payer après :
            <input type="number" name="delai" id="delai" value="0" min="0" max="12" style="width: 60px;" /> mois
        </div>

        <div class="result-box" id="resultat">
            Mensualité : <span id="echeance">0</span> Ar
        </div>

        <button class="simulate-btn" onclick="calculer()">SIMULER UN PRÊT</button>
    </div>

    <script>
        const apiBase = "http://localhost/examen_web_s4/ws";
        let typePrets = [];

        const typeSelect = document.getElementById("type_pret");
        const montantInput = document.getElementById("montant");
        const montantRange = document.getElementById("amountRange");
        const dureeInput = document.getElementById("duree");
        const dureeRange = document.getElementById("dureeRange");
        const echeanceAffiche = document.getElementById("echeance");

        // AJAX helper
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

        // Charger types de prêts
        function chargerTypesPret() {
            ajax("GET", "/typeprets", null, (response) => {
                typePrets = response;
                typeSelect.innerHTML = "";

                typePrets.forEach((tp, index) => {
                    const opt = document.createElement("option");
                    opt.value = index;
                    opt.textContent = `Type ${tp.nom}, TA ${tp.taux_annuel} %`;
                    typeSelect.appendChild(opt);
                });

                typeSelect.addEventListener("change", appliquerInfosTypePret);
                appliquerInfosTypePret(); // Initialisation
            });
        }

        function appliquerInfosTypePret() {
            const tp = typePrets[typeSelect.value];

            const montantMin = parseFloat(tp.montant_min);
            const montantMax = parseFloat(tp.montant_max);
            const dureeMax = parseInt(tp.duree_max);
            const taux = parseFloat(tp.taux_annuel);

            const stepMontant = 50; // 🔁 Fixé à 50 Ar
            const stepDuree = 1;

            // Champs Montant
            montantInput.min = montantMin;
            montantInput.max = montantMax;
            montantInput.step = stepMontant;
            montantInput.value = montantMin;

            montantRange.min = montantMin;
            montantRange.max = montantMax;
            montantRange.step = stepMontant;
            montantRange.value = montantMin;

            // Champs Durée
            dureeInput.min = 1;
            dureeInput.max = dureeMax;
            dureeInput.step = stepDuree;
            dureeInput.value = dureeMax;

            dureeRange.min = 1;
            dureeRange.max = dureeMax;
            dureeRange.step = stepDuree;
            dureeRange.value = dureeMax;

            calculer();
        }

        // Synchronisation sliders <-> inputs
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

        // 💰 Calcul d'échéance mensuelle pour un prêt à annuités constantes
        function calculer() {
            if (typeSelect.value === "") return;

            const tp = typePrets[typeSelect.value];
            const montant = parseFloat(montantInput.value);
            const duree = parseInt(dureeInput.value);
            const tauxAnnuel = parseFloat(tp.taux_annuel) / 100;
            console.log(tauxAnnuel);
            const tauxMensuel = tauxAnnuel / 12;

            if (isNaN(montant) || isNaN(duree) || isNaN(tauxMensuel) || duree <= 0) {
                echeanceAffiche.textContent = "0";
                return;
            }

            let echeance = 0;

            if (tauxMensuel === 0) {
                echeance = montant / duree;
            } else {
                echeance = (montant * tauxMensuel) / (1 - Math.pow(1 + tauxMensuel, -duree));
            }

            echeanceAffiche.textContent = Math.round(echeance).toLocaleString("fr-FR");
        }

        window.onload = chargerTypesPret;
    </script>
</body>

</html>