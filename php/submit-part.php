<?php

$app->post('/add/submit', function() use ($app) {
    // Set Database credentials
    if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }
    require $path . 'db-conn.php';

    $data = json_decode($_POST['data']);

    var_dump($data);
    
    $partNum = $data->part_num;
    /*
    $sql = "SELECT COUNT(*) FROM `parts` WHERE PART_NUM='" . $partNum . "'";
    $result = $CONN->query($sql);

    if (!$result) {
        printf("Error: %s\n", mysqli_error($CONN));
        return;
    }
    
    $row = mysqli_fetch_array($result);

    var_dump($row);
    
    $count = $row['COUNT(*)']
    */
    if (!($stmt = $mysqli->prepare("SELECT COUNT(*) FROM `parts` WHERE part_num=?"))) {
        echo "Select failed: (" . $mysqli->errno . ") " . $mysqli->error;
        $app->response->setStatus(500);
        return;
    }
    echo "prepared\n";
    if (!$stmt->bind_param("s", $partNum)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        $app->response->setStatus(500);
        return;
    }
    echo "bound\n";
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
        $app->response->setStatus(500);
        return;
    }
    echo "executed\n";
    if (!($result = $stmt->get_result())) {
        echo "Getting result set failed: (" . $stmt->errno . ") " . $stmt->error;
        $app->response->setStatus(500);
        return;
    }
    echo "fetched\n";
    var_dump($result->fetch_all());
    
    $stmt->close();
    
$count = 0;

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