<?php
require_once __DIR__ . '/../db.php';

class Login{
    public static function login($username, $password) {
        $db = getDB();
        
        // SÉCURITÉ: Ne jamais stocker les mots de passe en clair !
        // Le mot de passe devrait être hashé avec password_hash()
        $stmt = $db->prepare("SELECT * FROM userEF WHERE nom = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && $password == $user['mdp']) {
            // Ne pas retourner le mot de passe dans la réponse
            unset($user['mdp']);
            return $user;
        } else {
            return null;
        }
    }
    
    // Méthode pour créer un utilisateur avec mot de passe hashé
    public static function createUser($username, $password) {
        $db = getDB();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO userEF (nom, mdp) VALUES (?, ?)");
        return $stmt->execute([$username, $hashedPassword]);
    }
}