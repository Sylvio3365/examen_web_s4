<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un fond</title>
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

        .message {
            margin-top: 20px;
            color: #7f8c8d;
            font-size: 1rem;
        }

        .capital-info {
            margin-top: 30px;
            color: #2c3e50;
            font-size: 1.3rem;
            font-weight: 600;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Ajouter un fond</h1>
            <p>Gestion des fonds de l'établissement financier</p>
        </div>

        <div class="form-section">
            <div class="form-group">
                <input type="number" id="montant" class="form-control" placeholder="Montant du fond (Ar)" step="0.01" required>
            </div>
            <div class="form-group">
                <input type="date" id="date_" class="form-control" required>
            </div>
            <button class="btn btn-primary" onclick="ajouterFond()">Ajouter</button>

            <p id="message" class="message"></p>
        </div>

        <div class="capital-info">
            Capital actuel disponible : <span id="capital">...</span> Ar
        </div>
    </div>

    <script>
        const apiBase = "<?php echo $apiBase ?>";

        function ajouterFond() {
            const montant = document.getElementById("montant").value;
            const date_ = document.getElementById("date_").value;

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
</body>
</html>