$(document).ready(function(){
    
    $('#barcode').click(function(){
        if(!$('#barcode').hasClass('mode-selected')){
            $('.mode-selected').removeClass('mode-selected');
            $('#barcode').addClass('mode-selected');
        }
    });
    
    $('#bin').click(function(){
        if(!$('#bin').hasClass('mode-selected')){
            $('.mode-selected').removeClass('mode-selected');
            $('#bin').addClass('mode-selected');
        }
    });
    
    
    $('#BtnSubmitQuery').click(function(){
        var query = $('#txtSubmitQuery').val();
        console.log("query=" + query);
        //Post results here
        //$.post('../searchusers.php',{search: search},function(response){
            //$('#userSearchResultsTable').html(response);
        //});
        
        // testing some things out here
        $.ajax({
            type: 'GET',
            url: '/php/search.php',
            data: {
                mode:   $('#mode-storage').val(),
                input:  $('#txtSubmitQuery').val()
            },
            success: function(result){
                // data come back in json format
                var json = jQuery.parseJSON(result);
                console.log(JSON.stringify(json[0]));
                // parse and place the data in their respective places
                $('#results-pane .part-location').html(json[0].partLocation);
                $('#results-pane .part-name').html(json[0].partName);
                $('#results-pane .part-num').html(json[0].partNum);
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