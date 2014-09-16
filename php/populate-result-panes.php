<?php

if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT']; } // make sure path is known
require $path . '/templates/result-pane.html';

// begin searching if user input is given
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
    readfile($path . '/templates/result-pane.html');
}     
?>