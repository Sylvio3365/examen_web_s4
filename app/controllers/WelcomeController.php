<?php

namespace app\controllers;

use app\models\ProductModel;
use Flight;

class WelcomeController {

	public function __construct() {

	}

    // Affiche l'interface de gestion d'étudiants
    public function homeTemplate() {
        $data = ['page' => "home"];
        // On charge la vue principale qui communique avec le web service
        Flight::render('index', $data);
    }

}