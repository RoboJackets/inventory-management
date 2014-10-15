<?php

/*
 *  This file contains the post method of submitting parts for database submission
 */

$app->post('/submit/part', function () use ($app) {

    require 'c_Part.php';
    require 'c_Database.php';

    $data = json_decode(file_get_contents('php://input'));
    $db = New Database();

    foreach ($data as $index => $part) {
        foreach ($part as $index2 => $partObj) {
            $entry = New Part($db, array('part' => $partObj));

            // Open a new database transaction that is set to NOT autocommit
            $entry->startInput();

            // Add the info
            $entry->addPart();
            $entry->addBags();
            $entry->addAttributes();

            // Commit the changes into the database
            $entry->storeData();

            // Send the status via JSON back to the client
            $entry->sendStatus();
        }
    }
});