<?php

/**
 * Description of the Part Class
 *
 * @author Jonathan Jones
 */

if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }

class Part {
    
    private $part_id;
    private $barcode;
    private $connection;
    
    public $part_num;
    public $name;
    public $category;
    public $description;
    public $datasheet;
    public $location;
    
    public $bags;
    public $attributes;

    public $num_bags;
    public $total_qty;


    // Prepares the object when a new one is created.
    public function __construct(Database $db, $input)
    {
        $this->bags = array();
        $this->attributes = array();
        $this->barcode = $input;
        $this->connection = $db;
    }   // function __construct



    // This function will perform the most common purpose of this Class - finding all
    // information on a single given part number/barcode.
    public function findPart()
    {
        $this->findPartID();
        $this->findBarcodes();
        $this->findAttributes();
        $this->findPartInfo();
    }   //  function locateAllInfo
    


    public function sendPart()
    {
        $this->generateResults();
    }



    // Standard 'get' functions for private variables.
    public function get_part_id() {
        return $this->part_id;
    }


    public function get_barcode() {
        return $this->barcode;
    }
 


    // Function that echos json-encoded data about the part.
    public function sendJSON()
    {
        $temp = array();
        
        $temp['parts'] = $this;
        
        echo json_encode($temp);
    }   // function sendPart
    
    
    private function validate()
    {
        // will check for validation of fields when adding parts
    }
    

    // Function that searches the database for a partnumber when given a barcode.
    private function findPartInfo()
    {
        if(empty($this->part_id))
        {
            $this->findPartID();
        }
        if(empty($this->part_num) | empty($this->location))
        {
            $data_array = $this->connection->searchQuery("SELECT * FROM parts WHERE part_id=(?)", $this->part_id);

            if ($data_array) {
                foreach ($data_array as $index => $items) {
                    foreach ($items as $key => $val) { // itterate through all fields
                        $this->$key = $val;
                    }
                }
            }
            
        }
    }   // function findPartNum
    
    
    
    // Function that locates the part's ID number from the given input.
    private function findPartID()
    {
        if(isset($this->barcode))   // this should never be empty since assigned in the constructor
        {
            $temp = $this->connection->searchQuery("SELECT part_id FROM barcode_lookup WHERE barcode=(?) LIMIT 1", $this->barcode);

            // if part_id was successfully found
            if ($temp) {
                // the returned info is always a 2D array formatted as $data[index#][field_name]
                $this->part_id = array_shift(array_shift($temp));
            }

        }

        // if no result was found, assume the user input was a part number
        if (empty($this->part_id))
        {
            // move user's input to part number field and remove from barcode field
            $this->part_num = $this->barcode;
            $this->barcode = NULL;
            
            // search again for the part's id number
            $temp = $this->connection->searchQuery("SELECT part_id FROM parts WHERE part_num=(?) LIMIT 1", $this->part_num);
            if ($temp) {
                $this->part_id = array_shift(array_shift($temp));
            }
        }
    }   // function findPartID
    
    
    
    // Function that locates all the barcdoes and quantities for the respective part number.
    private function findBarcodes()
    {
        // make sure we can search using the part_id
        if(empty($this->part_id))
        {
            $this->findPartID();
        }
        
        // make sure part_id was found if not before
        if(isset($this->part_id))
        {
            $this->bags = $this->connection->searchQuery("SELECT barcode, quantity, added FROM barcode_lookup WHERE part_id=(?)", $this->part_id);
            $this->num_bags = count($this->bags);
            $this->calcQty();    // add up all the quantities for a grand total
        }
    }   // function findBarcodes
    
    
    
    // Function that locates all the attributes of a given part.
    private function findAttributes()
    {
        // make sure we can search using the part_id
        if(empty($this->part_id))
        {
            $this->findPartID();
        }
        
        // make sure part_id was found if not before
        if(isset($this->part_id))
        {
            $this->attributes = $this->connection->searchQuery("SELECT attribute, value, priority FROM attributes WHERE part_id=(?) ORDER by priority", $this->part_id);
        }
    }   // function findAttributes
    
    
    
    // This function will echo the HTML text that is brought in via Ajax on the client side.
    public function generateResults()
    {
        if (isset($this->total_qty))
        {
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

            echo 'PN: ' . $this->part_num . '  | Bags: ' . $this->num_bags . '  | Qty: ' . $this->total_qty;

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
        }
        else    // let user know if no results were found
        {
            $this->noResults();
        }
    }   // function outputResultsBox
    
    
    
    // This function will echo the HTML text used for the attributes section of the output.
    private function generateAttributes()
    {
        foreach($this->attributes as $index => $data)
        {
            foreach($data as $attrib => $val)
            {
                if ($attrib == 'attribute')
                {
                    echo '<dt>' . $val . '</dt>';
                }
                if ($attrib == 'value')
                {
                    echo '<dd>' . $val . '</dd>';   
                }
            }
        }  
    }   // function outputAttributeBox

    
    // This is the function that returns the HTML text used when no results are found.
    private function noResults()
    {
        echo '<div id="results-pane" class="container">';
        echo '<div class="row">';
        echo '<div class="col-xs-12 space"></div>';
        echo '</div>';
        echo '<div class="row">';
        echo '<div class="col-xs-12">';
        echo '<div class="panel panel-primary">';
        echo '<div class="panel-heading">';
        echo '<div id="part-location-data" class="part-location">';
            // location field
        echo '</div>';
        echo '<div class="part">';
        echo '<div id="part-name-data" class="part-name">';
            // name field
        echo '</div>';
        echo '<div id="part-num-data" class="part-num">';
        echo 'No results were found';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '<div class="panel-body">';
        echo '<dl class="dl-horizontal">';
            // attributes area
        echo '</dl>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }   // function noResults

    
    // This function can be called after all bags of a given part are found to
    // sum the total quantity for that part.
    private function calcQty()
    {
        $qty = array();
        
        foreach ($this->bags as $index=>$items)
        {
            foreach ($items as $bag=>$bag_qty)
            {
                $qty[$bag] += $bag_qty;
            }
        }
        // set the quantity
        $this->total_qty = $qty['quantity'];
    }   // function calcQty
    
    
}   // end of Part class
