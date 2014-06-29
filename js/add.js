function slideCard(card, direction, side) {
    if (direction === "in") {
        if (side === "right") {
            card.switchClass("off-left", "off-right", 0);
            card.removeClass("off-right", 300);
        } else {
            card.switchClass("off-right", "off-left", 0);
            card.removeClass("off-left", 300);
        }
    } else {
        if (side === "right") {
            card.addClass("off-right", 300);
        } else {
            card.addClass("off-left", 300);
        }
    }
};

function toCard(targetCardID, currentCardID) {
    if ($(currentCardID).nextAll(targetCardID).length !== 0) {
        slideCard($(currentCardID), "out", "left");
        slideCard($(targetCardID), "in", "right");
    } else if ($(currentCardID).prevAll(targetCardID).length !== 0) {
        slideCard($(currentCardID), "out", "right");
        slideCard($(targetCardID), "in", "left");
    }

    $("ol.steps li.steps-active").removeClass("steps-active");
    var stepIndex = $(targetCardID).index() + 1;
    $("ol.steps li:nth-child(" + stepIndex + ")").addClass("steps-active");

    return targetCardID;
};


$(document).ready(function() {
    var currentCardID = "#add-part";

    $(".to-card.btn-enabled").click(function() {
        var target = $(this).attr("card");
        if(!(("#" + target) === currentCardID)) {
            currentCardID = toCard("#" + target, currentCardID);
        }
    });

    $('.card .next.btn-enabled').click(function() {
        var target = $(this).parent().next().attr("id");
        currentCardID = toCard("#" + target, currentCardID);

    });

    $('.card .back.btn-enabled').click(function() {
        var target = $(this).parent().prev().attr("id");
        currentCardID = toCard("#" + target, currentCardID);
    });    

});