<?php
require_once __DIR__ . '/../models/Login.php';
require_once __DIR__ . '/../helpers/Utils.php';

class LoginController
{
    public static function formLogin()
    {
        Flight::render('login/login');
    }

    public static function loginPost()
    {
        // Vérifier la méthode HTTP
        if (Flight::request()->method !== 'POST') {
            Flight::json(['success' => false, 'error' => 'Méthode non autorisée'], 405);
            return;
        }

        $data = Flight::request()->data;

        $username = trim($data['nom'] ?? '');
        $password = trim($data['mdp'] ?? '');

        // Validation des champs
        if (empty($username) || empty($password)) {
            Flight::json(['success' => false, 'error' => 'Nom d\'utilisateur et mot de passe requis'], 400);
            return;
        }

        try {
            $user = Login::login($username, $password);

            if ($user) {
                // Démarrer une session
                session_start();
                $_SESSION['user_id'] = $user['id'] ?? null;
                $_SESSION['username'] = $user['nom'];
                $_SESSION['logged_in'] = true;
                
                Flight::json([
                    'success' => true, 
                    'message' => 'Connexion réussie', 
                    'user' => $user['nom']
                ]);
            } else {
                Flight::json(['success' => false, 'error' => 'Nom d\'utilisateur ou mot de passe incorrect'], 401);
            }
        } catch (Exception $e) {
            error_log("Erreur de connexion: " . $e->getMessage());
            Flight::json(['success' => false, 'error' => 'Erreur serveur'], 500);
        }
    }
}
