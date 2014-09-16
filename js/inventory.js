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
            url: '/php/search.php',
            data: {
                mode:   $('#mode-storage').val(),
                input:  $('#txtSubmitQuery').val()
            },
            success: function(result){
                
                // data comes back in json format
                //var json = jQuery.parseJSON(result);
                //console.log(json);
                //var data = json.parts[0]; // get the first search result returned
                
                $.each($.parseJSON(result), function (index, object) {
                    
                    var part = object;
                    
                    console.log("\n\nPart\n==========\n");
                    
                    $.each(object, function (key, value) { 
                        console.log(key + " => " + value);
                    });
                    
                });
                
                
                $('#part-location-data').html(part.location);
                $('#part-name-data').html(part.name);
                $('#part-num-data').html(part.part_num);
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