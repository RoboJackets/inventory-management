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
require $path . 'c_Bag.php';
require $path . 'c_Attribute.php';


class Part
{

    /*      ======== PUBLIC ========
     *  These are the standard attributes that are found within the database
     */
    public $part_num;
    public $name;
    public $category;
    public $description;
    public $datasheet;
    public $location;

    /*
     *  These are arrays of objects concerning their respective categories
     */
    public $bags;
    public $attributes;

    /*
     *  These are variables are calculated from the found database results
     */
    public $num_bags;
    public $total_qty;
    public $in_db;
    public $error_code;

    /*      ======== PRIVATE ========
     *  These are private variables are used for searching/adding information regarding database
     */
    private $part_id;
    private $barcode;
    private $input;
    public $new_bags;
    public $new_attributes;
    private $commit_code;
    private $send_status;

    private $part_added;
    private $bags_added;
    private $attributes_added;
    private $barcode_can_add;
    private $part_can_add;
    private $part_needs_update;
    private $part_log;
    private $log_statement;
    private $searchmode;

    /*
     *  This private object is the connection to the database (from c_Database.php)
     */
    private $connection;


    /*
     *  This constructor function sets initial object attributes according to what
     *  parameter was passed for the object's declaration. In order for the class methods
     *  to work, one of these paramaters must be given: BARCODE, PART_ID, PART_NUM
     */
    public function __construct(Database $db, array $input = null)
    {
        $this->connection = $db;
        $this->part_log = new LogFile();
        $this->part_log->setFile('part-log.txt');
        $this->commit_code = 0;
        $this->error_code = 0;  // set error code to 0 before beginning any operations

        $this->part_added = false;
        $this->bags_added = false;
        $this->attributes_added = false;
        $this->barcode_can_add = false;
        $this->part_can_add = false;
        $this->part_needs_update = false;

        if (isset($input)) {
            if (isset($input['part']) || isset($input['barcode']) || isset($input['part_id']) || isset($input['part_num'])) {
                if (isset($input['part'])) {
                    $this->searchmode = 'obj';
                    $this->input = $input['part'];
                    $this->new_bags = array();
                    $this->new_attributes = array();
                    $this->filterInput();
                } elseif (isset($input['barcode'])) {
                    $this->searchmode = 'barcode';
                    $this->barcode = $input['barcode'];
                    $this->findbyBarcode();
                } elseif (isset($input['part_id'])) {
                    $this->searchmode = 'part_id';
                    $this->part_id = $input['part_id'];
                    $this->checkID();
                    if (empty($this->in_db)) {
                        $this->in_db = 0;
                    }
                    $this->findbyID();
                } elseif (isset($input['part_num'])) {
                    $this->searchmode = 'part_num';
                    $this->part_num = $input['part_num'];
                    $this->findbyPartNum();
                }
            } else {
                exit("Invalid parameter(s) passed into the part class");
            }
        } else {    // create empty template if no extra paramaters were passed to the class constructor
            $this->bags = array();
            $this->attributes = array();
        }
    }   // end of Constructor function


    /*
    *  The functions listed below are to be used when values are not known at the time of the object's declaration
    *  =======================================================================================================
    *
    *                      SETTING PRIVATE VARIABLES
    *
    *  =======================================================================================================
    */
    public function set_part_id($id)
    {
        $this->part_id = $id;
    }   // end of set_part_id

    public function set_barcode($barcode)
    {
        $this->barcode = $barcode;
    }   // end of set_barcode

    public function set_part_num($number)
    {
        $this->part_num = $number;
    }   // end of set_part_num

    public function set_part_object($part)
    {
        $this->input = $part;
    }   // end of set_part_object


    /*
    *  The functions listed below are user-callable methods that assist in searching for parts in the database
    *  =======================================================================================================
    *
    *                      DATABASE SEARCHING
    *
    *  =======================================================================================================
    */


    /*
     *  This method will find all of the information for any given part when it's PART_ID is known.
     */
    public function findbyID()
    {
        if (isset($this->part_id)) {
            $this->findBarcodes();
            $this->findAttributes();
            $this->findPartInfo();
        }
    }   // end of findbyPartID


    /*
     *  This method will find all of the information for any given part when it's BARCODE is known.
     */
    public function findbyBarcode()
    {
        if (isset($this->barcode)) {
            $this->findPartID();
            $this->findBarcodes();
            $this->findAttributes();
            $this->findPartInfo();
        }
    }   // end of findbyBarcode


