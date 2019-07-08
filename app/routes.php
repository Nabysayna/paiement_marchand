<?php

//$app->post('/', App\Controllers\HomeController::class .':authorizationCheck');

/*$app->group('/auth', function () {

	$this->post('/ok', App\Controllers\HomeController::class .':accueil');

});*/

$app->group('/paiement_marchand', function () {

	$this->post('/initierPaie', App\Controllers\paiement_marchandController::class .':initier');

	$this->post('/confirmerPaie', App\Controllers\paiement_marchandController::class .':confirmer');

	

});







