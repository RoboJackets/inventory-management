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
                $.each(data, function (index, container_count) {
                    
                    if (index === "num_results")
                    {
                        console.log(index + " => " + container_count + "\n\n\n");
                        
                    $.get("/php/populate-result-panes.php", { "items": container_count }, function (containers) {
                        
                        for (i = 0; i<data.num_results; i++)
                        {
                            $( 'body' ).append( containers );
                        }
                        
                    });
                    }
                    
                });

                $.each(data.parts, function (arg) {
                    
                    //console.log(index + " => " + value + "\n");                        
                            
                      //  console.log(index + " => " + value + "\n");     
                        console.log(arg.name + "<- name");
                        //if (index === "parts") {

                            $('#part-location-data').each( function(value){
                                $(item).html(arg.location);
                            });
                            $('#part-name-data').each( function(value){
                                $(item).html(arg.name);
                            });
                            ('#part-num-data').html("PN: " + arg.part_num + "  | Bags: " + arg.num_bags + "  | Qty: " + arg.total_qty);
                        
                        //}
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