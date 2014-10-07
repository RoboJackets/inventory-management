<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 10/6/2014
 * Time: 5:20 PM
 */

// Set Database credentials
if (!isset($path)) {
    $path = $_SERVER['DOCUMENT_ROOT'] . '/php/';
}
if (!defined('HOST')) {
    require $path . 'config.php';
}


class Database
{

    private $connection;
    private $dbresults;
    private $query;

    // Constructor function to initialize the database connection
    public function __construct()
    {
        $this->createConnection();
    }

    // Create a connection to the database
    private function createConnection()
    {
        //$this->connection = New mysqli(HOST, USER, PASSWORD, DATABASE);
        $this->connection = New mysqli(HOST, USER, NULL, DATABASE);

        // Check for errors
        if ($this->connection->connect_error) {
            echo "Database connection failed: " . $this->connection->connect_error, E_USER_ERROR . "\n";
            exit();
        }

    }   // function connect


    public function closeConnection()
    {
        mysql_close($this->connection);
    }


    public function searchQuery($sql, $user_input)
    {
        // cast input to a string for consistency
        $input = (string)$user_input;

        if (!$this->query = $this->connection->prepare($sql)) {
            echo "Error: Could not prepare query statement. (" . $this->query->errno . ") " . $this->query->error . "\n";
        }
        if (!$this->query->bind_param("s", $input)) {
            echo "Error: Failed to bind parameters to statement. (" . $this->query->errno . ") " . $this->query->error . "\n";
        }
        if (!$this->query->execute()) {
            echo "Error: Failed to execute query. (" . $this->query->errno . ") " . $this->query->error . "\n";
        }

        $this->sortQuery();
        return $this->dbresults;

    }   // function searchQuery


    // filters a queries results
    private function sortQuery()
    {
        $meta = $this->query->result_metadata();  // get the metadata from the results

        // store the field heading names into an array, pass by reference
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }

        // callback function; same as: $query->bind_result($params)
        call_user_func_array(array($this->query, 'bind_result'), $params);

        $results = array();
        while ($this->query->fetch()) {   // fetch the results for every field

            $temp = array();

            foreach ($row as $key => $val) { // itterate through all fields
                $temp[$key] = $val;
            }

            // add results to the array
            $results[] = $temp;
        }

        // close the open database/query information
        $meta->close();
        $this->query->close();

        $this->dbresults = $results;
    }   // function filterSingle


    public function addBags($partID, $bag_input)    {
        if ($stmt = $this->connection->prepare("INSERT INTO barcode_lookup (part_id, barcode, quantity) VALUES (?,?,?);")) {
            foreach ($bag_input->bags as $bag) {
                $stmt->bind_param('sss', $partID, $bag->barcode, $bag->quantity);
                if (!$stmt->execute()) {
                    echo "Error: Failed to execute query. (" . $stmt->errno . ") " . $stmt->error . "\n";
                }
            }
            $stmt->close();
        }

    }

    public function addAttributes()
    {

    }

}   // end of Database Class
