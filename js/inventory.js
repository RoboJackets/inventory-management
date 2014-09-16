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

                // Create placeholder containers
                $.each($.parseJSON(result), function (index, container_count) {
                    
                    if (index === "num_results")
                    {
                        console.log(index + " => " + container_count);
                        
                    $.get("/php/populate-result-panes.php", { "items": container_count }, function (containers) {
                        
                        $( 'body' ).append( containers );
                        
                    });
                    }
                    
                });

                
                $.each($.parseJSON(result), function (index, object) {
                    
                    var part = object;
                    var temp = index;
                    
                    console.log(index + " => " + part);
                    
                        console.log("\n");
                    $.each(object, function(key, value){
                        // log the values of the part to the console (for debugging
                        // purposes only)
                            
                        console.log(key + " => " + value);    
                            
                            
                            console.log(value.name);
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