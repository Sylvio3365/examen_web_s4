<?php
require 'vendor/autoload.php';
require 'db.php';
require_once __DIR__ . '/services/EtudiantService.php';

$service = new EtudiantService();

Flight::route('GET /etudiants', [$service, 'getAll']);
Flight::route('GET /etudiants/@id', [$service, 'get']);
Flight::route('POST /etudiants', [$service, 'create']);
Flight::route('PUT /etudiants/@id', [$service, 'update']);
Flight::route('DELETE /etudiants/@id', [$service, 'delete']);

Flight::start();
