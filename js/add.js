$(document).ready(function(){
    
    // Animate to next card
    $('.card .next').click(function() {
        $(this).parent().addClass('off-left',500);
        $(this).parent().next().removeClass('off-right',500);
        
        $('.steps .steps-highlighted').last().next().addClass('steps-highlighted');
    });
    
    // Animate to previous card
    $('.card .back').click(function() {
        $(this).parent().addClass('off-right',500);
        $(this).parent().prev().removeClass('off-left',500);
        
        $('.steps .steps-highlighted').last().removeClass('steps-highlighted');
    });
});