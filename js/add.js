var allowedChars = /^[\w-+=& ]+$/;

function slideCard($card, direction, side) {
    if (direction === "in") {
        if (side === "right") {
            $card.switchClass("off-left", "off-right", 0);
            $card.removeClass("hidden");
            $card.removeClass("off-right", 300, "swing", function(){$card.find("input.focus").focus();});
        } else {
            $card.switchClass("off-right", "off-left", 0);
            $card.removeClass("hidden");
            $card.removeClass("off-left", 300, "swing", function(){$card.find("input.focus").focus();});
        }
    } else {
        if (side === "right") {
            $card.addClass("off-right", 300, "swing", function(){$card.addClass("hidden");});
        } else {
            $card.addClass("off-left", 300, "swing", function(){$card.addClass("hidden");});
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

function enableCard($cards) {
    $cards.each(function(index){
        var stepIndex = $(this).index() + 1;
        $("ol.steps li:nth-child(" + stepIndex + ")").addClass("btn-enabled");
    });

    $cards.prev(".card").children(".next").addClass("btn-enabled");
    $cards.next(".card").children(".back").addClass("btn-enabled");
};

function disableCard($cards) {  
    $cards.each(function(index){
        var stepIndex = $(this).index() + 1;
        $("ol.steps li:nth-child(" + stepIndex + ")").removeClass("btn-enabled");
    });

    $cards.prev(".card").children(".next").removeClass("btn-enabled");
    $cards.next(".card").children(".back").removeClass("btn-enabled");
};

function enableFastTrack() {
    $(".card:first-child .next").addClass("fast-track");
    $("ol.steps li:last-child").addClass("fast-track");
    enableCard($(".card"));
};

function disableFastTrack() {
    $(".card:first-child .next").removeClass("fast-track");
    $("ol.steps li:last-child").removeClass("fast-track");
    disableCard($(".card").slice(2));
};

function addAttributeInput() {
    var attrNum = $(".card table tbody tr:last-child").index() + 2;
    $newRow = $("#add-attributes tbody tr:last-child").clone().find("input").val("").end();
    $newRow.children("td:first-child").text(attrNum);

    var $removeButton = $($.parseHTML('<span class="glyphicon glyphicon-remove"></span>'));

    $(".card table tbody tr:last-child td input").attr("placeholder","");
    $(".card table tbody tr:last-child td input").off();

    $newRow.find("input").one("focus", function() {
        addAttributeInput();
    });
    $removeButton.click(function(){
        $(this).closest('tr').children('td').animate({ padding: 0 }).wrapInner('<div />').children().slideUp(200, function() { 
            $(this).closest('tr').remove(); 
            $(".card table tbody tr").each(function(idx){
                $(this).children().first().text(idx + 1);
            });
        });
    });

    $(".card table tbody tr:last-child td:last-child").append($removeButton);
    $(".card table tbody").append($newRow);
};

function validateEditDetails() {
    var partName = allowedChars.test($("#partNameInput").val());
    var category = $("#categoryInput").val() !== null;
    var description = /^[^'"\\]*$/.test($("#descriptionInput").val());
    var datasheet = /^[^'"\\\s]+$/.test($("#datasheetInput").val());
    var location = allowedChars.test($("#locationInput").val());

    if (partName && category && description && datasheet && location) {
        enableCard($("#add-attributes"));
    } else {
        disableCard($("#add-attributes"));
    }
}

$(document).ready(function() {
    var currentCardID = "#add-part";

    debug = "http://rj.str.at/";

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
        if ($(this).hasClass("fast-track")) {
            var target = "confirm";
            $(".card .next, ol.steps li").addClass("btn-enabled")
        } else {
            var target = $(this).parent().next().attr("id");
        }
        if ($(this).hasClass("btn-enabled")) {
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
        if (allowedChars.test($(this).val())) {
            enableCard($("#edit-details"));

            var query = {"partNumber":$(this).val()};
            $.post(debug + "add/validate-pn", query, function(result) {
                if (result === "true") {
                    $("#partNumberInput").parent().addClass("has-success");
                    enableFastTrack();
                    $("#partNumberInput").tooltip();
                } else {
                    $("#partNumberInput").parent().removeClass("has-success");
                    disableFastTrack();
                    $("#partNumberInput").tooltip('destroy');
                }
            });
        } else {
            disableCard($("#edit-details"));
        }
    });

    $("table tbody tr:last-child input").one("focus", function() {
        addAttributeInput();
    });

    $("#edit-details input, #edit-details select").on("change keyup paste", function() {
        if (validateEditDetails()) {
            enableCard($("#add-attributes"));
        } else {
            disableCard($(".card").slice(3));
        }
    });

    $(".card form").find("input:last").keydown(function(event){
        console.log("input keydown");
        if(event.keyCode == 13 || event.keyCode == 9) {
            $(this).parents(".card").find(".next").click();
            event.preventDefault();
            return false;
        }
    });
});
