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
require $path . 'c_Log.php';


class Database
{
    private $connection;
    private $dbresults;
    private $query;
    private $log;

    // Constructor function to initialize the database connection
    public function __construct()
    {
        $this->log = New LogFile();
        $this->log->setFile('database-errors.txt');
        $this->createConnection();
    }   // end of Constructor function

    // Create a connection to the database
    private function createConnection()
    {
        try {
            $this->connection = New mysqli(HOST, USER, PASSWORD, DATABASE);
        } catch (Exception $e) {
            echo "<b>ERROR:</b> Unable to create a connection to the database. " . USER . "@" . DATABASE . "</br>";
            $log_error = 'Could not create connection to the database ' . DATABASE . ' from ' . USER . '@' . HOST;
            $this->log->writeLog($log_error);
            exit();
        }
    }   // end of createConnection


    public function closeConnection()
    {
        $this->connection->close();
    }   // end of closeConnection


    public function startInput()
    {
        $this->connection->autocommit(false);
        //$this->connection->begin_transaction();
    }   // end of startInput


    public function endInput()
    {
        $this->connection->commit();
    }   // end of endInput

    public function rollBack() {
        $this->connection->rollback();
    }   // end of rollBack

    public function serverInfo() {
        return $this->connection->server_info;
    }   // end of serverInfo

    public function hostInfo() {
        return $this->connection->host_info;
    }   // end of hostInfo

    public function protocolVersion() {
        return $this->connection->protocol_version;
    }   // end of protocolVersion

    public function clientInfo() {
        return $this->connection->client_info;
    }   // end of clientInfo

    public function clientVersion() {
        return $this->connection->client_version;
    }   // end of ClientVersion

    public function threadID() {
        return $this->connection->thread_id;
    }   // end of threadID

    public function affectedRows() {
        return $this->connection->affected_rows;
    }

