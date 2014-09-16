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
                        
                        $( 'body' ).append( containers );
                        
                    });
                    }
                    
                });

                $.each(data, function (index, object) {
                    
                    console.log(index + " => " + object + "\n");                        
                            
                        console.log(index + " => " + object);     
                        console.log(index.name);
                        if (index === "parts") {

                            $('#part-location-data').html(data.parts.location);
                            $('#part-name-data').html(data.parts.name);
                            $('#part-num-data').html("PN: " + data.parts.part_num + "  | Bags: " + data.parts.num_bags + "  | Qty: " + data.parts.total_qty);
                        
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