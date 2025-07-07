<?php

use app\controllers\ApiExampleController;
use app\controllers\WelcomeController;
use app\controllers\LogController;
use app\controllers\HomeController;
use app\controllers\AccueilController;
use app\controllers\CrudController;
use app\controllers\TableauController;
use flight\Engine;
use flight\net\Router;
//use Flight;

/** 
 * @var Router $router 
 * @var Engine $app
 */
/*$router->get('/', function() use ($app) {
	$Welcome_Controller = new WelcomeController($app);
	$app->render('welcome', [ 'message' => 'It works!!' ]);
});*/

$Welcome_Controller = new WelcomeController();


$router->get('/', [$Welcome_Controller, 'homeTemplate']);


//$router->get('/', \app\controllers\WelcomeController::class.'->home'); 

$router->get('/hello-world/@name', function ($name) {
	echo '<h1>Hello world! Oh hey ' . $name . '!</h1>';
});

$router->group('/api', function () use ($router, $app) {
	$Api_Example_Controller = new ApiExampleController($app);
	$router->get('/users', [$Api_Example_Controller, 'getUsers']);
	$router->get('/users/@id:[0-9]', [$Api_Example_Controller, 'getUser']);
	$router->post('/users/@id:[0-9]', [$Api_Example_Controller, 'updateUser']);
});