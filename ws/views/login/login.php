<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Connexion</h3>

                        <form id="loginForm">
                            <div class="mb-3">
                                <input type="text" id="nom" class="form-control" placeholder="Nom d'utilisateur" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" id="mdp" class="form-control" placeholder="Mot de passe" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" id="loginBtn" class="btn btn-primary">Se connecter</button>
                            </div>
                            <div id="message" class="mt-3"></div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

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

            if (!nom || !mdp) {
                showMessage("Veuillez remplir tous les champs.", "danger");
                return;
            }

            loginBtn.disabled = true;
            loginBtn.textContent = "Connexion...";

            const data = `nom=${encodeURIComponent(nom)}&mdp=${encodeURIComponent(mdp)}`;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", apiBase + "/login", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    loginBtn.disabled = false;
                    loginBtn.textContent = "Se connecter";

                    try {
                        const response = JSON.parse(xhr.responseText);

                        if (xhr.status === 200 && response.success) {
                            showMessage("Connexion réussie ! Redirection...", "success");

                            setTimeout(() => {
                                window.location.href = apiBase + "/template";
                            }, 1000);
                        } else {
                            showMessage(response.error || "Erreur de connexion", "danger");
                        }
                    } catch (e) {
                        showMessage("Erreur serveur: réponse invalide", "danger");
                    }
                }
            };

            xhr.onerror = function() {
                loginBtn.disabled = false;
                loginBtn.textContent = "Se connecter";
                showMessage("Erreur de connexion réseau", "danger");
            };

            xhr.send(data);
        }

        function showMessage(text, type) {
            const messageDiv = document.getElementById("message");
            messageDiv.innerHTML = `<div class="alert alert-${type}" role="alert">${text}</div>`;
        }
    </script>

</body>

</html>