    /*
     *  This method will find all of the information for any given part when its PART_NUM is known.
     */
    public function findbyPartNum()
    {
        if (isset($this->part_num)) {
            $this->findPartID();
            $this->findBarcodes();
            $this->findAttributes();
            $this->findPartInfo();
        }
    }   // end of findbyPartNum


    /*
     *  This method is used to send the information from a found part back to the client
     */
    public function sendPart()
    {
        $this->generateResults();
    }


    /*
     *  This function is used to send a part's information encoded in a JSON format
     */
    public function sendJSON()
    {
        if ($this->in_db) {
            $part_data = json_encode(array('parts' => $this));
            echo $part_data;
        } else {
            echo 0;
        }
    }

    /*
    *  The functions listed below are user-callable methods that assist in searching for parts in the database
    *  =======================================================================================================
    *
    *                      DATABASE SEARCHING
    *
    *  =======================================================================================================
    */

    /*
     *  This function will find the part_id from the database when either a barcode or part number was
     *  passed into the initial object's constructor function.
     */
    private function findPartID()
    {
        if (isset($this->barcode)) {
            $temp = $this->connection->searchQuery("SELECT part_id FROM barcode_lookup WHERE barcode=(?) LIMIT 1", $this->barcode);

        } elseif (isset($this->part_num)) { // This is "elseif" because it will only be executed if the findbyPartNum method is called
            $temp = $this->connection->searchQuery("SELECT part_id FROM parts WHERE part_num=(?) LIMIT 1", $this->part_num);
        }

        // If part_id was successfully found
        if ($temp) {
            // the returned info is always a 2D array formatted as $data[index#][field_name]
            $temp2 = array_shift($temp);
            $this->part_id = array_shift($temp2);
            $this->checkID();   // validate the in_db boolean value - invalid is set within findBarcode() method
        }

    }   // end of findPartID


    /*
     *  This function will find all of a part's barcodes from the 'barcode_lookup' table in the database.
     *  NOTE: The PART_ID must be known
     */
    private function findBarcodes()
    {
        // make sure we can search using the part_id
        if (empty($this->part_id)) {
            $this->findPartID();
        }

        // make sure part_id was found if not before
        if (isset($this->part_id)) {
            $temp = $this->connection->searchQuery("SELECT barcode, quantity, added FROM barcode_lookup WHERE part_id=(?)", $this->part_id);

            foreach ($temp as $index => $bag) {
                $this->bags[] = New Bag($bag['barcode'], $bag['quantity']);
            }

            $this->num_bags = count($this->bags);
            $this->calcQty();    // add up all the quantities for a grand total
        }
    }   // end of findBarcodes


    /*
     *  This function will find a part's attributes from the 'attributes' table in the database.
     *  NOTE: The PART_ID must be known
     */
    private function findAttributes()
    {
        // Ensure that the part_id is known
        if (empty($this->part_id)) {
            $this->findPartID();
        }

        // make sure part_id was found if not before
        if (isset($this->part_id)) {
            $temp = $this->connection->searchQuery("SELECT attribute, value, priority FROM attributes WHERE part_id=(?) ORDER by priority", $this->part_id);

            if ($temp) {
                foreach ($temp as $index => $attrib) {
                    $this->attributes[] = New Attribute($attrib['attribute'], $attrib['value'], $attrib['priority']);
                }
            }
        }
    }   // end of findAttributes


    /*
     *  This function will find all the information from the 'parts' table in the database.
     *  NOTE: The PART_ID must be known
     */
    private function findPartInfo()
    {
        // Ensure that the part_id is known
        if (empty($this->part_id)) {
            $this->findPartID();
        }

        //
        if (empty($this->part_num) | empty($this->location)) {
            $data_array = $this->connection->searchQuery("SELECT * FROM parts WHERE part_id=(?)", $this->part_id);

            // Only continue if results were found
            if ($data_array) {
                foreach ($data_array as $index => $items) {
                    foreach ($items as $key => $val) { // itterate through all fields
                        $this->$key = $val;
                    }
                }
            }

        }
    }   // end of findPartInfo


    /*
    *  These are assorted functions that are used exclusively within the class.
    *  =======================================================================================================
    *
    *                      MISC.
    *
    *  =======================================================================================================
    */


