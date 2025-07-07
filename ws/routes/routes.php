<?php
require_once __DIR__ . '/../controllers/EtudiantController.php';
require_once __DIR__ . '/../controllers/TypePretController.php';
require_once __DIR__ . '/../controllers/PretController.php';
require_once __DIR__ . '/../controllers/FondController.php';
require_once __DIR__ . '/../controllers/PretStatutController.php';
require_once __DIR__ . '/../controllers/SortantController.php';

Flight::route('GET /typeprets', ['TypePretController', 'getAll']);
Flight::route('GET /typeprets/@id', ['TypePretController', 'getById']);
Flight::route('POST /typeprets', ['TypePretController', 'create']);
Flight::route('PUT /typeprets/@id', ['TypePretController', 'update']);
Flight::route('DELETE /typeprets/@id', ['TypePretController', 'delete']);
Flight::route('GET /', ['TypePretController', 'goIndex']);

Flight::route('GET /pret', ['PretController', 'goIndex']);
Flight::route('GET /interets', ['PretController', 'interets']);
Flight::route('GET /formFond', ['FondController', 'formulaireFond']);
Flight::route('POST /ajouterFond', ['FondController','insertFond']);
Flight::route('GET /capital', ['FondController', 'getCapitalActuel']);
Flight::route('GET /pendingPret', ['PretController', 'pendingPret']);
Flight::route('GET /pendingPretPage', ['PretController', 'goPendingPage']);
Flight::route('POST /validerPret', ['PretController', 'validerPret']);
Flight::route('POST /annulerPret', ['PretController', 'annulerPret']);
