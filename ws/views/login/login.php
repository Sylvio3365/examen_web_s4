<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion - Espace Client</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* --- Tout ton style moderne est ici --- */
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 48px;
            max-width: 420px;
            width: 100%;
            border: 1px solid #e5e7eb;
            animation: fadeInUp 0.6s ease-out;
        }

        .logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(45deg, #2563eb, #1d4ed8);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .logo-icon::after {
            content: "🏦";
            font-size: 24px;
        }

        h1 {
            color: #1f2937;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
            text-align: center;
        }

        .subtitle {
            color: #6b7280;
            font-size: 15px;
            margin-bottom: 32px;
            text-align: center;
        }

        label {
            color: #374151;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
        }

        input {
            width: 100%;
            padding: 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            background: #fafafa;
            color: #1f2937;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: #2563eb;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(45deg, #2563eb, #1d4ed8);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
        }

        .login-btn:disabled {
            background: #d1d5db;
            cursor: not-allowed;
        }

        .message {
            margin-top: 16px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            display: none;
        }

        .message.success {
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
        }

        .message.danger {
            background: linear-gradient(45deg, #ef4444, #dc2626);
            color: white;
        }

        .security-info {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
            color: #6b7280;
        }

        .security-badge {
            color: #059669;
            margin-top: 8px;
            font-weight: 500;
        }

        .security-badge::before {
            content: "🔒 ";
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="logo">
            <div class="logo-icon"></div>
            <h1>Connexion</h1>
        </div>

        <form id="loginForm">
            <label for="nom">Nom d'utilisateur</label>
            <input type="text" id="nom" placeholder="Votre nom d'utilisateur" required>

            <label for="mdp">Mot de passe</label>
            <input type="password" id="mdp" placeholder="Votre mot de passe" required>

            <button type="submit" id="loginBtn" class="login-btn">Se connecter</button>

            <div id="message" class="message"></div>
        </form>

        <div class="security-info">
            <p>Votre connexion est sécurisée par un chiffrement SSL 256-bit</p>
            <div class="security-badge">Connexion sécurisée</div>
        </div>
    </div>
    <?php $base_url = Flight::get('base_url'); ?>
    <script>
        const apiBase = "<?= $base_url ?>";

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
                                window.location.href = apiBase + "/acceuil";
                            }, 1500);
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
            messageDiv.textContent = text;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = "block";
        }
    </script>

</body>

</html>