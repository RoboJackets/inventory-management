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
    if ($stmt = $CONN->prepare("SELECT COUNT(*) FROM `parts` WHERE part_num=")) {
        echo "Running Query";
        /* bind parameters for markers */
        $stmt->bind_param("s", $partNum);

        /* execute query */
        $stmt->execute();

        /* bind result variables */
        $stmt->bind_result($count);

        /* fetch value */
        $stmt->fetch();

        /* close statement */
        $stmt->close();
    } else {
        echo "Query Failed";
        echo "Prepare failed: (" . $CONN->errno . ") " . $CONN->error;
    }
    
    var_dump($count);

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