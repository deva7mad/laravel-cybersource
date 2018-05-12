$(function() {
    payment_form = $('form').attr('id');
    addLinkToSetDefaults();
});

function setDefaultsForAll() {
    if (payment_form === "payment_confirmation"){
        setDefaultsForUnsignedDetailsSection();
    } else {
        setDefaultsForPaymentDetailsSection();
    } 
}

function addLinkToSetDefaults() {
    $(".section").prev().each(function (i) {
        legendText = $(this).text();
        $(this).text("");

        var setDefaultMethod = "setDefaultsFor" + capitalize($(this).next().attr("id")) + "()";

        newlink = $(document.createElement("a"));
        newlink.attr({
            id:'link-' + i, name:'link' + i, href:'#'
        });

        newlink.append(document.createTextNode(legendText));
        newlink.bind('click', function () {
            eval(setDefaultMethod);
        });

        $(this).append(newlink);
    });

    newbutton = $(document.createElement("input"));
    newbutton.attr({
        type:'button', id:'btn_defaultAll', value:'Default All', onClick:'setDefaultsForAll()'
    });

    newbutton.bind('click', function() {
        setDefaultsForAll;
    });

    $("#"+payment_form).append(newbutton);
}

function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function setDefaultsForPaymentDetailsSection() {

    var merchant_ref_no = 'B' + new Date().getTime();
    $("input[name='payment_method']").val("card");
    //$("input[name='transaction_type']").val("sale");
    $("input[name='reference_number']").val(merchant_ref_no);
    $("input[name='auth_trans_ref_no']").val(merchant_ref_no);
    $("input[name='recurring_amount']").val("1572.00");
    $("input[name='amount']").val("1572.00");
    $("input[name='currency']").val("THB");
    $("input[name='locale']").val("en-us");
    $("input[name='bill_to_forename']").val("Krungsri");
    $("input[name='bill_to_surname']").val("Simple");
    $("input[name='bill_to_email']").val("customer@mail.com");
    $("input[name='bill_to_phone']").val("+662-2962-000");
    $("input[name='bill_to_address_line1']").val("1222 Rama III Road");
    $("input[name='bill_to_address_line2']").val("Bang Phongphang");
    $("input[name='bill_to_address_city']").val("Yan Nawa");
    $("input[name='bill_to_address_state']").val("Bangkok");
    $("input[name='bill_to_address_country']").val("TH");
    $("input[name='bill_to_address_postal_code']").val("10120");
}

function setDefaultsForUnsignedDetailsSection(){
    $("input[name='card_type']").val("001");
    $("input[name='card_number']").val("4000000000000002");
    $("input[name='card_expiry_date']").val("02-2022");
    $("input[name='card_cvn']").val("111");
}

