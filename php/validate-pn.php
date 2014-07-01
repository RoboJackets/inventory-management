<?php

// Set Database credentials
if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }
if (file_exists($path . 'db-conn.php')) { require_once $path . 'db-conn.php'; }

$partNum = $_POST['partNumber'];

$sql = "SELECT COUNT(*) FROM `parts` WHERE PART_NUM='" . $partNum . "'";

$result = $CONN->query($sql);

if (!$result) {
    printf("Error: %s\n", mysqli_error($CONN));
    exit();
}

$row = mysqli_fetch_array($result);

echo json_encode($row['COUNT(*)'] != 0);