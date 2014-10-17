<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 10/14/2014
 * Time: 11:29 PM
 */

$app->get('/info', function () use ($app) {
    $app->view();
    $app->render('server_info.php', array(
        'title' => 'Server Information',
        'tab' => 'none'
    ));
});

$app->get('/view-log', function (){
   $app->view();
    echo file_get_contents('/logs/part-log.txt');
});