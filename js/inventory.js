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
        $('#results-placeholder').empty();
        
        var data;
        
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
                data = $.parseJSON(result);

                $.each(data, function (index, container_count) {
                    // only append data if results are found
                    if (container_count > 0)
                    {
                        console.log(index + " => " + container_count + "\n\n\n");
                        
                        $.get("/php/populate-result-panes.php", function(containers) {
                            $('#results-placeholder').append(containers);                      
                        });
                    }
                    
                    
                    
                    // fll in data
                    $.each(data.parts, function(arg, obj){
                        $('#part-location-data').each(function(){
                                $(this).html(obj.location);
                    });
                        $('#part-name-data').html(obj.name);
                        $('#part-num-data').html("PN: " + obj.part_num + "  | Bags: " + obj.num_bags + "  | Qty: " + obj.total_qty);
                    });
                    
                });
            },
            
            /*
            complete: function(){

                $.each(data.parts, function (arg, obj){
                    $('#part-location-data').html(obj.location);
                    $('#part-name-data').html(obj.name);
                    $('#part-num-data').html("PN: " + obj.part_num + "  | Bags: " + obj.num_bags + "  | Qty: " + obj.total_qty);
                
                }); },*/
        });

        $('#txtSubmitQuery').val('');
    });
    
    $('#txtSubmitQuery').keypress(function(e){
        if(e.which === 13){//Enter key pressed
            $('#BtnSubmitQuery').click();//Trigger search button click event
        }
    });
});