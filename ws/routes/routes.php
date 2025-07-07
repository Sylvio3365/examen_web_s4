<?php
require_once __DIR__ . '/../controllers/EtudiantController.php';
require_once __DIR__ . '/../controllers/TypePretController.php';
require_once __DIR__ . '/../controllers/PretController.php';
require_once __DIR__ . '/../controllers/FondController.php';
require_once __DIR__ . '/../controllers/ClientController.php';
require_once __DIR__ . '/../controllers/RemboursementController.php';

Flight::route('GET /typeprets', ['TypePretController', 'getAll']);
Flight::route('GET /typeprets/@id', ['TypePretController', 'getById']);
Flight::route('POST /typeprets', ['TypePretController', 'create']);
Flight::route('PUT /typeprets/@id', ['TypePretController', 'update']);
Flight::route('DELETE /typeprets/@id', ['TypePretController', 'delete']);
Flight::route('GET /', ['TypePretController', 'goIndex']);

Flight::route('GET /pret', ['PretController', 'goIndex']);
Flight::route('GET /interets', ['PretController', 'interets']);
Flight::route('GET /formFond', ['FondController', 'formulaireFond']);
Flight::route('POST /ajouterFond', ['FondController', 'insertFond']);
Flight::route('GET /capital', ['FondController', 'getCapitalActuel']);

Flight::route('GET /interets', ['PretController', 'goInteret']);
Flight::route('POST /api/interets', ['PretController', 'interets']);

Flight::route('POST /prets/add', ['PretController', 'addPret']);

Flight::route('GET /clients', ['ClientController', 'getAll']);

Flight::route('GET /teste', ['PretController', 'addRemboursement']);

Flight::route('GET /remboursements/attente', ['RemboursementController', 'getEnAttente']);
Flight::route('GET /remboursements/attente/liste', ['RemboursementController', 'liste']);
Flight::route('POST /remboursements/@id/statut', ['RemboursementController', 'setStatus']);
