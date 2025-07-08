<?php
require_once __DIR__ . '/../controllers/EtudiantController.php';
require_once __DIR__ . '/../controllers/TypePretController.php';
require_once __DIR__ . '/../controllers/PretController.php';
require_once __DIR__ . '/../controllers/FondController.php';
require_once __DIR__ . '/../controllers/ClientController.php';
require_once __DIR__ . '/../controllers/RemboursementController.php';
require_once __DIR__ . '/../controllers/PretStatutController.php';
require_once __DIR__ . '/../controllers/SortantController.php';
require_once __DIR__ . '/../controllers/TemplateController.php';
require_once __DIR__ . '/../controllers/LoginController.php';
require_once __DIR__ . '/../controllers/MontantController.php';

Flight::route('GET /typeprets', ['TypePretController', 'getAll']);
Flight::route('GET /typeprets/@id', ['TypePretController', 'getById']);
Flight::route('POST /typeprets', ['TypePretController', 'create']);
Flight::route('PUT /typeprets/@id', ['TypePretController', 'update']);
Flight::route('DELETE /typeprets/@id', ['TypePretController', 'delete']);
Flight::route('GET /typepret', ['TypePretController', 'goIndex']);

Flight::route('GET /pret', ['PretController', 'goIndex']);
Flight::route('GET /formFond', ['FondController', 'formulaireFond']);
Flight::route('POST /ajouterFond', ['FondController', 'insertFond']);
Flight::route('GET /capital', ['FondController', 'getCapitalActuel']);

Flight::route('GET /interets', ['PretController', 'goInteret']);
Flight::route('POST /api/interets', ['PretController', 'interets']);

Flight::route('GET /pret/generate-pdf/@id', ['PretController', 'generatePdf']);
Flight::route('GET /pret/@id/pdf', ['PretController', 'generatePdf']);
Flight::route('GET /api/pret/@id/pdf', ['PretController', 'generatePdf']);
Flight::route('POST /api/interets', ['PretController', 'interets']);

Flight::route('GET /clients_pret', ['ClientController', 'listeAvecPrets']);
Flight::route('GET /api/clients/avec-prets', ['ClientController', 'getClientsAvecPretsJson']);

Flight::route('POST /prets/add', ['PretController', 'addPret']);

Flight::route('GET /clients', ['ClientController', 'getAll']);

Flight::route('GET /teste', ['PretController', 'addRemboursement']);

Flight::route('GET /remboursements/attente', ['RemboursementController', 'getEnAttente']);
Flight::route('GET /remboursements_attente', ['RemboursementController', 'liste']);
Flight::route('POST /remboursements/@id/statut', ['RemboursementController', 'setStatus']);
Flight::route('GET /pendingPret', ['PretController', 'pendingPret']);
Flight::route('GET /pendingPretPage', ['PretController', 'goPendingPage']);
Flight::route('POST /validerPret', ['PretController', 'validerPret']);
Flight::route('POST /annulerPret', ['PretController', 'annulerPret']);

Flight::route('GET /template', ['TemplateController', 'template']);
Flight::route('GET /', ['LoginController', 'formLogin']);
Flight::route('POST /login', ['LoginController', 'loginPost']);

Flight::route('GET /comparaison', ['PretController', 'goComparaisonPage']);
Flight::route('GET /api/prets/valides', ['PretController', 'getValidatedPrets']);
Flight::route('POST /api/prets/comparer', ['PretController', 'comparerPrets']);
Flight::route('POST /api/prets/comparaison/pdf', ['PretController', 'generateComparisonPdf']);
Flight::route('GET /montant', ['MontantController', 'getMontantParPeriode']);

Flight::route('GET /montanttotal', ['MontantController', 'montanttotal']);
