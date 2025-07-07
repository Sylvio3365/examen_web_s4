<?php

namespace app\controllers;

use app\models\ProductModel;
use Flight;

class WelcomeController {

	public function __construct() {

	}

    //pour tester le template
    public function homeTemplate() {
        $data = ['page' => "home"];
        Flight::render('welcome', $data);
    }

}