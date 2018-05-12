<?php
$response_page = route('cybersource.payment.response');
$cancel_page = route('cybersource.payment.cancel');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Secure Acceptance - Payment Form</title>
    <link rel="stylesheet" type="text/css" href="{{url('cybersource/assets/css/payment.css')}}"/>
</head>
<body>
    <img src="{{url('cybersource/assets/img/logo-cybersource.png')}}" style="padding-bottom: 10px;" />

<h2>Payment Form</h2>

<form id="payment_form" action="{{route('secure.payment.confirm')}}" method="post">
    <input type="hidden" name="profile_id" value="<?php echo PROFILE_ID; ?>">
    <input type="hidden" name="access_key" value="<?php echo ACCESS_KEY; ?>">
    <input type="hidden" name="transaction_uuid" value="<?php echo uniqid(); ?>">
    <input type="hidden" name="signed_date_time" value="<?php echo gmdate("Y-m-d\TH:i:s\Z"); ?>">

    <input type="hidden" name="signed_field_names" value="profile_id,access_key,transaction_uuid,signed_field_names,unsigned_field_names,signed_date_time,locale,transaction_type,reference_number,auth_trans_ref_no,amount,currency,merchant_descriptor,override_custom_cancel_page,override_custom_receipt_page">
    
    <input type="hidden" name="unsigned_field_names" value="device_fingerprint_id,signature,bill_to_forename,bill_to_surname,bill_to_email,bill_to_phone,bill_to_address_line1,bill_to_address_line2,bill_to_address_city,bill_to_address_state,bill_to_address_country,bill_to_address_postal_code,customer_ip_address,line_item_count,item_0_code,item_0_sku,item_0_name,item_0_quantity,item_0_unit_price,item_1_code,item_1_sku,item_1_name,item_1_quantity,item_1_unit_price,merchant_defined_data1,merchant_defined_data2,merchant_defined_data3,merchant_defined_data4">
    <fieldset>
        <legend>Payment Details</legend>
        <div id="paymentDetailsSection" class="section">

            <span>transaction_type:</span>            <input type="text" name="transaction_type" value="sale"><br/>
            <span>create_token:</span>                <input type="checkbox" id="create_token" onclick="createToken(this)"><br/>
            <span>reference_number:</span>            <input type="text" name="reference_number"><br/>
            <span><b>auth_trans_ref_no</b>:</span>    <input type="text" name="auth_trans_ref_no"><br/>
            <span>amount:</span>                      <input type="text" name="amount"><br/>
            <span>currency:</span>                    <input type="text" name="currency"><br/>
            <span>locale:</span>                      <input type="text" name="locale"> (en-us, th-th, ja-jp)<br/>
            <span>merchant_descriptor:</span>         <input type="text" name="merchant_descriptor" value="Krungsri">
    </fieldset>
    <p>
    <fieldset>
        <legend>Unsigned Data Fields</legend>
        The name of each unsigned field should be included in the unsigned_field_names.
            <h3>Billing Information</h3>
            <span>bill_to_forename:</span>            <input type="text" name="bill_to_forename"><br/>
            <span>bill_to_surname:</span>             <input type="text" name="bill_to_surname"><br/>
            <span>bill_to_email:</span>               <input type="text" name="bill_to_email"><br/>
            <span>bill_to_phone:</span>               <input type="text" name="bill_to_phone"><br/>
            <span>bill_to_address_line1:</span>       <input type="text" name="bill_to_address_line1" maxlength="60"><br/>
            <span>bill_to_address_line2:</span>       <input type="text" name="bill_to_address_line2" maxlength="60"><br/>
            <span>bill_to_address_city:</span>        <input type="text" name="bill_to_address_city"><br/>
            <span>bill_to_address_state:</span>       <input type="text" name="bill_to_address_state"><br/>
            <span>bill_to_address_country:</span>     <input type="text" name="bill_to_address_country"><br/>
            <span>bill_to_address_postal_code:</span> <input type="text" name="bill_to_address_postal_code"><br/>

        </div>
    </fieldset>

    <input type="hidden" name="override_custom_cancel_page" value="<?php echo $response_page; ?>">
    <input type="hidden" name="override_custom_receipt_page" value="<?php echo $response_page; ?>">

    <!-- MDD START -->
    <input type="hidden" name="customer_ip_address" value="<?php echo @$_SERVER['REMOTE_ADDR']; ?>">

    <input type="hidden" name="line_item_count" value="2"/>

    <input type="hidden" name="item_0_sku" value="sku001"/>
    <input type="hidden" name="item_0_code" value="KFLTFDIV">
    <input type="hidden" name="item_0_name" value="KFLTFDIV">
    <input type="hidden" name="item_0_quantity" value="100">
    <input type="hidden" name="item_0_unit_price" value="15.72">

    <input type="hidden" name="item_1_sku" value="sku002"/>
    <input type="hidden" name="item_1_code" value="KFLTFD70">
    <input type="hidden" name="item_1_name" value="KFLTFD70">
    <input type="hidden" name="item_1_quantity" value="100">
    <input type="hidden" name="item_1_unit_price" value="10.00">

    <input type="hidden" name="merchant_defined_data1" value="MDD#1">
    <input type="hidden" name="merchant_defined_data2" value="MDD#2">
    <input type="hidden" name="merchant_defined_data3" value="MDD#3">
    <input type="hidden" name="merchant_defined_data4" value="MDD#4">
    <!-- MDD END -->
    <input type="submit" id="btn_submit" value="Submit"/>

<script type="text/javascript" src="{{url('cybersource/assets/js/jquery-1.7.min.js')}}"></script>
<script type="text/javascript" src="{{url('cybersource/assets/js/payment_form.js')}}"></script>
    <script type="text/javascript">
        
        function createToken(create_token) {

            var type = 'sale';

            if (create_token.checked) {
                type += ',create_payment_token';
            }

            $("input[name='transaction_type']").val(type);
        }
    </script>

</form>

</body>
</html>
