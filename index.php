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
        'mode' => 'barcode',
        'tab' => 'default'
    ));
});

$app->get('/add', function() use ($app) {
    $app->view();
    $app->render('add.php', array(
        'title' => 'Add a Component',
        'tab' => 'add'
    ));
});

include 'php/server_route.php';

$app->get('/:mode', function($mode) use ($app) {
    $app->view();
    $app->render('html.php', array(
        'title' => 'RoboJackets Inventory',
        'mode' => $mode,
        'tab' => 'default'
    ));
});

require 'php/validate-pn.php';
require 'php/send-part.php';


$app->run();