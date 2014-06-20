<?php

/* 
 * This is the PHP codes that is used to connect to the database. Include
 * this file with every page that connects to the database.
 */

include_once "rj-inv_config.php";
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);