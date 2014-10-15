$(document).ready(function () {

    $('#txtSubmitQuery').focus();

    var engine = new Bloodhound({
        name: 'parts',
        remote: 'http://dev.rj.str.at/livesearch?q=%QUERY',
        //remote: 'http://rj.localhost/livesearch?q=%QUERY',
        datumTokenizer: function(d) {
            return Bloodhound.tokenizers.whitespace(d.val);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace
    });
    var promise = engine.initialize();

    $('#txtSubmitQuery').typeahead({
        // limit typeahead results from searching query until a few characters are known
        // minLength: 2,
        highlight: true
    }, {
        displayKey: 'part',
        source: engine.ttAdapter()
    });


    $('#barcode').click(function () {
        if (!$('#barcode').hasClass('mode-selected')) {
            $('.mode-selected').removeClass('mode-selected');
            $('#barcode').addClass('mode-selected');
            $('#mode-storage').val('barcode');
        }
        $('#txtSubmitQuery').focus();
    });

    $('#bin').click(function () {
        if (!$('#bin').hasClass('mode-selected')) {
            $('.mode-selected').removeClass('mode-selected');
            $('#bin').addClass('mode-selected');
            $('#mode-storage').val('bin');
        }
        $('#txtSubmitQuery').focus();
    });

    $('#BtnSubmitQuery').click(function () {
        var query = $('#txtSubmitQuery').val();

        // ajax communication for getting database results
        $.ajax({
            type: 'GET',
            url: '/search',
            data: {
                mode: $('#mode-storage').val(),
                input: $('#txtSubmitQuery').val()
            },
            success: function (result) {
                $('#results-placeholder').empty();
                $('#results-placeholder').append(result);
            }
        });

        $('#txtSubmitQuery').val('');
    });

    $('#txtSubmitQuery').keypress(function (e) {
        if (e.which === 13) {//Enter key pressed
            $('#BtnSubmitQuery').click();//Trigger search button click event
        }
    });


});