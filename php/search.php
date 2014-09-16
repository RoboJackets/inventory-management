<?php
/*
 * Used to perform a search on the database. Returns (echo) json formatted data.
 */

if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; } // make sure path is known
require $path.'db-conn.php';
require $path.'c_Part.php';

/*
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
*/

// begin searching if user input is given
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
    $part = new Part($_GET['input']);
    
    $part->findPartID();
    $part->findBarcodes();
    $part->findAttributes();
    $part->findPartInfo();
    $part->sendPart();
    
    mysqli_close($CONN);
}
?>