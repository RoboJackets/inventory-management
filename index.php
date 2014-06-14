<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader(); //Req'd since not using Composer


$app = new \Slim\Slim();


$app->get('/', function() use ($app) {
    $app->view();
    $app->render('htmlHeader.php', array('title'=>'Robojackets Inventory'));
    $app->render('searchArea.php', array('queryType'=>'Barcode'));
    $app->render('htmlFooter.php');
});



$app->run();
