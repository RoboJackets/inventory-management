function slideCard(card, direction, side) {
    if (direction === "in") {
        if (side === "right") {
            card.switchClass("off-left", "off-right", 0);
            card.removeClass("hidden");
            card.removeClass("off-right", 300);
        } else {
            card.switchClass("off-right", "off-left", 0);
            card.removeClass("hidden");
            card.removeClass("off-left", 300);
        }
    } else {
        if (side === "right") {
            card.addClass("off-right", 300, "swing", function(){card.addClass("hidden");});
        } else {
            card.addClass("off-left", 300, "swing", function(){card.addClass("hidden");});
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

function enableCard(cardID) {
    cardID = "#" + cardID;
    var stepIndex = $(cardID).index() + 1;
    $("ol.steps li:nth-child(" + stepIndex + ")").addClass("btn-enabled");
};

function addAttributeInput() {
    var attrNum = $(".card table tbody tr:last-child").index() + 2;
    $tableRow = $("#add-attributes tbody tr:first-child").clone().find("input").val("").end();
    $tableRow.children("td:first-child").text(attrNum);
    $tableRow.one("keydown paste", function() {
        addAttributeInput()
    });

    $(".card table tbody").append($tableRow);
};

$(document).ready(function() {
    var currentCardID = "#add-part";

    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    $(".to-card").click(function() {
        if ($(this).hasClass("btn-enabled")) {
            var target = $(this).attr("card");
            if(!(("#" + target) === currentCardID)) {
                currentCardID = toCard("#" + target, currentCardID);
            }
        }
    });

    $(".card .next").click(function() {
        if ($(this).hasClass("btn-enabled")) {
            var target = $(this).parent().next().attr("id");
            currentCardID = toCard("#" + target, currentCardID);
        }
    });

    $(".card .back").click(function() {
        if ($(this).hasClass("btn-enabled")) {
            var target = $(this).parent().prev().attr("id");
            currentCardID = toCard("#" + target, currentCardID);
        }
    });

    $("#partNumberInput").on("change keyup paste", function() {
        if ($(this).val !== "") {
            $("#btn-add-part-next").addClass("btn-enabled");
            enableCard("edit-details");
        }
    });

    $("table tr:last-child").one("keydown paste", function() {
        addAttributeInput();
    });
});
