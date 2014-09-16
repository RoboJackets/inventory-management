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
                
                var data = $.parseJSON(result);
                
                //$.each($.parseJSON(result), function (index, container_count) {
                    
                $.each(data, function (index, container_count) {
                    
                    if (index === "num_results")
                    {
                        console.log(index + " => " + container_count);
                        
                    $.get("/php/populate-result-panes.php", { "items": container_count }, function (containers) {
                        
                        for ($i = 0; $i<$data.num_results; $i++)
                        {
                            $( 'body' ).append( containers );
                        }
                        
                    });
                    }
                    
                });

                $.each(data, function (index, object) {
                    
                    console.log(index + " => " + object + "\n");                        
                            
                        console.log(index + " => " + object);     
                        console.log(index.name);
                        if (index === "parts") {

                            $('#part-location-data').each( function(object){
                                $(item).html(index.location);
                            });
                            $('#part-name-data').each( function(object){
                                $(item).html(index.name);
                            });
                            //$('#part-num-data').html("PN: " + index.part_num + "  | Bags: " + index.num_bags + "  | Qty: " + index.total_qty);
                        
                        }
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