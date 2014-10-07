<?php

// Set Database credentials
if (!isset($path)) {
    $path = $_SERVER['DOCUMENT_ROOT'] . '/php/';
}
require $path . 'c_Part.php';

$app->post('/php/add-part.php', function () use ($app) {

    $data = json_decode(file_get_contents("php://input"));
    $db = New Database();

    foreach ($data->parts as $part) {
        $entry = New Part($db, array('part', $part));
        $entry->addNewBags();
    }
});