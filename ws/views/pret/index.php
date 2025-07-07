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

        <!-- Type de prêt -->
        <label for="type_pret">Type de prêt</label>
        <select id="type_pret"></select>

        <!-- Client -->
        <label for="idclient">Client</label>
        <select id="idclient">
            <option value="">-- Choisir un client --</option>
        </select>

        <!-- Montant -->
        <label for="montant">Montant (en Ar)</label>
        <input type="number" id="montant" step="50" />
        <input type="range" id="amountRange" step="50" />

        <!-- Durée -->
        <label for="duree">Durée (mois)</label>
        <input type="number" id="duree" />
        <input type="range" id="dureeRange" />

        <!-- Délai -->
        <label for="delai">Payer après (mois)</label>
        <input type="number" id="delai" value="0" min="0" max="12" style="width: 60px;" />

        <!-- Assurance -->
        <label>
            <input type="checkbox" id="avec_assurance" />
            Ajouter une assurance (calculée selon le type de prêt)
        </label>

        <!-- Résultat -->
        <div class="result-box" id="resultat">
            Mensualité : <span id="echeance">0</span> Ar
        </div>

        <button class="simulate-btn" onclick="calculer()">SIMULER UN PRÊT</button>
        <button class="simulate-btn" onclick="enregistrerPret()">ENREGISTRER LE PRÊT</button>
    </div>

    <script>
        const apiBase = "http://localhost/examen_web_s4/ws";
        let typePrets = [];

        const typeSelect = document.getElementById("type_pret");
        const montantInput = document.getElementById("montant");
        const montantRange = document.getElementById("amountRange");
        const dureeInput = document.getElementById("duree");
        const dureeRange = document.getElementById("dureeRange");
        const delaiInput = document.getElementById("delai");
        const avecAssuranceInput = document.getElementById("avec_assurance");
        const echeanceAffiche = document.getElementById("echeance");
        const idClientSelect = document.getElementById("idclient");

        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    try {
                        const json = JSON.parse(xhr.responseText);
                        callback(json);
                    } catch (e) {
                        alert("Erreur serveur : " + xhr.responseText);
                    }
                }
            };
            xhr.send(data);
        }

        function chargerTypesPret() {
            ajax("GET", "/typeprets", null, (response) => {
                typePrets = response;
                typeSelect.innerHTML = "";
                typePrets.forEach((tp, index) => {
                    const opt = document.createElement("option");
                    opt.value = index;
                    opt.textContent = `${tp.nom} - ${tp.taux_annuel}%`;
                    typeSelect.appendChild(opt);
                });
                typeSelect.addEventListener("change", appliquerInfosTypePret);
                appliquerInfosTypePret();
            });
        }

        function chargerClients() {
            ajax("GET", "/clients", null, (clients) => {
                idClientSelect.innerHTML = '<option value="">-- Choisir un client --</option>';
                clients.forEach(c => {
                    const option = document.createElement("option");
                    option.value = c.idclient;
                    option.textContent = `${c.nom} ${c.prenom}`;
                    idClientSelect.appendChild(option);
                });
            });
        }

        function appliquerInfosTypePret() {
            const tp = typePrets[typeSelect.value];
            if (!tp) return;

            const min = parseFloat(tp.montant_min);
            const max = parseFloat(tp.montant_max);
            const dureeMax = parseInt(tp.duree_max);

            montantInput.min = montantRange.min = min;
            montantInput.max = montantRange.max = max;
            montantInput.value = montantRange.value = min;

            dureeInput.min = dureeRange.min = 1;
            dureeInput.max = dureeRange.max = dureeMax;
            dureeInput.value = dureeRange.value = dureeMax;

            calculer();
        }

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

        avecAssuranceInput.addEventListener("change", calculer); // ✅ AJOUT ICI

        function calculer() {
            const tp = typePrets[typeSelect.value];
            const montant = parseFloat(montantInput.value);
            const duree = parseInt(dureeInput.value);
            const tauxMensuel = parseFloat(tp.taux_annuel) / 100 / 12;
            const tauxAssurance = avecAssuranceInput.checked ? parseFloat(tp.taux_assurance || 0) : 0;

            if (isNaN(montant) || isNaN(duree) || duree <= 0) {
                echeanceAffiche.textContent = "0";
                return;
            }

            // Calcul de la mensualité sans assurance
            let mensualite = 0;
            if (tauxMensuel === 0) {
                mensualite = montant / duree;
            } else {
                mensualite = (montant * tauxMensuel) / (1 - Math.pow(1 + tauxMensuel, -duree));
            }

            // Calcul assurance totale puis par mois
            const assuranceTotale = montant * (tauxAssurance / 100);
            const assuranceMensuelle = assuranceTotale / duree;
            // Mensualité finale
            const mensualiteTotale = mensualite + assuranceMensuelle;
            // Affichage
            echeanceAffiche.textContent = Math.round(mensualiteTotale).toLocaleString("fr-FR");
        }

        function enregistrerPret() {
            const tp = typePrets[typeSelect.value];
            const idclient = idClientSelect.value;

            if (!idclient) {
                alert("Veuillez sélectionner un client !");
                return;
            }

            const data = `montant=${montantInput.value}&duree=${dureeInput.value}&idtypepret=${tp.idtypepret}&idclient=${idclient}&delais=${delaiInput.value}&assurance=${avecAssuranceInput.checked ? 1 : 0}`;
            ajax("POST", "/prets/add", data, (res) => {
                if (res.status === "success") {
                    alert("✅ Prêt enregistré avec succès !");
                } else {
                    alert("❌ Erreur : " + (res.message || "Échec enregistrement"));
                }
            });
        }

        window.onload = () => {
            chargerTypesPret();
            chargerClients();
        };
    </script>
</body>

</html>