<?php

$app->post('/add/submit', function() use ($app) {
    // Set Database credentials
    if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }
    require $path . 'db-conn.php';

    $data = json_decode($_POST['data']);

    var_dump($data);
    
    $partNum = $data->part_num;

    if ($stmt = $CONN->prepare("SELECT COUNT(*) FROM `parts` WHERE part_num=?")) {

        $stmt->bind_param("s", $partNum);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Prepare failed: (" . $CONN->errno . ") " . $CONN->error . "<br>";
        $app->response->setStatus(500);
        return;
    }

    if ($count == 0){
        echo "Insert New Row\n";
        //Insert new row
        //Insert
    } else {
        //Error out if part already exists
        printf("Error: Part %s already exists:", $partNum);
        $app->response->setStatus(409);
        return;
    }  
    
});