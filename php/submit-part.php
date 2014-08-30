<?php

$app->post('/add/submit', function() use ($app) {
    // Set Database credentials
    if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }
    require $path . 'db-conn.php';

    $partNum = $_POST['data'];

    var_dump($partNum);
    
    $sql = "SELECT COUNT(*) FROM `parts` WHERE PART_NUM='" . $partNum . "'";
    $result = $CONN->query($sql);

    if (!$result) {
        printf("Error: %s\n", mysqli_error($CONN));
        exit();
    }

    $row = mysqli_fetch_array($result);
    
    var_dump($row);

    if ($row['COUNT(*)']){
        echo "Insert New Row\n";
        //Insert new row
        //Insert
    } else {
        //Error out if part already exists
        printf("Error: Part %s already exists", $_POST['partNumber']);
        //exit();
    }  
    
});