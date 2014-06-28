function slideCard(card, direction, side) {
    if (direction === "in") {
        if (side === "right") {
            card.switchClass("off-left", "off-right", 0);
            card.removeClass("off-right", 500);
        } else {
            card.switchClass("off-right", "off-left", 0);
            card.removeClass("off-left", 500);
        }
    } else {
        if (side === "right") {
            card.addClass("off-right", 500);
        } else {
            card.addClass("off-left", 500);
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
    
    return targetCardID;
};


$(document).ready(function() {
    var currentCardID = "#add-part";
    
    $(".to-card").click(function() {
        var target = $(this).attr("card");
        console.log("target: " + target);
        if(!(("#" + target) === currentCardID)) {
            currentCardID = toCard("#" + target, currentCardID);
        } else {
            console.log("same card");
        }
    });
    
    //TO DO: Add Coloring to Steps
});