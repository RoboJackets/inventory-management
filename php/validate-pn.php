<?php

$app->post('/add/validate-pn', function() use ($app) {
    // Set Database credentials
    if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }
    require $path . 'db-conn.php';

    $partNum = $_POST['partNumber'];

    if ($stmt = $CONN->prepare("SELECT COUNT(*) FROM `parts` WHERE part_num=?")) {
        $stmt->bind_param("s", $partNum);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Prepare failed: (" . $stmt->errno . ") " . $stmt->error . "<br>";
        $app->response->setStatus(500);
        return;
    }

    echo json_encode($count);

});