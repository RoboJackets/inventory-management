<?php

function initDbConn(){
    if (!isset($path)) { $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }                 
    if (!defined('HOST')) { require $path . 'config.php'; }   // make sure constants are defined
    
    $dsn = 'mysql:host='. HOST . ';dbname=' . DATABASE . ';';
    $db = new PDO($dsn, USER, PASSWORD);
    
    return $db;
}