    public function searchQuery($sql, $user_input)
    {
        // cast input to a string for consistency
        $input = (string)$user_input;

        if (!$this->query = $this->connection->prepare($sql)) {
            echo "<b>ERROR: Could not prepare query statement:</b> (" . $this->connection->errno . ") " . $this->connection->error . "</br>";
            $log_error = 'Query statement prepare failure: (' . $this->connection->errno . ') ' . $this->connection->error;
            $this->log->writeLog($log_error);
        }
        if (!$this->query->bind_param("s", $input)) {
            echo "<b>ERROR: Failed to bind parameters to statement.</b> (" . $this->connection->errno . ") " . $this->connection->error . "</br>";
            $log_error = 'Query bind parameter failure: (' . $this->connection->errno . ') ' . $this->connection->error;
            $this->log->writeLog($log_error);
        }
        if (!$this->query->execute()) {
            echo "<b>ERROR: Failed to execute query.</b> (" . $this->connection->errno . ") " . $this->connection->error . "</br>";
            $log_error = 'Query execution failure: (' . $this->connection->errno . ') ' . $this->connection->error;
            $this->log->writeLog($log_error);
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


    public function addPart($part_input)
    {
        $rows = array('added' => 0, 'modified' => 0);
        if ($stmt = $this->connection->prepare("INSERT INTO parts (part_num, name, category, description, datasheet, location) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), description=VALUES(description), datasheet=VALUES(datasheet), location=VALUES(location);")) {
            $stmt->bind_param("ssssss", $part_input->part_num, $part_input->name, $part_input->category, $part_input->description, $part_input->datasheet, $part_input->location);
            if (!$stmt->execute()) {
                echo "<b>ERROR: Failed to execute query in <i>addPart</i> method:</b> (" . $this->connection->errno . ") " . $this->connection->error . "</br>";
                $log_error = 'Query bind parameters failure [addPart()]: (' . $this->connection->errno . ') ' . $this->connection->error;
                $this->log->writeLog($log_error);
            }
            $temp = $stmt->affected_rows;
            if ($temp == 2) {
                $rows['modified'] += 1;
            } else if ($temp == 1) {
                $rows['added'] += 1;
            }
            $stmt->close();
        } else {
            echo "<b>ERROR: Prepare failed in <i>addPart</i> method:</b> (" . $this->connection->errno . ") " . $this->connection->error . "<br>";
            $log_error = 'Query statement prepare failure [addPart()]: (' . $this->connection->errno . ') ' . $this->connection->error;
            $this->log->writeLog($log_error);
        }

        // return the part's id number and any errors (no errors will return 00000)
        return array('part_id' => $this->connection->insert_id, 'rows_modified' => $rows['modified'], 'rows_added' => $rows['added'], 'sqlstate' => (int)$this->connection->sqlstate);
    }   // end of addPart


    public function addBags($partID, $bag_input)
    {
        $rows = array('added' => 0, 'modified' => 0);
        if ($stmt = $this->connection->prepare("INSERT INTO barcode_lookup (part_id, barcode, quantity) VALUES (?,?,?);")) {
            foreach ($bag_input as $index => $bag) {
                $stmt->bind_param('sss', $partID, $bag->barcode, $bag->quantity);
                if (!$stmt->execute()) {
                    echo "<b>ERROR: Failed to execute query in <i>addBags</i> method:</b> (" . $this->connection->errno . ") " . $this->connection->error . "</br>";
                    $log_error = 'Query bind parameters failure [addBags()]: (' . $this->connection->errno . ') ' . $this->connection->error;
                    $this->log->writeLog($log_error);
                }
                $temp = $stmt->affected_rows;
                if ($temp == 2) {
                    $rows['modified'] += 1;
                } else if ($temp == 1) {
                    $rows['added'] += 1;
                }
            }
            $stmt->close();
        } else {
            echo "<b>ERROR: Prepare failed in <i>addBags</i> method:</b> (" . $this->connection->errno . ") " . $this->connection->error . "<br>";
            $log_error = 'Query statement prepare failure [addBags()]: (' . $this->connection->errno . ') ' . $this->connection->error;
            $this->log->writeLog($log_error);
        }

        return array('status' => (int)$this->connection->sqlstate, 'rows_added' => $rows['added'], 'rows_modified' => $rows['modified']);
    }   // end of addBags


    public function addAttributes($partID, $attrib_input)
    {
        $rows = array('added' => 0, 'modified' => 0);
        if ($stmt = $this->connection->prepare("INSERT INTO attributes (part_id, attribute, value, priority) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE value=VALUES(value), priority=VALUES(priority);")) {
            foreach ($attrib_input as $index => $attribute) {
                $stmt->bind_param('ssss', $partID, $attribute->attribute, $attribute->value, $attribute->priority);
                if (!$stmt->execute()) {
                    echo "<b>ERROR: Failed to execute query in <i>addAttributes</i> method:</b> (" . $this->connection->errno . ") " . $this->connection->error . "</br>";
                    $log_error = 'Query bind parameters failure [addAttributes()]: (' . $this->connection->errno . ') ' . $this->connection->error;
                    $this->log->writeLog($log_error);
                }
                $temp = $stmt->affected_rows;
                if ($temp == 2) {
                    $rows['modified'] += 1;
                } else if ($temp == 1) {
                    $rows['added'] += 1;
                }
            }
            $stmt->close();
        } else {
            echo "<b>ERROR: Prepare failed in <i>addAttributes</i> method:</b> (" . $this->connection->errno . ") " . $this->connection->error . "<br>";
            $log_error = 'Query statement prepare failure [addAttributes()]: (' . $this->connection->errno . ') ' . $this->connection->error;
            $this->log->writeLog($log_error);
        }

        return array('status' => (int)$this->connection->sqlstate, 'rows_added' => $rows['added'], 'rows_modified' => $rows['modified']);
    }   // end of addAttributes

}   // end of Database Class
