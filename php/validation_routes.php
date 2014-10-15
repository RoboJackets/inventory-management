<?php
/*
$app->post('/add/validate-pn', function () use ($app) {
    // Set Database credentials
    if (!isset($path)) {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/php/';
    }
    require $path . 'c_Database.php';

    $connection = New Database();
    $count = $connection->searchQuery("SELECT COUNT(*) FROM parts WHERE part_num=(?)", $_POST['partNumber']);
    $count = array_shift($count);
    echo $count['COUNT(*)'];
});
 */

/*
 *  Send a http POST method with a 'partNumber' field and its value. The response will be one of the following:
 *  - The number zero '0', meaning the part number is not in the database
 *  - JSON encoded information about part numbers that were found to be within the database
 */
$app->post('/validate/partNumber', function () {

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

$app->post('/validate/location', function () {
    if (!isset($path)) { // make sure path is known
        $path = $_SERVER['DOCUMENT_ROOT'] . '/php/';
    }
    require $path . 'c_Part.php';
    require $path . 'c_Database.php';

    $data = $_POST['location'];
    $conn = New Database();
    $part = New Part($conn);
    $part->location = $data;
    echo $part->validateLocation();
    $conn->closeConnection();
});

$app->post('/validate/barcode', function () {
    if (!isset($path)) { // make sure path is known
        $path = $_SERVER['DOCUMENT_ROOT'] . '/php/';
    }
    require $path . 'c_Part.php';
    require $path . 'c_Database.php';

    $conn = New Database();
    $part = New Part($conn);
    $container = New Bag($_POST['barcode'], 1);

    $part->new_bags[] = $container;
    echo $part->validateBarcode();
    $conn->closeConnection();
});