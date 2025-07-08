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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Espace Client</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 48px;
            width: 100%;
            max-width: 420px;
            border: 1px solid #e5e7eb;
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
            text-align: center;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #6b7280;
            text-align: center;
            font-size: 15px;
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #374151;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #fafafa;
            color: #1f2937;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #9ca3af;
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
            margin-top: 8px;
        }

        .login-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn:disabled {
            background: #d1d5db;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .login-btn.loading {
            position: relative;
            color: transparent;
        }

        .login-btn.loading::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .message {
            margin-top: 16px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .message.show {
            opacity: 1;
            transform: translateY(0);
        }

        .message.success {
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
            border: 1px solid #06d6a0;
        }

        .message.error {
            background: linear-gradient(45deg, #ef4444, #dc2626);
            color: white;
            border: 1px solid #f87171;
        }

        .security-info {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .security-info p {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.5;
        }

        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #059669;
            font-size: 12px;
            font-weight: 500;
            margin-top: 8px;
        }

        .security-badge::before {
            content: "🔒";
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 32px 24px;
                margin: 16px;
            }
            
            h1 {
                font-size: 24px;
            }
        }

        /* Animation d'entrée */
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

        .login-container {
            animation: fadeInUp 0.6s ease-out;
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
            <div class="form-group">
                <label for="nom">Nom d'utilisateur</label>
                <input type="text" id="nom" placeholder="Votre nom d'utilisateur" required>
            </div>
            
            <div class="form-group">
                <label for="mdp">Mot de passe</label>
                <input type="password" id="mdp" placeholder="Votre mot de passe" required>
            </div>
            
            <button type="submit" id="loginBtn" class="login-btn">Se connecter</button>
            
            <div id="message" class="message"></div>
        </form>
        
        <div class="security-info">
            <p>Votre connexion est sécurisée par un chiffrement SSL 256-bit</p>
            <div class="security-badge">Connexion sécurisée</div>
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
            loginBtn.classList.add('loading');

            const data = `nom=${encodeURIComponent(nom)}&mdp=${encodeURIComponent(mdp)}`;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", apiBase + "/login", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    loginBtn.disabled = false;
                    loginBtn.classList.remove('loading');

                    try {
                        const response = JSON.parse(xhr.responseText);

                        if (xhr.status === 200 && response.success) {
                            showMessage("Connexion réussie ! Redirection...", "success");

                            setTimeout(() => {
                                window.location.href = apiBase + "/template";
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
                loginBtn.classList.remove('loading');
                showMessage("Erreur de connexion réseau", "error");
            };

            xhr.send(data);
        }

        function showMessage(text, type) {
            const messageDiv = document.getElementById("message");
            messageDiv.innerHTML = `<div class="alert alert-${type}" role="alert">${text}</div>`;
            messageDiv.textContent = text;
            messageDiv.className = `message ${type}`;
            
            // Forcer le reflow pour l'animation
            messageDiv.offsetHeight;
            messageDiv.classList.add('show');
            
            // Masquer le message après 5 secondes si c'est une erreur
            if (type === 'error') {
                setTimeout(() => {
                    messageDiv.classList.remove('show');
                }, 5000);
            }
        }

        // Animation subtile des inputs
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });
    </script>

</body>

</html>