<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 10/6/2014
 * Time: 5:20 PM
 */

if (!isset($path)) {
    $path = $_SERVER['DOCUMENT_ROOT'] . '/php/';
}
require $path . 'c_Database.php';
require $path . 'c_Part.php';

class MultiPart
{
    // property declaration
    public $parts;
    public $user_input;    // array used for holding the part_id numbers of the found components (so multiple results of the same part are not returned)
    public $num_results;
    private $connection;


    public function __construct(Database $db, $input)
    {
        $this->parts = array();
        $this->user_input = $input;
        $this->connection = $db;
    }   // end of Constructor function


    /*
     *  Performs database searches and attempts to locate all part information that is in a specific location.
     */
    public function findBin()
    {
        // Get an array of part_id numbers that all relate to the user-given location
        $part_ids = $this->connection->searchQuery("SELECT part_id FROM parts WHERE location=(?)", $this->user_input);

        // Loop through all the part_id numbers found for the given location
        foreach ($part_ids as $index => $part) {

            $temp_part = New Part($this->connection, array('part_id' => $part['part_id']));

            $this->parts[] = $temp_part;
        }

        $this->num_results = count($this->parts);

    }   // end of findBin


    /*
     *  This function will send the HTML content of the bin contents
     */
    public function sendBin()
    {
        foreach ($this->parts as $index => $vals) {
            $vals->sendPart();
        }
    }   // end of sendBin


}   // end of Bin Class