    /*
     *  This function will calculate a total quantity for all barcodes that share the found part number
     */
    private function calcQty()
    {
        if ($this->bags) {  // only count bags if we have at least 1
            // decalre the array to prevent php from yelling at you
            $qty = array('part_id' => 0, 'barcode' => 0, 'quantity' => 0);

            foreach ($this->bags as $index => $items) {
                foreach ($items as $bag => $bag_qty) {
                    $qty[$bag] += $bag_qty;
                }
            }
            // set the quantity
            $this->total_qty = $qty['quantity'];
        }
    }   // end of calcQty


    /*
    *  These methods and functions are used at the start of adding a part to the database
    *  =======================================================================================================
    *
    *                      IMPORTING DATA
    *
    *  =======================================================================================================
    */


    /*
     *  This method checks the client's passed data against the part's information in the database. We do not want
     *  to change the "last updated" field from the 'parts' table in the database if all information is the same
     */
    private function pullPart()
    {
        // Set the location as upper case
        if ($this->location != $this->input->location) {
            $this->location = strtoupper($this->input->location);
            $this->part_needs_update = true;
        }

        // Set the description to nothing if no description given
        if ($this->description != $this->input->description) {
            if ($this->input->description == "") {
                $this->description = null;
            } else {
                // always store the description as lower case
                $this->description = strtolower($this->input->description);
            }
            $this->part_needs_update = true;
        }

        // Set the name
        if ($this->name != $this->input->name) {
            $this->name = $this->input->name;
            $this->part_needs_update = true;
        }

        // Set the category as lower case
        if ($this->category != $this->input->category) {
            $this->category = strtolower($this->input->category);
            $this->part_needs_update = true;
        }

        // Set datasheet - do not modify case of letters, some server routing services are case sensitive
        if ($this->datasheet != $this->input->datasheet) {
            $this->datasheet = $this->input->datasheet;
        }
    }


    /*
     *
     */
    private function pullBags()
    {
        // create temporary array of new bags
        $temp_bags = array();
        foreach ($this->input->bags as $bag) {
            $temp_bags[] = New Bag($bag->barcode, $bag->quantity);
        }

        return $temp_bags;
    }   // end of pullBags


    /*
     *
     */
    private function pullAttributes()
    {
        // create temporary array of new attributes
        $temp_attributes = array();
        foreach ($this->input->attributes as $attrib) {
            $temp_attributes[] = New Attribute($attrib->attribute, $attrib->value, $attrib->priority);
        }

        return $temp_attributes;
    }   // end of pullAttributes


    /*
     *  This function will filter the client's passed input values into the class's structure. This must be called
     *  before anything can be done with the information from the data fields.
     */
    private function filterInput()
    {
        // This method should never be called without client-submitted data
        if (isset($this->input)) {

            // Set the user-submitted part number into the data structure
            $this->part_num = $this->input->part_num;

            // Check the database and see if the part number is already in the 'parts' table
            $this->checkPart();

            // If part is already in the database, only append the extra barcodes and quantities
            if ($this->in_db) {
                // Find all the part information current stored in the database
                $this->findbyPartNum();
                $this->pullPart();
                // find the values within the user-submitted input that's not already in the database.
                $this->new_attributes = $this->compareAttributes($this->pullAttributes());
                $this->new_bags = $this->compareBags($this->pullBags());
            } else {
                $this->pullPart();
                $this->new_attributes = $this->pullAttributes();
                $this->new_bags = $this->pullBags();
            }

            // unset the client side input so this method will not execute if called again
            unset($this->input);

            // Validate all the passed inputs
            $this->validate();
        }

    }   // end of filterInput


    public function startInput()
    {
        $this->connection->startInput();
    }

    /*
    *  This function finds new bags that should be added/updated into the database
    */
    private function compareAttributes($raw_attribs)
    {
        if (!$this->new_attributes) {
            // create a temporary array for storing the attributes that should be added
            $returnAttribs = array();

            // loop through every attribute passed into the function
            foreach ($raw_attribs as $new_index => $new_attrib) {

                // temporary variable that is set if the attribute is already known
                $found = 0;

                // only compare if there are actually existing attributes to check the new attributes against
                if ($this->attributes) {

                    // loop through all of the known attributes and compare each one with the new attribute in question
                    foreach ($this->attributes as $known_index => $know_attrib) {
                        // if the attribute's name was already found to be in the database...
                        if ($new_attrib->attribute == $know_attrib->attribute && $new_attrib->value == $know_attrib->value) {
                            // set the temporary variable to 1 and break out of the foreach loop
                            $found = 1;
                            break;
                        }
                    }
                    // the loop will break and continue here if the attribute was found
                    // however, if the attribute was not found, add it to the array that will be returned
                }
                if (!$found) {
                    $returnAttribs[] = $new_attrib;
                }
            }
            // return the array of attributes that were found to not be in the database
            return $returnAttribs;
        }
    }   // end of compareAttributes


