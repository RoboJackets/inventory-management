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
     *  These 2 variables are calculated from the found database results
     */
    public $num_bags;
    public $total_qty;
    public $in_db;

    /*      ======== PRIVATE ========
     *  These 3 private variables are used for searching the database
     */
    private $part_id;
    private $barcode;
    private $input;
    private $new_bags;
    private $new_attributes;

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

        if (isset($input)) {
            if (isset($input['part']) or isset($input['barcode']) or isset($input['part_id'])) {
                if (isset($input['part'])) {
                    $this->input = $input['part'];
                    $this->new_bags = array();
                    $this->new_attributes = array();
                    $this->filterInput();
                } elseif (isset($input['barcode'])) {
                    $this->barcode = $input['barcode'];
                    $this->findbyBarcode();
                } elseif (isset($input['part_id'])) {
                    $this->part_id = $input['part_id'];
                    $this->findbyID();
                } elseif (isset($input['part_num'])) {
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
            $this->part_id = array_shift(array_shift($temp));
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
        $qty = array();

        foreach ($this->bags as $index => $items) {
            foreach ($items as $bag => $bag_qty) {
                $qty[$bag] += $bag_qty;
            }
        }
        // set the quantity
        $this->total_qty = $qty['quantity'];
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
     *  This function will filter the client's passed input values into the class's structure. This must be called
     *  before anything can be done with the information from the data fields.
     */
    private function filterInput()
    {

        // Set the part number
        $this->part_num = $this->input->part_num;

        // Check the database and see if the part number is already there
        $this->checkDatabase();

        // If part is already in the database, only append the extra barcodes and quantities
        if ($this->in_db) {
            // Find all the part information current stored in the database
            $this->findbyPartNum();

            // Add the aditional barcodes and quantities to the class structure
            foreach ($this->input->bags as $bag) {
                $this->new_bags[] = New Bag($bag->barcode, $bag->quantity);
            }

            // Add the additional attributes and their values to the class structure
            foreach ($this->input->attributes as $attrib) {
                $this->new_attributes[] = New Attribute($attrib->attribute, $attrib->value, $attrib->priority);
            }

            // Check the new barcode and attribute values against what was already in the database
            $this->checkValues();

        } else {


            // Set the location
            $this->location = strtoupper($this->input->location);

            // Set the description
            if ($this->input->description == "") {
                $this->description = null;
            }

            // Set the name
            $this->name = $this->input->name;

            // Set the category
            $this->category = $this->input->category;


            // Set datasheet
            $this->datasheet = $this->input->datasheet;

        }

    }   // end of filterInput


    /*
     *  This function checks the database for a part by its part number and set/unsets the $in_db boolean value accordingly
     */
    private function checkDatabase()
    {
        if (isset($this->part_id)) {
            $count = array_shift($this->$connection->searchQuery("SELECT COUNT(*) FROM parts WHERE part_num=(?)", $this->part_num));
            $this->in_db = $count['COUNT(*)'];
            var_dump($this->in_db);
        }
    }

    /*
     *  This function adds data to the database from a part that already exists
     */
    private function checkValues()
    {
        // Check for barcodes that already exist
        foreach($this->new_bags as $add_bag)
        {
            var_dump($add_bag);
            if(in_array($add_bag, $this->bags))
            {
                unset($add_bag);
            }
        }

        // Check for attributes that already exist
        foreach($this->new_attributes as $add_attrib)
        {
            if(in_array($add_attrib, $this->bags))
            {
                unset($add_attrib);
            }
        }

    }   // end of addOld


    public function addNewBags()
    {
        // Add the new bags into the database
        $this->connection->addBags($this->part_id, $this->new_bags);
    }

    /*
     *  This function is used for validating the content sent from the client before it is added into the database
     */
    private function validate()
    {

        // PUT STUFF HERE

    }   // end of validate


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

            echo 'PN: <b>' . $this->part_num . '</b>';

            //if ($this->bags) {
            echo '  | Bags: <b>' . $this->num_bags . '</b>  | Qty: <b>' . $this->total_qty . '</b>';
            //}

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
