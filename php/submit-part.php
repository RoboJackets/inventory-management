<?php

$app->post('/add/submit', function() use ($app) {
    // Set Database credentials
    if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }
    require $path . 'db-conn.php';

    $data = json_decode($_POST['data']);
    
    var_dump($data);
    
    $part = $data->parts[0];
    //add code here that check to ensure only 1 part was sent / use a for each structure

    if ($stmt = $CONN->prepare("SELECT COUNT(*) FROM `parts` WHERE part_num=?")) {
        $stmt->bind_param("s", $part->part_num);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Prepare failed: (" . $CONN->errno . ") " . $CONN->error . "<br>";
        $app->response->setStatus(500);
        return;
    }

    if ($count == 0){ // If part isn't already in DB
        if ($stmt = $CONN->prepare("INSERT INTO parts (part_num, name, category, description, datasheet, location)
            VALUES (?,?,?,?,?,?)")){
            $stmt->bind_param("ssssss", $part->part_num, $part->name, $part->category, $part->description, $part->datasheet, $part->location);
            $stmt->execute();
            $stmt->fetch();
            $stmt->close();
        } else {
            echo "Prepare failed: (" . $CONN->errno . ") " . $CONN->error . "<br>";
            $app->response->setStatus(500);
            return;
        }
    } else {
        //Error out if part already exists
        printf("Error: Part %s already exists:", $part->part_num);
        $app->response->setStatus(409);
        return;
    }  
    
});