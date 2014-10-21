<?php

/*
 *  This file contains the post method of submitting parts for database submission
 */

require 'r_basic_auth.php';

$app->add(new HttpBasicAuthCustom('robojackets', 'robojackets', 'Protected Area', '/submit/part'));

$app->post('/submit/part', function () use ($app) {

    require 'c_Part.php';
    require 'c_Database.php';

    $data = json_decode(file_get_contents('php://input'));
    $conn = New Database();

    foreach ($data as $index => $part) {
        foreach ($part as $index2 => $partObj) {
            $entry = New Part($conn, array('part' => $partObj));

            // Open a new database transaction that is set to NOT autocommit
            $entry->startInput();

            // Add the info
            $entry->addPart();
            $entry->addBags();
            $entry->addAttributes();

            // Commit the changes into the database
            $entry->storeData();

            // The following line can be used for debugging. Uncomment it and a warning is returned in the client browser, thus keeping all input
            // echo json_encode(array('title' => 'DEBUGGIN MODE:', 'message' => 'you are using the debug mode for r_add.js', 'validation_code' => 0x01));

            // Send the status via JSON back to the client
            $entry->sendStatus();
        }
    }
    $conn->closeConnection();
})->name('/submit/part');