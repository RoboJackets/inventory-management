<!---------------------------------------------------------------------------
Example client script for JQUERY:AJAX -> PHP:MYSQL example
---------------------------------------------------------------------------->

<html>
  <head>
    <script language="javascript" type="text/javascript" src="jquery.js"></script>
  </head>
  <body>

  <!-------------------------------------------------------------------------
  1) Create some html content that can be accessed by jquery
  -------------------------------------------------------------------------->
  <h2> Client example </h2>
  <h3>Output: </h3>
  <div id="output">this element will be accessed by jquery and this text replaced</div>

  <script id="source" language="javascript" type="text/javascript">

  $(function () 
  {
    //-----------------------------------------------------------------------
    // 2) Send a http request with AJAX
    //-----------------------------------------------------------------------
    $.ajax({                                      
      url: 'rj-inv_search-part.php',   //the script to call to get data          
      data: "",                        //you can insert url argumnets here to pass to api.php
                                       //for example "id=5&parent=6"
      dataType: 'json',                //data format
      
      // NEED TO WORK ON THIS TO BE EXPANDABLE FOR MULTIPLE RETURNS...?
      success: function(data)          //on recieve of reply
      {
        var pn = data[0];               //get id
        var name = data[1];             //get name
        var category = data[2]
        var value = data[3]
        var package = data[4]
        var description = data[5]
        var datasheet = data[6]
        var attributes = data[7]
        var location = data[8]
        var flag_error = data[9]
        var status = data[10]
        var updated = data[11]
        
        //--------------------------------------------------------------------
        // 3) Update html content
        //--------------------------------------------------------------------
        $('#output').html("<b>id: </b>"+id+"<b> name: </b>"+vname); 
        // Set output element html recommend reading up on jquery selectors,
        // they are awesome.
        // http://api.jquery.com/category/selectors/
      } 
    });
  }); 

  </script>
  </body>
</html>