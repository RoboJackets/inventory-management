var allowedChars = /^[\w-+=&' ]+$/;
var currentCardID = "#add-part";

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
    $(".card:first-child .next, .submit").addClass("fast-track");
    $("ol.steps li:last-child").addClass("fast-track");
    enableCard($(".card"));
};

function disableFastTrack() {
    $(".card:first-child .next, .submit").removeClass("fast-track");
    $("ol.steps li:last-child").removeClass("fast-track");
    disableCard($(".card").slice(2));
}

function addInputField(id, readOnly, key, value) {
    if (readOnly === undefined) readOnly = false;
    if (key === undefined) key = "";
    if (value === undefined) value = "";

    $newRow = $(id + " tbody tr:last-child").clone().find("input").val("").end();
    $newRow.find("input").attr("placeholder","");

    var $removeButton = $($.parseHTML('<span class="glyphicon glyphicon-remove"></span>'));

    $removeButton.click(function(){
        $(this).closest('tr').children('td').animate({ padding: 0 }).wrapInner('<div />').children().slideUp(200, function() { 
            $(this).closest('tr').remove(); 
            $(id + " table tbody tr").each(function(idx){
                $(this).children().first().text(idx + 1);
            });
        });
    });

    $newRow.find("input").on("change keyup paste focus", function() {
        if (id == "#add-attributes") {
            if (validateAddAttributes()) {
                enableCard($("#barcode"));
            } else {
                disableCard($("#barcode"));
            }
        } else if (id == "#barcode") {
            if (validateBarcode()) {
                $(".submit").addClass("btn-enabled");
            } else {
                $(".submit").removeClass("btn-enabled");
            }
        }
    });

    $newRow.find("td:last-child").append($removeButton);

    $newRow.find("td:nth-child(2) input").val(key);
    $newRow.find("td:nth-child(3) input").val(value);
    if (readOnly) $newRow.find("td:nth-child(2) input").attr({readonly: true, tabindex:-1});

    if (readOnly) {
        $(id + " tbody tr td:nth-child(2) input:not([readonly])").first().parents("tr").before($newRow);
    } else {
        $(id + " tbody tr:last-child").before($newRow);
    }

    $(id + " table tbody tr").each(function(idx){
        $(this).children().first().text(idx + 1);
    });
};

function addAttributes(fields) {
    $("#add-attributes table tbody tr td:nth-child(2) input[readonly]").parents("tr").find("span.glyphicon-remove").click();
    $.each(fields, function(index, value) {
        addInputField("#add-attributes", true, value);
    })
}

