<link rel="stylesheet" href="<?php echo $apiBase ?>/public/css/style.css">

<div class="container my-4">
    <h1 class="mb-4">Ajouter un fond à l’établissement financier</h1>

    <div class="mb-3">
        <input type="number" id="montant" class="form-control" placeholder="Montant du fond" step="0.01" required>
    </div>
    <div class="mb-3">
        <input type="date" id="date_" class="form-control" required>
    </div>
    <button class="btn btn-primary" onclick="ajouterFond()">Ajouter</button>

    <h2 class="mt-4">Capital actuel disponible : <span id="capital">...</span> Ar</h2>

    <p id="message" class="mt-3"></p>
</div>

<script>
    const apiBase = "<?php echo $apiBase ?>";

    function ajouterFond() {
        const montant = document.getElementById("montant").value;
        const date_ = document.getElementById("date_").value;

        // Validation côté client
        if (!montant || !date_) {
            document.getElementById("message").textContent = "Veuillez remplir tous les champs.";
            return;
        }

        const data = `montant=${encodeURIComponent(montant)}&date_=${encodeURIComponent(date_)}`;

        const xhr = new XMLHttpRequest();
        xhr.open("POST", apiBase + "/ajouterFond", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    console.log(response);
                    if (xhr.status === 200) {
                        document.getElementById("message").textContent = response.message;
                        document.getElementById("montant").value = "";
                        document.getElementById("date_").value = "";
                        chargerCapital();
                    } else {
                        document.getElementById("message").textContent = "Erreur: " + (response.error || "Erreur inconnue");
                    }
                } catch (e) {
                    document.getElementById("message").textContent = "Erreur de communication avec le serveur";
                    console.error("Erreur parsing JSON:", e);
                }
            }
        };
        xhr.send(data);
    }

    function chargerCapital() {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", apiBase + "/capital", true);
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                document.getElementById("capital").textContent = response.capital.toLocaleString();
            }
        };
        xhr.send();
    }

    chargerCapital();
</script>