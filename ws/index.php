<?php
require 'vendor/autoload.php';
require 'db.php';

// Partie 1 : IP ou domaine
$host = 'http://localhost';

// Partie 2 : nom du projet (racine du dossier)
$project = '/examen_web_s4/ws';

// Combinaison des deux
$baseUrl = $host . $project;

// Définir la base URL dans Flight
Flight::set('base_url', $baseUrl);

// Charger les routes
require 'routes/routes.php';

Flight::start();
