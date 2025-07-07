<?php
require_once __DIR__ . '/../controllers/EtudiantController.php';
require_once __DIR__ . '/../controllers/TypePretController.php';

Flight::route('GET /typeprets', ['TypePretController', 'getAll']);
Flight::route('GET /typeprets/@id', ['TypePretController', 'getById']);
Flight::route('POST /typeprets', ['TypePretController', 'create']);
Flight::route('PUT /typeprets/@id', ['TypePretController', 'update']);
Flight::route('DELETE /typeprets/@id', ['TypePretController', 'delete']);
Flight::route('GET /', ['TypePretController', 'goIndex']);
