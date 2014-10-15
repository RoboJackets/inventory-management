$(document).ready(function(){
    
    $('#txtSubmitQuery').focus();

    $("#txtSubmitQuery").on("change keyup paste", function () {

        var query = {"input": $(this).val()};

        $.post("livesearch", query, function (data) {

            var result = $.parseJSON(data);

            $('#results-placeholder').empty();

            //console.log(result);

           // if (result > 0) {
              //  console.log('results');
                for (var i in result) {
                    console.log(result[i]);
                    $('#results-placeholder').append(result[i]);
                    $('#results-placeholder').append('</br>');
                }
            //} else {

           // }
        });


    });

    $('#barcode').click(function(){
        if(!$('#barcode').hasClass('mode-selected')){
            $('.mode-selected').removeClass('mode-selected');
            $('#barcode').addClass('mode-selected');
            $('#mode-storage').val('barcode');
        }
        $('#txtSubmitQuery').focus();
    });
    
    $('#bin').click(function(){
        if(!$('#bin').hasClass('mode-selected')){
            $('.mode-selected').removeClass('mode-selected');
            $('#bin').addClass('mode-selected');
            $('#mode-storage').val('bin');
        }
        $('#txtSubmitQuery').focus();
    });

    $('#BtnSubmitQuery').click(function(){
        var query = $('#txtSubmitQuery').val();
        
        // ajax communication for getting database results
        $.ajax({
            type: 'GET',
            url: '/php/search.php',
            data: {
                mode:   $('#mode-storage').val(),
                input:  $('#txtSubmitQuery').val()
            },
            success: function(result){
                $('#results-placeholder').empty();
                $('#results-placeholder').append(result);
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