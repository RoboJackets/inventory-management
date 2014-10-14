<?php


/*
 *
 */
$app->post('/add/submit', function () use ($app) {

// Set Database credentials
    if (!isset($path)) {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/php/';
    }
    require $path . 'c_Part.php';
    require $path . 'c_Database.php';

    $data = json_decode(file_get_contents('php://input'));
    $db = New Database();

    foreach ($data as $index => $part) {
        foreach ($part as $index2 => $partObj) {
            $entry = New Part($db, array('part' => $partObj));
            $db->startInput();
            //$entry->addPart();
            //$entry->addBags();
            //$entry->addAttributes();
            $entry->storeData();
            $entry->sendStatus();
        }
    }
});