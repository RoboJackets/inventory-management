<?php

// Set Database credentials
if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }
if (file_exists($path . 'db-conn.php')) { require_once $path . 'db-conn.php'; }


$sql = 'SELECT COUNT(*) FROM `parts` WHERE PART_NUM=\'1593LGY\'';
$results = $CONN->query($sql);
echo $results;
