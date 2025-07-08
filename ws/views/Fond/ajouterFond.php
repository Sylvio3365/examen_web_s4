<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter un fond</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <!-- En-tête -->
        <div class="card p-4 mb-4 text-center">
            <h2 class="mb-2">Ajouter un fond</h2>
            <p class="text-muted">Gestion des fonds de l'établissement financier</p>
        </div>

        <!-- Formulaire -->
        <div class="card p-4 mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="number" id="montant" class="form-control" placeholder="Montant du fond (Ar)" step="0.01" required>
                </div>
                <div class="col-md-6">
                    <input type="date" id="date_" class="form-control" required>
                </div>
            </div>
            <div class="mt-4 text-end">
                <button class="btn btn-primary" onclick="ajouterFond()">Ajouter</button>
            </div>
            <div id="message" class="mt-3"></div>
        </div>

        <!-- Capital disponible -->
        <div class="card p-4">
            <h5 class="mb-0">Capital actuel disponible : <span id="capital">...</span> Ar</h5>
        </div>
    </div>
    <?php $base_url = Flight::get('base_url'); ?>
    <script>
        const apiBase = "<?= $base_url ?>";

        function ajouterFond() {
            const montant = document.getElementById("montant").value;
            const date_ = document.getElementById("date_").value;
            const messageDiv = document.getElementById("message");

            if (!montant || !date_) {
                showMessage("Veuillez remplir tous les champs.", "warning");
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
                        if (xhr.status === 200) {
                            showMessage(response.message, "success");
                            document.getElementById("montant").value = "";
                            document.getElementById("date_").value = "";
                            chargerCapital();
                        } else {
                            showMessage("Erreur: " + (response.error || "Erreur inconnue"), "danger");
                        }
                    } catch (e) {
                        console.error("Erreur parsing JSON:", e);
                        showMessage("Erreur de communication avec le serveur", "danger");
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

        function showMessage(msg, type) {
            const messageDiv = document.getElementById("message");
            messageDiv.innerHTML = `<div class="alert alert-${type}" role="alert">${msg}</div>`;
        }

        chargerCapital();
    </script>

</body>

</html>