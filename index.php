<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader(); //Req'd since not using Composer

$app = new \Slim\Slim(array(
    // Configuration parameters
    'templates.path' => './templates',
    'log.enabled' => true,
    'log.level' => \Slim\Log::INFO
));
    

$app->get('/', function() use ($app) {
    $app->view();
    $app->render('html.php', array(
        'title' => 'RoboJackets Inventory',
        'mode' => 'barcode'
    ));
});


$app->get('/add', function() use ($app) {
    $app->view();
    $app->render('add.php', array(
        'title' => 'Add a Component'
    ));
});


require 'php/validate-pn.php'; //Test of new code layout system


$app->get('/:mode', function($mode) use ($app) {
    $app->view();
    $app->render('html.php', array(
        'title' => 'RoboJackets Inventory',
        'mode' => $mode,
    ));
});


require 'php/submit-part.php';


$app->run();