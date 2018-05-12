<?php
return array(
    /**
     * The timezone to be used by cybersource
     */
    'env' => 'test',
    /**
     * The timezone to be used by cybersource
     */
    'timezone' => 'America/Los_Angeles',
    /**
     * The organization ID when creating the cybersource account
     */
    'organization_id' => '',
    /**
     * The Endpoint to hit
     * Change between test and prod environments
     */
    'wsdl_endpoint' => 'https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.26.wsdl',
    /**
     * Probably not necessary - currently not being used
     */
    'outbound_merchant_id' => '',
    /**
     * The currency format
     */
    'currency' => 'USD',
    /**
     * Reports Endpoints
     * Change between test and prod environments
     */
    'reports' => array(
        'endpoint' => 'ebctest.cybersource.com/ebctest',
        'version' => '0.1',
        'api_version' =>  '2011-03',
        'username' => '',
        'password' => '',
    ),
    /**
     * Both the merchant and transaction IDs
     */
    'merchant_id' => '',
    'merchant_reference_code' => '',
    'transaction_id' => '',
    /**
     * Timeout for requests
     */
    'timeout' => '10',
    /**
     * Cybersource Username/Password info
     */
    'username' => '',
    'password' => '',
    /**
     * Translated result codes to be returned
     * as part of the CybersourceResponse
     */
    'result_codes' => [
        '100' => 'Successful transaction.',
        '101' => 'The request is missing one or more required fields.',
        '102' => 'One or more fields in the request contains invalid data.',
        '104' => 'The access key and transaction uuid fields for this authorization request matches the access_key and transaction_uuid of another authorization request that you sent within the past 15 minutes.',
        '110' => 'Only a partial amount was approved.',
        '150' => 'Error: General system failure.',
        '151' => 'Error: The request was received but there was a server timeout.',
        '152' => 'Error: The request was received, but a service did not finish running in time.',
        '200' => 'The authorization request was approved by the issuing bank but declined by CyberSource because it did not pass the Address Verification Service (AVS) check.',
        '201' => 'The issuing bank has questions about the request.',
        '202' => 'Expired card.',
        '203' => 'General decline of the card.',
        '204' => 'Insufficient funds in the account.',
        '205' => 'Stolen or lost card.',
        '207' => 'Issuing bank unavailable.',
        '208' => 'Inactive card or card not authorized for card-not-present transactions.',
        '209' => 'American Express Card Identification Digits (CID) did not match.',
        '210' => 'The card has reached the credit limit.',
        '211' => 'Invalid CVN.',
        '221' => 'The customer matched an entry on the processor\'s negative file.',
        '222' => 'Account frozen.',
        '230' => 'The authorization request was approved by the issuing bank but declined by CyberSource because it did not pass the CVN check.',
        '231' => 'Invalid credit card number.',
        '232' => 'The card type is not accepted by the payment processor.',
        '233' => 'General decline by the processor.',
        '234' => 'There is a problem with your CyberSource merchant configuration.',
        '235' => 'The requested amount exceeds the originally authorized amount.',
        '236' => 'Processor failure.',
        '237' => 'The authorization has already been reversed.',
        '238' => 'The authorization has already been captured.',
        '239' => 'The requested transaction amount must match the previous transaction amount.',
        '240' => 'The card type sent is invalid or does not correlate with the credit card number.',
        '241' => 'The request ID is invalid.',
        '242' => 'You requested a capture, but there is no corresponding, unused authorization record.',
        '243' => 'The transaction has already been settled or reversed.',
        '246' => 'The capture or credit is not voidable because the capture or credit information has laready been submitted to your processor. Or, you requested a void for a type of transaction that cannot be voided.',
        '247' => 'You requested a credit for a capture that was previously voided.',
        '250' => 'Error: The request was received, but there was a timeout at the payment processor.',
        '475' => 'The cardholder is enrolled for payer authentication.',
        '476' => 'Payer authentication could not be authenticated.',
        '520' => 'The authorization request was approved by the issuing bank but declined by CyberSource based on your Smart Authorization settings.',
    ],
);