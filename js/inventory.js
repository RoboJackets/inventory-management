$(document).ready(function(){
    
    $('#barcode').click(function(){
        if(!$('#barcode').hasClass('mode-selected')){
            $('.mode-selected').removeClass('mode-selected');
            $('#barcode').addClass('mode-selected');
            $('#mode-storage').val('barcode');
        }
    });
    
    $('#bin').click(function(){
        if(!$('#bin').hasClass('mode-selected')){
            $('.mode-selected').removeClass('mode-selected');
            $('#bin').addClass('mode-selected');
            $('#mode-storage').val('bin');
        }
    });
    
    
    $('#BtnSubmitQuery').click(function(){
        var query = $('#txtSubmitQuery').val();
        console.log("query=" + query);
        
        // ajax communication for getting database results
        $.ajax({
            type: 'GET',
            url: '/php/binSearch.php',
            data: {
                mode:   $('#mode-storage').val(),
                input:  $('#txtSubmitQuery').val()
            },
            success: function(result){
                
                // data comes back in json format
                //var json = jQuery.parseJSON(result);
                //console.log(json);
                //var data = json.parts[0]; // get the first search result returned
                
                //var part;
                
                
                // Create placeholder containers
                //var num_r;
                $.each($.parseJSON(result), function (index, container_count) {
                    
                    if (index == "num_results")
                    {
                        console.log(index + " => " + container_count);
                        
                    $.get("/php/populate-result-panes.php", { "items": container_count }, function (containers) {
                        
                        $( 'body' ).append( containers );
                        
                    });
                    }
                    
                });
                
                //console.log(result);
                
                //console.log("\n\n\n\========\n" + num_r + "\n\n================");
                
                $.each($.parseJSON(result), function (index, object) {
                    
                    part = object.parts;  // assign the object to a declared variable
                    
                    $.each(part, function(){
                    // log the values of the part to the console (for debugging
                    // purposes only)
                    
                    
                   //$.each(object, function (key, value) { 
                     //   console.log(key + " => " + value);
                    
                    
                   if (key = "parts") 
                   {
                   // Update the information on the current page
                   $('#part-location-data').html(part.location);
                   $('#part-name-data').html(part.name);
                   $('#part-num-data').html("PN: " + part.part_num + "  | Bags: " + part.num_bags + "  | Qty: " + part.total_qty);
                   }
               });
                  // });
                   
                });
                
                
            }
        });
        
        
        $('#txtSubmitQuery').val('');
    });
    
    $('#txtSubmitQuery').keypress(function(e){
        if(e.which === 13){//Enter key pressed
            $('#BtnSubmitQuery').click();//Trigger search button click event
        }
    });
});