    /*
     *  This function finds new bags that should be added/updated into the database
     */
    private function compareBags($raw_bags)
    {
        if (!$this->new_bags) {
            // create a temporary array for storing the bags that should be added
            $returnBags = array();

            // loop through every bag passed into the function
            foreach ($raw_bags as $new_index => $new_bag) {
                // temporary variable that is set if the attribute is already known
                $found = 0;

                // only compare if there are actually existing bags to check against
                if ($this->bags) {

                    // loop through all of the known bags and compare each one with the new bag in question
                    foreach ($this->bags as $known_index => $know_bag) {
                        // if the barcode was already found to be in the database...
                        if ($new_bag->barcode == $know_bag->barcode && $new_bag->quantity == $know_bag->quantity) {
                            // set the temporary variable to 1 and break out of the foreach loop
                            $found = 1;
                            break;
                        }
                    }
                    // the loop will break and continue here if the attribute was found
                    // however, if the bag was not found, add it to the array that will be returned
                }
                if (!$found) {
                    $returnBags[] = $new_bag;
                }
            }
            return $returnBags;
        }
    }   // end of compareBags


    /*
     *  This function checks the database for the existence of a part by its ID number and sets $in_db boolean value accordingly
     */
    private function checkID()
    {
        if (isset($this->part_id)) {
            $count = $this->connection->searchQuery("SELECT COUNT(*) FROM barcode_lookup WHERE part_id=(?)", $this->part_id);
            $count = array_shift($count);
            $this->in_db = $count['COUNT(*)'];
        }
    }   // end of checkID


    /*
     *  This function checks the database for a part by its part number and sets the $in_db boolean value accordingly
     */
    private function checkPart()
    {
        if (isset($this->part_num)) {
            $count = $this->connection->searchQuery("SELECT COUNT(*) FROM parts WHERE part_num=(?)", $this->part_num);
            $count = array_shift($count);
            $this->in_db = $count['COUNT(*)'];
        }
    }   // end of checkPart


    /*
     *  This function add a part's information into the database and returns the part's ID number that was assigned
     */
    public function addPart()
    {
        if (!$this->in_db || $this->part_needs_update) {    // only run if not in the database already
            if (!$this->error_code) {
                $results = $this->connection->addPart($this);

                $error = $results['sqlstate'];
                if ($error) {
                    $this->commit_code = $error;
                    $this->abort();
                } else {

                    if ($results['rows_added']) {
                        $this->log_statement = 'PART ADDED: ' . $results['rows_added'] . ' row added to the parts table for partnumber ';
                        $this->part_id = $results['part_id'];
                    } else {
                        $this->log_statement = 'PART MODIFIED: ' . $results['rows_modified'] . ' row changed from the parts table for partnumber ';
                    }

                    // write a new line in the log file
                    $this->log_statement .= $this->part_num . '.';
                    $this->part_log->writeLog($this->log_statement);
                }
            }
        }
    }   // end of addPart


    /*
     *  This method adds new bags (barcode/qty) into the database
     */
    public function addBags()
    {
        if (isset($this->new_bags)) {
            // if there are no errors for the user's input
            if (!$this->error_code) {
                // Add the new bags into the database
                $results = $this->connection->addBags($this->part_id, $this->new_bags);
                $error = $results['status'];
                if ($error) {
                    $this->commit_code = $error;
                    $this->abort();
                } else {
                    $added = $results['rows_added'];
                    $modified = $results['rows_modified'];

                    if ($added) {
                        $plural = ($added == 1) ? '' : 's';
                        $this->log_statement = 'BAG ADDED: ' . $added . ' row' . $plural . ' added to the barcode_lookup table for partnumber ';
                        $this->log_statement .= $this->part_num . '.';
                        $this->part_log->writeLog($this->log_statement);
                    }
                    if ($modified) {
                        $plural = ($modified == 1) ? '' : 's';
                        $this->log_statement = 'BAG MODIFIED: ' . $modified . ' row' . $plural . ' changed from the barcode_lookup table for partnumber ';
                        $this->log_statement .= $this->part_num . '.';
                        $this->part_log->writeLog($this->log_statement);
                    }
                }
            }
        }
    }   // end of addBags


