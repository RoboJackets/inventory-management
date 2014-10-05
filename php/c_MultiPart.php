<?php

/**
 * Description of the MultiPart Class
 *
 * @author Jonathan Jones
 */

if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }
require $path . 'c_Part.php';
require $path . 'c_Database.php';

class MultiPart
{
    // property declaration
    private $connection;
    public $parts;
    public $user_input;
    public $num_results;
    
    
    public function __construct(Database $db, $input)
    {
        $this->parts = array();
        $this->user_input = $input;
        $this->connection = $db;
    }
    
    // Performs database searches and attempts to locate all part
    // information that is in a specific location.
    public function findBin()
    {
        // Get an array of part_id numbers that all relate to the user-given location
        $part_ids = $this->connection->searchQuery("SELECT part_id FROM parts WHERE location=(?)", $this->user_input);
        foreach ($part_ids as $index => $part)
        {
            $temp_part = New Part($this->connection, $part['part_id']);
            
            $temp_part->findPartbyID();
            
            $this->parts[] = $temp_part;
        }
        
        $this->num_results = count($this->parts);
    }
    

    public function sendBin()
    {
        foreach($this->parts as $index => $vals)
        {
            $vals->sendPart();
        }
    }


    // Sends results to client as JSON encoded data.
    public function sendBinJSON()
    {
        echo json_encode($this);
    }


}   // end of Bin Class
?>