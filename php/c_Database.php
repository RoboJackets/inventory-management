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
require $path . 'config.php';


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
        $this->connection = New mysqli(HOST, USER, PASSWORD, DATABASE);
        //$this->connection = New mysqli(HOST, USER, NULL, DATABASE);


        if (!$this->connection) {
            echo "Could not create connection to the database.\n";
        }

        // Check for errors
        if ($this->connection->connect_error) {
            echo "Database connection failed: " . "<b>" . $this->connection->connect_error . "</b> \n";
            exit();
        }

    }   // function connect


    public function closeConnection()
    {
        mysqli_close($this->connection);
    }


    public function searchQuery($sql, $user_input)
    {
        // cast input to a string for consistency
        $input = (string)$user_input;

        if (!$this->query = $this->connection->prepare($sql)) {
            echo "Error: Could not prepare query statement. (" . $this->connection->errno . ") " . $this->connection->error . "\n";
        }
        if (!$this->query->bind_param("s", $input)) {
            echo "Error: Failed to bind parameters to statement. (" . $this->connection->errno . ") " . $this->connection->error . "\n";
        }
        if (!$this->query->execute()) {
            echo "Error: Failed to execute query. (" . $this->connection->errno . ") " . $this->connection->error . "\n";
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


    public function addBags($partID, $bag_input)
    {
        if ($stmt = $this->connection->prepare("INSERT INTO barcode_lookup (part_id, barcode, quantity) VALUES (?,?,?);")) {
            foreach ($bag_input as $index => $bag) {
                $stmt->bind_param('sss', $partID, $bag->barcode, $bag->quantity);
                if (!$stmt->execute()) {
                    echo "<b>Error: Failed to execute query.</b> (" . $stmt->errno . ") " . $stmt->error . "\n";
                }
            }
            $stmt->close();
        }

    }

    public function addAttributes($partID, $attrib_input)
    {
        if ($stmt = $this->connection->prepare("INSERT INTO attributes (part_id, attribute, value, priority) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE value=VALUES(value), priority=VALUES(priority);")) {
            foreach ($attrib_input as $index => $attribute) {
                $stmt->bind_param('ssss', $partID, $attribute->attribute, $attribute->value, $attribute->priority);
                if (!$stmt->execute()) {
                    echo "<b>Error: Failed to execute query.</b> (" . $stmt->errno . ") " . $stmt->error . "\n";
                }
            }
            $stmt->close();
        }
    }

}   // end of Database Class