    /*
     *  This method adds new attributes (attribute/value) into the database
     */
    public function addAttributes()
    {
        if (isset($this->new_attributes)) {
            // if there are no errors for the user's input
            if (!$this->error_code) {
                // Add the new attributes into the database
                $results = $this->connection->addAttributes($this->part_id, $this->new_attributes);
                $error = $results['status'];
                if ($error) {
                    $this->commit_code = $error;
                    $this->abort();
                } else {
                    $added = $results['rows_added'];
                    $modified = $results['rows_modified'];
                    if ($added) {
                        $plural = ($added == 1) ? '' : 's';
                        $this->log_statement = 'ATTRIBUTE ADDED: ' . $added . ' row' . $plural . ' added to the attributes table for partnumber ';
                        $this->log_statement .= $this->part_num . '.';
                        $this->part_log->writeLog($this->log_statement);
                    }
                    if ($modified) {
                        $plural = ($modified == 1) ? '' : 's';
                        $this->log_statement = 'ATTRIBUTE MODIFIED: ' . $modified . ' row' . $plural . ' changed from the attributes table for partnumber ';
                        $this->log_statement .= $this->part_num . '.';
                        $this->part_log->writeLog($this->log_statement);
                    }
                }
            }
        }
    }   // end of addAttributes

    /*
     *  This function will ensure that any database changes are valid before making the modifications/additions finalized
     */
    public function storeData()
    {
        if (empty($this->error_code) && empty($this->commit_code)) {
            // commit the database changes
            $this->connection->endInput();
            $this->send_status = "Please place part number <i> " . $this->part_num . "</i> into bin <i>" . $this->location . "</i>.</br>";
        }
    }

    public function sendStatus()
    {

        $temp = 'Success ';
        if ($this->error_code || $this->commit_code) {
            $temp = 'Error ';
        }

        echo json_encode(array('title' => $temp, 'message' => $this->send_status, 'validation_code' => $this->error_code));
    }

    /*
     *  ** THIS METHOD ENDS THE RUNNING PHP PROCESS - ONLY CALL IN LAST RESORT SITUATIONS **
     *  This method will call the "rollback()" method of the mysqli class. This will cause any database
     *  changes that have yet to be committed from being permentately written into the database structure.
     */
    private function abort()
    {
        $this->connection->rollBack();
        $this->send_status = "Database error (<i> " . $this->commit_code . "</i>). Please contact the system administrator.</br>No database changes.</br>";
        $this->error_code = 0xFF;
        $this->sendStatus();
        exit();
    }


    /*
     *  This function is used for validating the content sent from the client before it is added into the database
     */
    public function validate()
    {
        $temp1 = $this->validateBarcode();
        if ($temp1) {
            $this->barcodes_can_add = true;
        }

        $temp2 = $this->validateLocation();
        $temp3 = $this->validateCategory();
        $temp4 = $this->validateDatasheet();
        if ($temp2 && $temp3 && $temp4) {
            $this->part_can_add = true;
        }
    }   // end of validate


    /*
     *  This method is used for validating the client's location input.
     */
    public function validateLocation()
    {
        // typical location names
        preg_match('/^[A-I]0[1-6]$/i', $this->location, $match);
        if (!$match) {
            $this->error_code = $this->error_code | 0x01;
            $this->send_status = "Location <i>" . $this->location . "</i> is an invalid location.</br>";
            return 0;   // invalid
        } else {
            return 1;   // validation approved
        }
    }   // end of validateLocation


    /*
     *  This method is used for validating the client's barcode input. This will ensure that all a barcode does not
     *  already exist in the database before being entered.
     */
    public function validateBarcode()
    {
        if (isset($this->new_bags)) {
            foreach ($this->new_bags as $index => $bag) {


                // 8 digits, first is always 0, next 6 are any digit in [0-9], last is checksum digit
                preg_match('/^\d{2,8}$/', (int)$bag->barcode, $match);
                if (!$match) {
                    $this->error_code = $this->error_code | 0x02;
                    $this->send_status = "<i>" . $bag->barcode . "</i> is an invalid barcode.</br>";
                    return 0;   // invalid
                }

                $temp = $this->connection->searchQuery("SELECT COUNT(*) FROM barcode_lookup WHERE barcode=(?)", $bag->barcode);
                $temp = $temp = array_shift($temp);
                $count = $temp['COUNT(*)'];
                if ($count) {
                    $this->error_code = $this->error_code | 0x04;
                    $this->send_status = "Barcode <i>" . $bag->barcode . "</i> is already in the database.</br>";
                    return 0;   // invalid
                }


            }
            return 1;   // validation approved
        }
    }   // end of validateBarcode


