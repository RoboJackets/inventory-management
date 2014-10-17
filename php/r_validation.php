<?php
/*
 *  Send a http POST method with a 'partNumber' field and its value. The response will be one of the following:
 *  - The number zero '0', meaning the part number is not in the database
 *  - JSON encoded information about part numbers that were found to be within the database
 */
$app->post('/validate/partNumber', function () {

    require 'c_Part.php';
    require 'c_Database.php';

    $data = $_POST['partNumber'];
    $conn = New Database();
    $part = New Part($conn, array('part_num' => $data));
    $part->sendJSON();
    $conn->closeConnection();
});

$app->post('/validate/location', function () {

    require 'c_Part.php';
    require 'c_Database.php';

    $data = $_POST['location'];
    $conn = New Database();
    $part = New Part($conn);
    $part->location = $data;
    echo $part->validateLocation();
    $conn->closeConnection();
});

$app->post('/validate/barcode', function () {

    require 'c_Part.php';
    require 'c_Database.php';

    $conn = New Database();
    $part = New Part($conn);
    $container = New Bag($_POST['barcode'], 1);

    $part->new_bags[] = $container;
    echo $part->validateBarcode();
    $conn->closeConnection();
});

$app->post('/validate/datasheet', function () {

    require 'c_Part.php';
    require 'c_Database.php';

    $conn = New Database();
    $part = New Part($conn);
    $link = $_POST['datasheet'];

    $part->datasheet = $link;
    echo $part->validateDatasheet();
    $conn->closeConnection();
});