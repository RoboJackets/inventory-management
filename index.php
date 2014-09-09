<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader(); //Req'd since not using Composer

$app = new \Slim\Slim();

$app->get('/', function() use ($app) {
    $app->view();
    $app->render('html.php', array('title'=>'RoboJackets Inventory',
                                   'mode'=>'barcode',
                                   'partLocation'=>null,
                                   'partName'=>null,
                                   'partNum'=>null
                                  ));
});


$app->get('/add', function() use ($app) {
    $app->view();
    $app->render('add.html');
});

require 'php/validate-pn.php'; //Test of new code layout system

$app->get('/:mode', function($mode) use ($app) {
    $app->view();
    $app->render('html.php', array('title'=>'RoboJackets Inventory',
                                   'mode'=>$mode,
                                   'partLocation'=>null,
                                   'partName'=>null,
                                   'partNum'=>null
                                  ));
});


require 'php/submit-part.php';

$app->run();
