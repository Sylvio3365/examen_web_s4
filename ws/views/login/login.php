<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <h1>Connexion</h1>
    
    <form id="loginForm">
        <input type="text" id="nom" placeholder="Nom d'utilisateur" required>
        <input type="password" id="mdp" placeholder="Mot de passe" required>
        <button type="submit" id="loginBtn">Se connecter</button>
        <div id="message"></div>
    </form>

    <script>
        const apiBase = "http://localhost/examen_web_s4/ws";
        
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            loginPost();
        });

        function loginPost() {
            const nom = document.getElementById("nom").value.trim();
            const mdp = document.getElementById("mdp").value.trim();
            const messageDiv = document.getElementById("message");
            const loginBtn = document.getElementById("loginBtn");

            // Validation côté client
            if (!nom || !mdp) {
                showMessage("Veuillez remplir tous les champs.", "error");
                return;
            }

            // Désactiver le bouton pendant la requête
            loginBtn.disabled = true;
            loginBtn.textContent = "Connexion...";

            const data = `nom=${encodeURIComponent(nom)}&mdp=${encodeURIComponent(mdp)}`;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", apiBase + "/login", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    // Réactiver le bouton
                    loginBtn.disabled = false;
                    loginBtn.textContent = "Se connecter";

                    try {
                        const response = JSON.parse(xhr.responseText);
                        console.log("Réponse serveur:", response); // Debug
                        
                        if (xhr.status === 200 && response.success) {
                            showMessage("Connexion réussie ! Redirection...", "success");
                            
                            // Redirection après succès
                            setTimeout(() => {
                                window.location.href = apiBase + "/template";
                            }, 1000);
                        } else {
                            showMessage(response.error || "Erreur de connexion", "error");
                        }
                    } catch (e) {
                        console.error("Erreur JSON:", e);
                        console.error("Réponse brute:", xhr.responseText);
                        showMessage("Erreur serveur: réponse invalide", "error");
                    }
                }
            };

            xhr.onerror = function() {
                loginBtn.disabled = false;
                loginBtn.textContent = "Se connecter";
                showMessage("Erreur de connexion réseau", "error");
            };

            xhr.send(data);
        }

        function showMessage(text, type) {
            const messageDiv = document.getElementById("message");
            messageDiv.textContent = text;
            messageDiv.className = `message ${type}`;
        }
    </script>
</body>
</html>