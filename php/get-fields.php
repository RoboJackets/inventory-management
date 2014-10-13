<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 10/12/2014
 * Time: 2:44 AM
 */

// begin searching if user input is given
$app->post('/validate/partNumber', function () use ($app) {

    if (!isset($path)) { // make sure path is known
        $path = $_SERVER['DOCUMENT_ROOT'] . '/php/';
    }
    require $path . 'c_Part.php';
    require $path . 'c_Database.php';

    $data = $_POST['partNumber'];
    $conn = New Database();
    $part = New Part($conn, array('part_num' => $data));
    $part->sendJSON();
    $conn->closeConnection();
});