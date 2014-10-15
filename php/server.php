<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 10/14/2014
 * Time: 11:03 PM
 */

    // Set Database credentials
    if (!isset($path)) {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/php/';
    }
    require $path . 'c_Database.php';
    $db = New Database();

    echo '<dt>Server Version: </dt><dd>' . $db->serverInfo() . '</dd>';
    echo '<dt>Host Info: </dt><dd>' . $db->hostInfo() . '</dd>';
    echo '<dt>Protocol Version: </dt><dd>' . $db->protocolVersion() . '</dd>';
    echo '<dt>Client Info: </dt><dd>' . $db->clientInfo() . '</dd>';
    echo '<dt>Client Version: </dt><dd>' . $db->clientVersion() . '</dd>';
    echo '<dt>Thread ID: </dt><dd>' . $db->threadID() . '</dd>';
