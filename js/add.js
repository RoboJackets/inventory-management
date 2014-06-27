$(document).ready(function(){
    $('.card #btn-add-next').click(function() {
        $(this).parent().addClass('off-left',500);
        $(this).parent().next().removeClass('off-right',500);
    });
});