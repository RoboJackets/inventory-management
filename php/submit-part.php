<?php

$app->post('/add/submit', function() use ($app) {
    // Set Database credentials
    if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }
    require $path . 'db-conn.php';

    $data = json_decode(file_get_contents("php://input"));
    var_dump($data);

    
    $part = $data->parts[0];
    //add code here that check to ensure only 1 part was sent / use a foreach structure

    
    
    if ($stmt = $CONN->prepare("SELECT COUNT(*) FROM `parts` WHERE part_num=?")) {
        $stmt->bind_param("s", $part->part_num);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Prepare failed: (" . $stmt->errno . ") " . $stmt->error . "<br>";
        $app->response->setStatus(500);
        return;
    }

    if ($count == 0){ // If part isn't already in DB
        
        $part->location = strtoupper($part->location);
        if($part->description == "") $part->description = null;
        if ($stmt = $CONN->prepare("INSERT INTO parts (part_num, name, category, description, datasheet, location) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), description=VALUES(description), datasheet=VALUES(datasheet), location=VALUES(location);")){
            $stmt->bind_param("ssssss", $part->part_num, $part->name, $part->category, $part->description, $part->datasheet, $part->location);
            if (!$stmt->execute()) {
                echo "Error: Failed to execute query. (" . $stmt->errno . ") " . $stmt->error . "\n";
            }
            $stmt->close();
        } else {
            echo "Prepare failed: (" . $stmt->errno . ") " . $stmt->error . "<br>";
            $app->response->setStatus(500);
            return;
        } 
        $part->part_id = $CONN->insert_id; 
    }
    
    if ($stmt = $CONN->prepare("INSERT INTO barcode_lookup (part_id, barcode, quantity) VALUES (?,?,?);")){
        foreach($part->bags as $bag){
            $stmt->bind_param('sss', $part->part_id, $bag->barcode, $bag->quantity);
            if (!$stmt->execute()) {
                echo "Error: Failed to execute query. (" . $stmt->errno . ") " . $stmt->error . "\n";
            }
        }
        $stmt->close();
    }

    if ($stmt = $CONN->prepare("INSERT INTO attributes (part_id, attribute, value, priority) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE value=VALUES(value), priority=VALUES(priority);")){
        foreach($part->attributes as $attribute){
            $stmt->bind_param('ssss', $part->part_id, $attribute->attribute, $attribute->value, $attribute->priority);
            if (!$stmt->execute()) {
                echo "Error: Failed to execute query. (" . $stmt->errno . ") " . $stmt->error . "\n";
            }
        }
        $stmt->close();
    }
    
});