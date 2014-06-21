<?php

/* 
 * This file contains the functions for making a connection to the database
 * and searching the database for information.
 */

// Include connection file
if(!defined('__ROOT__')){
    define('__ROOT__', dirname(dirname(__FILE__)));
    require_once(__ROOT__.'php/db-conn.php');
}
 

function SearchByBarcode($part_barcode) {
    
}

function SearchByPartNum($part_number) {
    
}

function SearchByBin($bin) {
    
}

function StartSession() {
    
    $session_name = 'sec_session_id';   // Set a custom session name
    $secure = SECURE;   // defined in rj-inv_config.php
    
    // Stop JavaScript from being able to access the session id.
    $httponly = true;
    
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header('Location: ../error.php?err=Could not initiate a safe session (ini_set)');
        exit();
    }
    
    // Get current cookies params.
    $cookieParams = session_get_cookie_params();
    
    // Set the cookie params.
    session_set_cookie_params(
        $cookieParams['lifetime'],
        $cookieParams['path'], 
        $cookieParams['domain'], 
        $secure,
        $httponly
        );
    
    session_name($session_name);    // Sets the session name to the one set above.
    session_start();                // Start the PHP session 
    session_regenerate_id();        // Regenerated the session, delete the old one. 
}


// BELOW IS DIRECT COPY AND PASTE FROM ONLINE - NO CHANGES MADE


/* 
 * This function will check the email and password against the database. 
 * It will return true if there is a match. 
 */
function login($email, $password, $mysqli) {
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT id, username, password, salt 
        FROM members
       WHERE email = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
 
        // get variables from result.
        $stmt->bind_result($user_id, $username, $db_password, $salt);
        $stmt->fetch();
 
        // hash the password with the unique salt.
        $password = hash('sha512', $password . $salt);
        if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
            // from too many login attempts 
 
            if (checkbrute($user_id, $mysqli) == true) {
                // Account is locked 
                // Send an email to user saying their account is locked
                return false;
            } else {
                // Check if the password in the database matches
                // the password the user submitted.
                if ($db_password == $password) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    // XSS protection as we might print this value
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", 
                                                                "", 
                                                                $username);
                    $_SESSION['username'] = $username;
                    $_SESSION['login_string'] = hash('sha512', 
                              $password . $user_browser);
                    // Login successful.
                    return true;
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time)
                                    VALUES ('$user_id', '$now')");
                    return false;
                }
            }
        } else {
            // No user exists.
            return false;
        }
    }
}