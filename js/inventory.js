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
                    
                    if (index === "num_results")
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
                    
                    console.log("made it!");
                    
                    var part = object;

                    if (index === "parts")
                    {
                        console.log(index + " => " + object);
                        
                    $.each(part, function(key, value){
                    // log the values of the part to the console (for debugging
                    // purposes only)

                    // Update the information on the current page
                    if (key === "location")
                    {
                        $('#part-location-data').html(part.location);
                    }
                    
                    if (key === "name")
                    {
                        $('#part-name-data').html(part.name);
                    }
                
                    if (key === "part_num")
                    {
                        $('#part-num-data').html("PN: " + part.part_num + "  | Bags: " + part.num_bags + "  | Qty: " + part.total_qty);
                    }

               });
           }
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