function validateEditDetails() {
    var partName = allowedChars.test($("#partNameInput").val())
    var category = $("#categoryInput").val() !== null;
    var description = /^[^'"\\]*$/.test($("#descriptionInput").val());
    var datasheet = /^[^'"\\\s]+$/.test($("#datasheetInput").val());
    var location = allowedChars.test($("#locationInput").val());

    if (partName || !$("#partNameInput").val()) {
        $("#partNameInput").parent().removeClass("has-error");
    } else {
        $("#partNameInput").parent().addClass("has-error");
    }

    if (description || !$("#descriptionInput").val()) {
        $("#descriptionInput").parent().removeClass("has-error");
    } else {
        $("#descriptionInput").parent().addClass("has-error");
    }

    if (datasheet || !$("#datasheetInput").val()) {
        $("#datasheetInput").parent().removeClass("has-error");
    } else {
        $("#datasheetInput").parent().addClass("has-error");
    }

    if (location || !$("#locationInput").val()) {
        $("#locationInput").parent().removeClass("has-error");
    } else {
        $("#locationInput").parent().addClass("has-error");
    }

    if (partName && category && description && datasheet && location) {
        enableCard($("#add-attributes"));
    } else {
        disableCard($("#add-attributes"));
    }
}

function validateAddAttributes() {
    var flag = true;
    $("#add-attributes table tbody tr input").parent().removeClass("has-error");
    $("#add-attributes table tbody tr").each(function(index, value) {
        $key = $(this).find("td:nth-child(2) input");
        $val = $(this).find("td:nth-child(3) input");
        if(($key.val() == "")?!($val.val() == ""):($val.val() == "")) { //XNOR
            flag = false;
        }
    });

    $("#add-attributes table tbody tr input").each(function(index, value) {
        if(!/^[^"\\]*$/.test($(this).val())) {
            $(this).parent().addClass("has-error");
            flag = false;
        }
    });
    return flag;
}

function validateBarcode() {
    var flag = true;
    $("#barcode table tbody tr input").parent().removeClass("has-error");
    $("#barcode table tbody tr").each(function(index, value) {
        $key = $(this).find("td:nth-child(2) input");
        $val = $(this).find("td:nth-child(3) input");
        if(($key.val() == "")?!($val.val() == ""):($val.val() == "")) { //XNOR
            flag = false;
        }
    });

    $("#barcode table tbody tr input").each(function(index, value) {
        if(!/^\d*$/.test($(this).val())) {
            $(this).parent().addClass("has-error");
            flag = false;
        }
    });
    return flag;
}

function Attribute(attribute, value, priority) {
    this.attribute = attribute;
    this.value = value;
    this.priority = priority;
}

function Bag(barcode, quantity) {
    this.barcode = barcode;
    this.quantity = quantity;
}

function Part(partNum, name, category, description, datasheet, location, bags, attributes) {
    this.part_num = partNum;
    this.name = name;
    this.category = category;
    this.description = description;
    this.datasheet = datasheet;
    this.location = location;
    this.bags = bags;
    this.attributes = attributes;
}

function submitData() {
    var attributes = [];
    var bags = [];
    
    $attributes = $("#add-attributes table tr:not(:last-child)");
    
    $attributes.each(function(index){
        attributes.push(new Attribute(
            $(this).find("td:nth-child(2) input").val(),
            $(this).find("td:nth-child(3) input").val(),              
            $(this).children("td:nth-child(1)").text()
                     ));
    });
    
    $bags = $("#barcode table tr:not(:last-child)");

    $bags.each(function(index){
        bags.push(new Bag(
            $(this).find("td:nth-child(2) input").val(),
            $(this).find("td:nth-child(3) input").val(),              
        ));
    });

    
    var part = new Part(
        $('#partNumberInput').val(),
        $('#partNameInput').val(),
        $('#categoryInput').val(),
        $('#descriptionInput').val(),
        $('#datasheetInput').val(),
        $('#locationInput').val(),
        bags,
        attributes
    );
    
    var parts = {
        "parts":[part]
    };
    
    var data = JSON.stringify(parts);
    $.post("add/submit", data, "json")
    .done(function(xhr){
        $("#toast-alert").show(); //This should be replaced with a more generic toast function
        resetPage();
    })
    .fail(function(xhr){
        alert("Data could not be submitted. Error code: " + xhr.status);
        console.log(result);
    });
}

function resetPage(){
    $("#categoryInput").val("");
    $("card table tr:not(:last-child) td span").click();
    $("input").val("");
    disableCard($(".card").slice(1));
    currentCardID = toCard("#add-part","#barcode");
}

$(document).ready(function() {
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
            var target = "barcode";
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
            $.post("add/validate-pn", query, function(result) {
                result = $.parseJSON(result);
                if (result) {
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
            if (!$(this).val()) {
                $("#partNumberInput").parent().removeClass("has-error");
            } else {
                $("#partNumberInput").parent().addClass("has-error");
            }
        }
    });

    $("#add-attributes tbody tr:last-child input").on("focus", function() {
        addInputField("#add-attributes");
        var index = $(this).parent().index() + 1;
        $(this).parents("tr").prev().find("td:nth-child(" + index + ") input").focus();
    });
    
    $("#barcode tbody tr:last-child input").on("focus", function() {
        addInputField("#barcode");
        var index = $(this).parent().index() + 1;
        $(this).parents("tr").prev().find("td:nth-child(" + index + ") input").focus();
    });

    $("#edit-details input, #edit-details select").on("change keyup paste", function() {
        if (validateEditDetails()) {
            enableCard($("#add-attributes"));
        } else {
            disableCard($(".card").slice(3));
        }
    });

    $("#edit-details select").change(function() {
        var attributes = {
            capacitor: [
                "Type",
                "Capacitance",
                "Tolerance",
                "Voltage Rating",
                "Series",
                "Package",
                "Mounting Type"],
            connector: [
                "Pitch",
                "Series",
                "Number of Positions",
                "Number of Rows",
                "Connector Type",
                "Contact Type",
                "Mounting Type",
                "Package"],
            diode: [
                "Type",
                "Series",
                "Direction",
                "Channels",
                "Voltage Forward",
                "Voltage Reverse",
                "Mounting Type",
                "Package"],
            ic: [
                "Type",
                "Series",
                "Package",
                "Mounting Type"],
            inductor: [
                "Type",
                "Series",
                "Inductance",
                "Tolerance",
                "Shielding",
                "Current Rating",
                "Mounting Type",
                "Package"],
            oscillator: [
                "Frequency",
                "Type",
                "Series",
                "Function",
                "VS",
                "Current Supply",
                "Mounting Type",
                "Package"],
            resistor: [
                "Resistance",
                "Tolerance",
                "Series",
                "Power",
                "Channels",
                "Mounting Type",
                "Package"],
            other: [
                "Part Type",
                "Mounting Type",
                "Series",
                "Package"]
        };
        addAttributes(attributes[$(this).val()]);
    });

    $(".card form").find("input:last").keydown(function(event){
        if(event.keyCode == 13 || event.keyCode == 9) {
            $(this).parents(".card").find(".next").click();
            event.preventDefault();
            return false;
        }
    });

    $("#barcodeInput").on("change keyup paste", function() {
        if (allowedChars.test($(this).val())) {
            $(".submit").addClass("btn-enabled");
        } else {
            $(".submit").removeClass("btn-enabled");
        }
    });
    
    $("#btn-confirm-submit").click(function(){
        if($(this).hasClass("btn-enabled")){
            submitData();
        }
    });
    
});