<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fonds</title>
</head>

<body>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <title>Ajout de fond</title>
        <link rel="stylesheet" href="public/css/style.css">
    </head>

    <body>

        <h1>Ajouter un fond à l’établissement financier</h1>

        <div>
            <input type="number" id="montant" placeholder="Montant du fond" step="0.01" required>
            <input type="date" id="date_" placeholder="Date du fond" required>
            <button onclick="ajouterFond()">Ajouter</button>
        </div>

        <h2>Capital actuel disponible : <span id="capital">...</span> Ar</h2>


        <p id="message"></p>

        <script>
            const apiBase = "http://localhost/examen_web_s4/ws";

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

            // Appeler la fonction au chargement
            chargerCapital();

        </script>

    </body>

    </html>

</body>

</html>