    /*
     *  This method is used for validating the client's category input
     */
    public function validateCategory()
    {
        $categories = array('resistor', 'capacitor', 'inductor', 'diode', 'discrete', 'ic', 'oscillator', 'connector', 'other');
        if (!in_array($this->category, $categories)) {
            $this->error_code = $this->error_code | 0x08;
            $this->send_status = "Category <i>" . $this->category . "</i> is an invalid category.</br>";
            return 0;   // invalid
        } else {
            return 1;   // validation approved
        }
    }   // end of validateCategory


    /*
     *
     */
    public function validateDatasheet()
    {
        // remove invalid characters
        $this->datasheet = filter_var($this->datasheet, FILTER_SANITIZE_URL);

        $temp = $this->datasheet;

        // add http:// if missing - this also accounts for https://
        if (preg_match("#https?://#", $this->datasheet) === 0) {
            $temp = 'http://' . $temp;
        }

        // determine if the url is valid and return the boolean result
        if (filter_var($temp, FILTER_VALIDATE_URL)) {
            $this->datasheet = $temp;
            return 1;   // valid

        } else {
            return 0;   // invalid
        }
    }   // end of validateDatasheet


    /*
    *  The methods in this section are used for generating the client-side return information
    *  =======================================================================================================
    *
    *                      SENDING DATA
    *
    *  =======================================================================================================
    */


    /*
     *  This function constructs a result box that is sent back to the client
     */
    private
    function generateResults()
    {
        if (isset($this->total_qty)) {
            echo '<div id="results-pane" class="container">';
            echo '<div class="row">';
            echo '<div class="col-xs-12 space"></div>';
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="col-xs-12">';
            echo '<div class="panel panel-primary">';
            echo '<div class="panel-heading">';
            echo '<div id="part-location-data" class="part-location">';

            echo $this->location;

            echo '</div>';
            echo '<div class="part">';
            echo '<div id="part-name-data" class="part-name">';

            echo $this->name;

            echo '</div>';
            echo '<div id="part-num-data" class="part-num">';

            echo 'PN: <b><a href="' . $this->datasheet . '" target=' . '"_blank' . '">' . $this->part_num . '</a></b>';

            if ($this->searchmode == 'barcode') {

                echo '  | Qty: <b>';

                foreach($this->bags as $index => $bag){
                    if($bag->barcode == $this->barcode)
                    {
                        echo $bag->quantity;
                    }
                }
                echo '</b>';

            } else {

                echo '  | Bags: <b>' . $this->num_bags . '</b>  | Qty: <b>' . $this->total_qty . '</b>';

            }

            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '<div class="panel-body">';
            echo '<dl class="dl-horizontal">';

            $this->generateAttributes();

            echo '</dl>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        } else    // let user know if no results were found
        {
            $this->noResults();
        }
    }   // end of generateResults


    /*
     *  This is a method used for generating the dynamic set of attributes for the client
     */
    private
    function generateAttributes()
    {
        if (isset($this->attributes)) {
            foreach ($this->attributes as $index => $attrib) {
                echo '<dt>' . $attrib->attribute . '</dt>';
                echo '<dd>' . $attrib->value . '</dd>';
            }
        }
    }   // end of generateAttributes


    /*
     *  This function is used to construct a "No Results" box that is sent back to the client
     */
    private
    function noResults()
    {
        echo '<div id="results-pane" class="container">';
        echo '<div class="row">';
        echo '<div class="col-xs-12 space"></div>';
        echo '</div>';
        echo '<div class="row">';
        echo '    <div class="col-xs-12">';
        echo '        <div class="panel panel-primary">';
        echo '            <div class="panel-heading part-num">';
        echo 'No Results Were Found';
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';
        echo '</div>';
        echo '</div>';
    }   // end of noResults


}   // end of Part class
