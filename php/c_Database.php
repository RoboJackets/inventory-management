<?php

/**
 * Description of Database Class
 *
 * @author Jonathan
 */

// Set Database credentials
if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }

// this is where the default query rights are found
if (!defined('HOST')) { require $path . 'config.php'; }
//require $path . 'db-conn.php';

class Database {
    
    private $connection;
    
    
    // Constructor function to initialize the database connection
    public function __construct()
    {
        createConnection();
    }
    
    
    // Create a connection to the database
    private function createConnection()
    {
        $this->connection = new mysqli(HOST, USER, PASSWORD, DATABASE);    
        
        // Check for errors
        if ($this->connection->connect_error) {
            echo "Database connection failed: " . $this->connection->connect_error, E_USER_ERROR . "\n";
            exit();
        }
    }   // function connect

    
    public function searchQuery($sql, $user_input)
    {        
        $input = (string)$user_input;

        if(!$query = $CONN->prepare($sql)){
            echo "Error: Could not prepare query statement. (" . $query->errno . ") " . $query->error . "\n";
        }
        if (!$query->bind_param("s", $input)) {
            echo "Error: Failed to bind parameters to statement. (" . $query->errno . ") " . $query->error . "\n";
        }
        if (!$query->execute()) {
            echo "Error: Failed to execute query. (" . $query->errno . ") " . $query->error . "\n";
        }
        
        return $query;   // return the results after formatting to an arry of php objects
    }   // function searchQuery
    
    
    
    // filters a queries results
    public function filterSingle($query, $field_name)
    {
        $meta = $query->result_metadata();  // get the metadata from the results

        // store the field heading names into an array, pass by reference
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }

        // callback function; same as: $query->bind_result($params)
        call_user_func_array(array($query, 'bind_result'), $params);
       
        $results = array();
        while ($query->fetch()) {   // fetch the results for every field
            $results[] = $row[$field_name];
        }

        // close the open database/query information
        $meta->close();
        $query->close();

        return $results;
    }   // function filterSingle
    
    
    
    private function filterMany($query)
    {
        $meta = $query->result_metadata();  // get the metadata from the results

        // store the field heading names into an array, pass by reference
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }

        // callback function; same as: $query->bind_result($params)
        call_user_func_array(array($query, 'bind_result'), $params);

        $results = array();
        
        while ($query->fetch()) {   // fetch the results for every field

            $tmpObj = new stdClass();

            foreach($row as $key => $val) { // itterate through all fields
                $tmpObj->$key = $val; 
            }

            // add row (now as object) to the array of results
            $results[] = $tmpObj;
        }

        // close the open database/query information
        $meta->close();
        $query->close();

        return $results;
    }   // function filterMany
    
    
    
    
}
