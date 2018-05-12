<?php

namespace JustGeeky\LaravelCybersource;

use JustGeeky\LaravelCybersource\Exceptions\CybersourceException;
use JustGeeky\LaravelCybersource\Models\CybersourceResponse;
use JustGeeky\LaravelCybersource\Models\CybersourceSOAPModel;
use JustGeeky\LaravelCybersource\Configs\Factory as ConfigsFactory;

class Cybersource {

    /**
     * @var SOAPRequester
     */

    /* ServerConfigs */
    protected $configs;

    private $requester;

    public $timeout = 10;

    public $billTo = array();

    public $card = array();

    public $items = array();

    public $avsCodes = array(
    	'A' => 'Partial match: Street address matches, but 5-digit and 9-digit postal codes do not match.',
    	'B' => 'Partial match: Street address matches, but postal code is not verified.',
    	'C' => 'No match: Street address and postal code do not match.',
    	'D' => 'Match: Street address and postal code match.',
    	'E' => 'Invalid: AVS data is invalid or AVS is not allowed for this card type.',
    	'F' => 'Partial match: Card member\'s name does not match, but billing postal code matches.',
    	'G' => 'Not supported: Non-U.S. issuing bank does not support AVS.',
    	'H' => 'Partial match: Card member\'s name does not match, but street address and postal code match.',
    	'I' => 'No match: Address not verified.',
    	'K' => 'Partial match: Card member\'s name matches, but billing address and billing postal code do not match.',
    	'L' => 'Partial match: Card member\'s name and billing postal code match, but billing address does not match.',
    	'M' => 'Match: Street address and postal code match.',
    	'N' => 'No match: Card member\'s name, street address, or postal code do not match.',
    	'O' => 'Partial match: Card member\'s name and billing address match, but billing postal code does not match.',
    	'P' => 'Partial match: Postal code matches, but street address not verified.',
    	'R' => 'System unavailable.',
    	'S' => 'Not supported: U.S. issuing bank does not support AVS.',
    	'T' => 'Partial match: Card member\'s name does not match, but street address matches.',
    	'U' => 'System unavailable: Address information is unavailable because either the U.S. bank does not support non-U.S. AVS or AVS in a U.S. bank is not functioning properly.',
    	'V' => 'Match: Card member\'s name, billing address, and billing postal code match.',
    	'W' => 'Partial match: Street address does not match, but 9-digit postal code matches.',
    	'X' => 'Match: Street address and 9-digit postal code match.',
    	'Y' => 'Match: Street address and 5-digit postal code match.',
    	'Z' => 'Partial match: Street address does not match, but 5-digit postal code matches.',
    	'1' => 'Not supported: AVS is not supported for this processor or card type.',
    	'2' => 'Unrecognized: The processor returned an unrecognized value for the AVS response.',
    );

    public $cvnCodes = array(
    	'D' => 'The transaction was determined to be suspicious by the issuing bank.',
    	'I' => 'The CVN failed the processor\'s data validation check.',
    	'M' => 'The CVN matched.',
    	'N' => 'The CVN did not match.',
    	'P' => 'The CVN was not processed by the processor for an unspecified reason.',
    	'S' => 'The CVN is on the card but waqs not included in the request.',
    	'U' => 'Card verification is not supported by the issuing bank.',
    	'X' => 'Card verification is not supported by the card association.',
    	'1' => 'Card verification is not supported for this processor or card type.',
    	'2' => 'An unrecognized result code was returned by the processor for the card verification response.',
    	'3' => 'No result code was returned by the processor.',
    );

    public $cardTypes = array(
    	'Visa' => '001',
    	'MasterCard' => '002',
    	'American Express' => '003',
    	'Discover' => '004',
    	'Diners Club' => '005',
    	'Carte Blanche' => '006',
    	'JCB' => '007',
    );

    public $testCards = array(
    	'amex' => '378282246310005',
    	'discover' => '6011111111111117',
    	'mastercard' => '5555555555554444',
    	'visa' => '4111111111111111',
    );

    private $report_types = array(
    	'payment_submission_detail' 	=> 'PaymentSubmissionDetailReport',
    	'subscription_detail' 			=> 'SubscriptionDetailReport',
    	'transaction_detail' 			=> 'TransactionDetailReport',
    	'transaction_exception_detail' 	=> 'TransactionExceptionDetailReport',
    );


    public function __construct($requester)
    {
    	$this->requester = $requester;
    	$this->configs = (new ConfigsFactory())->getFromConfigFile();
    }

    // @codeCoverageIgnoreStart
    /**
     * @param $subscriptionId
     * @return \Credibility\LaravelCybersource\models\CybersourceResponse
     */
    public function getSubscriptionStatus($subscriptionId)
    {
    	$request = $this->createSubscriptionStatusRequest($subscriptionId);
    	return $this->sendRequest($request);
    }

    /**
     * @param $paymentToken
     * @param $productTitle
     * @param $amount
     * @param $frequency
     * @param bool $autoRenew
     * @param null $startDate
     * @param null $merchantReferenceNumber
     * @param $currency
     * @return \Credibility\LaravelCybersource\models\CybersourceResponse
     */
    public function createSubscription($paymentToken, $productTitle, $amount, $frequency, $autoRenew = true, $startDate = null, $merchantReferenceNumber = null, $currency = null)
    {
    	$request = $this->createNewSubscriptionRequest($paymentToken, $productTitle,
    		$amount, $frequency, $autoRenew, $startDate, $merchantReferenceNumber, $currency
    	);
    	return $this->sendRequest($request);
    }

    /**
     * @param string $requestId The request ID received from an AuthReply statement, if applicable.
     * @param boolean|null $autoAuthorize Set to false to enable the disableAutoAuth flag to avoid an authorization and simply store the card. The default (null) means to omit the value, which means it'll use the setting on the account. Set to true to force an authorization, whether the account requires it or not.
     * @param string|null $recurringSubscriptionInfo specify that this is an on-demand subscription, it should not auto-bill
     * @return \Credibility\LaravelCybersource\models\CybersourceResponse
     */

    public function createCardSubscription($requestId = null, $autoAuthorize = null, $recurringSubscriptionInfo = null, $currency = null )
    {
    	$request = $this->createNewCardSubscriptionRequest($requestId, $autoAuthorize, $recurringSubscriptionInfo, $currency);
    	return $this->sendRequest($request);
    }

    /**
     * @param $subscriptionId
     * @param $paymentToken
     * @param $currency
     * @return \Credibility\LaravelCybersource\models\CybersourceResponse
     */
    public function updateSubscription($subscriptionId, $paymentToken, $currency = null)
    {
    	$request = $this->createUpdateSubscriptionRequest($subscriptionId, $paymentToken, $currency);
    	return $this->sendRequest($request);
    }
    /**
     * @param $subscriptionId
     * @param $updateCard
     * @param $currency
     * @return \Credibility\LaravelCybersource\models\CybersourceResponse
     */
    public function updateCardSubscription($subscriptionId, $updateCard, $currency = null)
    {
    	$request = $this->createUpdateCardSubscriptionRequest($subscriptionId, $updateCard, $currency);
    	return $this->sendRequest($request);
    }

    /**
     * @param $subscriptionId
     * @return \Credibility\LaravelCybersource\models\CybersourceResponse
     */
    public function cancelSubscription($subscriptionId)
    {
    	$request = $this->createCancelSubscriptionRequest($subscriptionId);
    	return $this->sendRequest($request);
    }

    /**
     * @param $subscriptionId
     * @return \Credibility\LaravelCybersource\models\CybersourceResponse
     */
    public function deleteSubscription($subscriptionId)
    {
    	$request = $this->createDeleteSubscriptionRequest($subscriptionId);
    	return $this->sendRequest($request);
    }


    /**
     * @param $requestId
     * @return \Credibility\LaravelCybersource\models\CybersourceResponse
     */
    public function voidTransaction($requestId)
    {
    	$request = $this->createVoidTransationRequest($requestId);
    	return $this->sendRequest($request);
    }
    
    /**
     * @param $requestId
     * @param $amount
     * @return \Credibility\LaravelCybersource\models\CybersourceResponse
     */
    public function captureTransaction($requestId, $amount)
    {
    	$request = $this->createCaptureTransationRequest($requestId, $amount);
    	return $this->sendRequest($request);
    }

    /**
     * @param $amount
     * @param $currency
     * @return \Credibility\LaravelCybersource\models\CybersourceResponse
     */
    public function chargeCardOnce($amount = null, $currency = null)
    {
    	$request = $this->createChargeCardOnceRequest($amount, $currency);
    	return $this->sendRequest($request);
    }


    /**
     * Charge the given Subscription ID a certain amount.
     *
     * @param string $subscriptionId The CyberSource Subscription ID to charge.
     * @param float $amount The amount to charge.
     * @return stdClass The raw response object from the SOAPRequester endpoint
     */
    public function chargeCurrentSubscriptionOnce($subscriptionId, $amount = null, $currency = null)
    {
    	$request = $this->createChargeCurrentSubscriptionOnceRequest($subscriptionId, $amount, $currency);
    	return $this->sendRequest($request);
    }


    /**
     * @param $amount
     * @param $paymentToken
     * @return \Credibility\LaravelCybersource\models\CybersourceResponse
     */
    public function chargeOnce($amount, $paymentToken, $currency = null)
    {
    	$request = $this->createOneTimeChargeRequest($amount, $paymentToken, $currency);
    	return $this->sendRequest($request);
    }

    public function createOneTimeChargeRequest($amount, $paymentToken, $currency = null)
    {
    	$request = $this->createNewSubscriptionRequest(
    		$paymentToken, 'one-time-charge', $amount, 'on-demand', 'false', $currency);
    	return $request;
    }

    /**
     * @param $transactionId
     * @param $currency
     * @param $total
     * @return \Credibility\LaravelCybersource\models\CybersourceResponse
     */
    public function refund($transactionId, $currency = null, $total)
    {
    	$request = $this->createRefundRequest($transactionId, $currency, $total);
    	return $this->sendRequest($request);
    }



    // @codeCoverageIgnoreEnd




    public function createNewSubscriptionRequest($paymentToken, $productTitle, $amount,
    	$frequency = 'weekly', $autoRenew = 'true', $startDate = null,
    	$merchantReferenceNumber = null, $currency = null)
    {
    	$startDate = empty($startDate) ? $this->getTodaysDate() : $startDate;
    	$request = $this->createNewRequest($merchantReferenceNumber);

    	$paySubscriptionCreateService = new CybersourceSOAPModel();
    	$paySubscriptionCreateService->run = 'true';
    	$paySubscriptionCreateService->paymentRequestID = $paymentToken;

    	$subscription = new CybersourceSOAPModel();
    	$subscription->title = $productTitle;
    	$subscription->paymentMethod = 'credit card';

    	$recurringSubscriptionInfo = new CybersourceSOAPModel();
    	$recurringSubscriptionInfo->frequency = $frequency;
    	$recurringSubscriptionInfo->amount = $amount;
    	$recurringSubscriptionInfo->automaticRenew = $autoRenew;
    	$recurringSubscriptionInfo->startDate = $startDate;

    	if(is_null($currency)){
    		$currency = $this->configs->getCurrency();
    	}

    	$request->purchaseTotals = $this->createPurchaseTotals($currency);
    	$request->paySubscriptionCreateService = $paySubscriptionCreateService;
    	$request->recurringSubscriptionInfo = $recurringSubscriptionInfo;
    	$request->subscription = $subscription;

    	return $request;
    }

    public function createRefundRequest($requestId, $currency = null, $total)
    {
    	$request = $this->createNewRequest();

    	$ccCreditService = new CybersourceSOAPModel();
    	$ccCreditService->run = 'true';
    	$ccCreditService->captureRequestID = $requestId;

    	$request->purchaseTotals = $this->createPurchaseTotals($currency, $total);
    	$request->ccCreditService = $ccCreditService;

    	return $request;
    }

    /**
     * Create a new payment subscription, either by performing a $0 authorization check on the credit card or using a
     * pre-created request token from an authorization request that's already been performed.
     *
     * @param string $requestId The request ID received from an AuthReply statement, if applicable.
     * @param boolean|null $autoAuthorize Set to false to enable the disableAutoAuth flag to avoid an authorization and simply store the card. The default (null) means to omit the value, which means it'll use the setting on the account. Set to true to force an authorization, whether the account requires it or not.
     * @param string|null $recurringSubscriptionInfo specify that this is an on-demand subscription, it should not auto-bill
     */
    public function createNewCardSubscriptionRequest ( $requestId = null, $autoAuthorize = null, $recurringSubscriptionInfo = null, $currency = null ) {

    	$request = $this->createNewRequest();

    	$paySubscriptionCreateService = new CybersourceSOAPModel();
    	$paySubscriptionCreateService->run = 'true';

        // if there is a request token passed in, reference it
    	if ( $requestId != null ) {
    		$paySubscriptionCreateService->paymentRequestID = $requestId;
    	}
    	else {

    		if ( $autoAuthorize === false ) {
    			$paySubscriptionCreateService->disableAutoAuth = 'true';
    		}
    		else if ( $autoAuthorize === true ) {
    			$paySubscriptionCreateService->disableAutoAuth = 'false';
    		}

    	}

        // remove this block if error
    	$subscription = new CybersourceSOAPModel();
    	$subscription->paymentMethod = 'credit card';



    	if ( $recurringSubscriptionInfo == null ) {
            // specify that this is an on-demand subscription, it should not auto-bill
    		$recurringSubscriptionInfo = new CybersourceSOAPModel();
    		$recurringSubscriptionInfo->frequency = 'on-demand';
    	}


        // we only need to add billing info to the request if there is not a previous request token - otherwise it's contained in it
    	if ( $requestId == null ) {

            // add billing info to the request
    		$request->billTo = $this->createBillTo();

            // add credit card info to the request
    		$request->card = $this->createCard();

    	}

    	if(is_null($currency)){
    		$currency = $this->configs->getCurrency();
    	}

    	$request->purchaseTotals = $this->createPurchaseTotals($currency);
    	$request->paySubscriptionCreateService = $paySubscriptionCreateService;
    	$request->subscription = $subscription;
    	$request->recurringSubscriptionInfo = $recurringSubscriptionInfo;

    	return $request;

    }


    public function createSubscriptionStatusRequest($subscriptionId)
    {
    	$request = $this->createNewRequest();

    	$subscriptionRetrieveRequest = new CybersourceSOAPModel();
    	$subscriptionRetrieveRequest->run = 'true';

    	$request->paySubscriptionRetrieveService = $subscriptionRetrieveRequest;

    	$subscriptionInfo = new CybersourceSOAPModel();
    	$subscriptionInfo->subscriptionID = $subscriptionId;

    	$request->recurringSubscriptionInfo = $subscriptionInfo;

    	return $request;
    }

    public function createUpdateSubscriptionRequest($subscriptionId, $paymentToken, $currency = null)
    {
    	$request = $this->createNewRequest();

    	$subscriptionUpdateRequest = new CybersourceSOAPModel();
    	$subscriptionUpdateRequest->run = 'true';
    	$subscriptionUpdateRequest->paymentRequestID = $paymentToken;

    	$request->paySubscriptionUpdateService = $subscriptionUpdateRequest;

    	$subscriptionInfo = new CybersourceSOAPModel();
    	$subscriptionInfo->subscriptionID = $subscriptionId;

    	if(!is_null($currency)){
    		$request->purchaseTotals = $this->createPurchaseTotals($currency);
    	}

    	$request->recurringSubscriptionInfo = $subscriptionInfo;

    	return $request;
    }

    public function createUpdateCardSubscriptionRequest($subscriptionId, $updateCard = null, $currency = null)
    {
    	$request = $this->createNewRequest();

    	$subscriptionUpdateRequest = new CybersourceSOAPModel();
    	$subscriptionUpdateRequest->run = 'true';

    	$request->paySubscriptionUpdateService = $subscriptionUpdateRequest;

    	$subscriptionInfo = new CybersourceSOAPModel();
    	$subscriptionInfo->subscriptionID = $subscriptionId;

    	$request->recurringSubscriptionInfo = $subscriptionInfo;

    	$request->billTo = $this->createBillTo();

    	if(!is_null($currency)){
    		$request->purchaseTotals = $this->createPurchaseTotals($currency);
    	}

    	if($updateCard === true){
    		$request->card = $this->createCard();
    	}

    	return $request;
    }



    public function createCancelSubscriptionRequest($subscriptionId)
    {
    	$request = $this->createNewRequest();

    	$cancel = new CybersourceSOAPModel();
    	$cancel->run = 'true';

    	$subscriptionInfo = new CybersourceSOAPModel();
    	$subscriptionInfo->subscriptionID = $subscriptionId;
    	$subscriptionInfo->status = 'cancel';

    	$request->paySubscriptionUpdateService = $cancel;
    	$request->recurringSubscriptionInfo = $subscriptionInfo;

    	return $request;
    }

    /**
     * Void a request that has not yet been settled. If it's already settled, you'll have to do a credit instead.
     *
     * @param  string $request_id The Request ID of the operation you wish to void.
     * @return object The response object from CyberSource.
     */

    public function createVoidTransationRequest($requestId)
    {
    	$request = $this->createNewRequest();

    	$voidService = new CybersourceSOAPModel();
    	$voidService->run = 'true';
    	$voidService->voidRequestID = $requestId;

    	$request->voidService = $voidService;

    	return $request;
    }

    /**
     * Capture a request that has not yet been settled. If it's already settled.
     *
     * @param  string $request_id The Request ID of the operation you wish to capture.
     * @param  int    $amount The Total Amount of the operation that in the request.
     * @return object The response object from CyberSource.
     */
    public function createCaptureTransationRequest($requestId, $amount)
    {
    	$request = $this->createNewRequest();

        // and actually charge them
    	$ccCaptureService = new CybersourceSOAPModel();
    	$ccCaptureService->run = 'true';
    	$ccCaptureService->authRequestID = $requestId;

    	$purchaseTotals = new CybersourceSOAPModel();
    	$purchaseTotals->grandTotalAmount = $amount;

    	$request->purchaseTotals = $purchaseTotals;
    	$request->ccCaptureService = $ccCaptureService;

    	return $request;
    }


    public function createDeleteSubscriptionRequest($subscriptionId)
    {
    	$request = $this->createNewRequest();

    	$delete = new CybersourceSOAPModel();
    	$delete->run = 'true';

    	$subscriptionInfo = new CybersourceSOAPModel();
    	$subscriptionInfo->subscriptionID = $subscriptionId;


    	$request->paySubscriptionDeleteService = $delete;
    	$request->recurringSubscriptionInfo = $subscriptionInfo;

    	return $request;
    }


    public function createChargeCardOnceRequest($amount = null, $currency = null)
    {
    	$request = $this->createNewRequest();

        // we want to perform an authorization
    	$ccAuthService = new CybersourceSOAPModel();
        $ccAuthService->run = 'true';		// note that it's textual true so it doesn't get cast as an int

        // and actually charge them
        $ccCaptureService = new CybersourceSOAPModel();
        $ccCaptureService->run = 'true';


        $request->ccAuthService = $ccAuthService;
        $request->ccCaptureService = $ccCaptureService;

        // add billing info to the request
        $request->billTo = $this->createBillTo();

        // add credit card info to the request
        $request->card = $this->createCard();

        // if there was an amount or currency specified, just use it - otherwise add the individual items

        if(is_null($currency)){
        	$currency = $this->configs->getCurrency();
        }
        $request->purchaseTotals = $this->createPurchaseTotals($currency, $amount);

        if ( is_null($amount) ) {
        	$request->item = $this->createItems( $request );
        }


        return $request;
    }


    /**
     * Charge the given Subscription ID a certain amount.
     *
     * @param string $subscriptionId The CyberSource Subscription ID to charge.
     * @param float $amount The amount to charge.
     * @return stdClass The raw response object from the SOAPRequester endpoint
     */
    public function createChargeCurrentSubscriptionOnceRequest ( $subscriptionId, $amount = null, $currency = null ) {

    	$request = $this->createNewRequest();

        // we want to perform an authorization
    	$ccAuthService = new CybersourceSOAPModel();
        $ccAuthService->run = 'true';		// note that it's textual true so it doesn't get cast as an int
        $request->ccAuthService = $ccAuthService;

        // and actually charge them
        $ccCaptureService = new CybersourceSOAPModel();
        $ccCaptureService->run = 'true';
        $request->ccCaptureService = $ccCaptureService;

        // actually remember to add the subscription ID that we're billing... duh!
        $recurringSubscriptionInfo = new CybersourceSOAPModel();
        $recurringSubscriptionInfo->subscriptionID = $subscriptionId;
        $request->recurringSubscriptionInfo = $recurringSubscriptionInfo;

        if(!is_null($currency) || !is_null($amount)){
        	$request->purchaseTotals = $this->createPurchaseTotals($currency, $amount);
        }

        // if there was an amount or currency specified, just use it - otherwise add the individual items
        if ( is_null($amount) ) {
        	$request->item = $this->createItems( $request );
        }

        return $request;

    }


    public function createNewRequest($merchantReferenceNumber = null)
    {
    	if(is_null($merchantReferenceNumber)) {
    		$merchantReferenceNumber = $this->configs->getMerchantReferenceCode();
    	}
    	return new CybersourceSOAPModel(
    		'PHP', phpversion(),
    		$this->configs->getEnv(),
    		$this->configs->getMerchantId(),
    		$merchantReferenceNumber
    	);
    }

    public function sendRequest($request)
    {
    	$rawResponse = $this->requester->send($request);
    	$csResponse = new CybersourceResponse($rawResponse, $this->configs->getResultCodes());
    	$csResponse->setRequest($request);

    	return $csResponse;
    }

    // Reports
    // @codeCoverageIgnoreStart
    public function getSubscriptions($date)
    {
    	return $this->sendReportRequest('SubscriptionDetailReport', $date);
    }

    public function getPaymentSubmissions($date)
    {
    	return $this->sendReportRequest('PaymentSubmissionDetailReport', $date);
    }

    public function getTransactions($date)
    {
    	return $this->sendReportRequest('TransactionDetailReport', $date);
    }

    public function getTransactionException($date)
    {
    	return $this->sendReportRequest('TransactionExceptionDetailReport', $date);
    }

    /**
     * @param $report_name
     * @param $date
     * @return array
     * @throws Exceptions\CybersourceException
     */
    private function sendReportRequest($report_name, $date)
    {
    	$merchant_id = $this->configs->getMerchantId();

    	$reportsArray = $this->configs->getReports();
    	$endpoint = $reportsArray['endpoint'];
    	$username = $reportsArray['username'];
    	$password = $reportsArray['password'];

    	if ( !$date instanceof \DateTime ) {
    		$date = new \DateTime($date);
    	}

        // get the right host and substitute in our username and password for http basic authentication
    	$url =
    	'https://' .
    	$username . ':' .
    	$password . '@' .
    	$endpoint .
    	'/DownloadReport/' .
    	$date->format('Y/m/d/') .
    	$merchant_id . '/' .
    	$report_name . '.csv';

    	$result = file_get_contents( $url );

    	if ( $result === false ) {

            // this would be a lot easier if we could just have an error handler that throws exceptions, but here it is...
    		$error = error_get_last();

    		if ( isset( $error['message'] ) ) {

                // try to parse out the specific message, minus the function and crap
    			$message = $error['message'];

    			preg_match( '/failed to open stream: (.*)/', $message, $matches );

    			if ( isset( $matches[1] ) ) {
    				$message = $matches[1];
    			}

    			if ( strpos( $message, 'The report requested cannot be found on this server' ) !== false ) {
                    throw new CybersourceException( $message, 400 );		// code 400? it's an HTTP 400 error. get it?
                }
                else {
                    // we don't know exactly what type of error, throw a generic error
                	throw new CybersourceException( $message );
                }

            }

            // something happened, but we dont' know what - die!
            throw new CybersourceException();

        }

        // parse out the results
        // but first, remove the first line - it's a header
        $result = substr( $result, strpos( $result, "\n" ) + strlen( "\n" ) );

        $records = CybersourceHelper::str_getcsv($result);

        return $records;

    }

    // @codeCoverageIgnoreEnd

    private function getTodaysDate()
    {
    	date_default_timezone_set($this->configs->getTimezone());
    	return date('Ymd');
    }


    public function card( $number, $expirationMonth, $expirationYear, $cvnCode = null, $cardType = null ) {

    	$this->card = array(
    		'accountNumber' => $number,
    		'expirationMonth' => $expirationMonth,
    		'expirationYear' => $expirationYear,
    	);

        // if a cvn code was supplied, use it
        // note that cvIndicator is turned on automatically if we pass in a cvNumber
    	if ( $cvnCode != null ) {
    		$this->card['cvNumber'] = $cvnCode;
    	}

        // and if we specified a card type, use that too
    	if ( $cardType != null ) {
            // if the card type is numeric, we probably already specified the exact code, just use it
    		if ( is_numeric( $cardType ) ) {
    			$this->card['cardType'] = $cardType;
    		}
    		else {
                // otherwise, convert it from a textual name
    			$this->card['cardType'] = $this->cardTypes[ $cardType ];
    		}
    	}

    	return $this;

    }

    public function items( $items = array() ) {

    	foreach ( $items as $item )  {
    		$this->addItem( $item['price'], $item['quantity'] );
    	}

    	return $this;

    }

    public function addItem( $price, $quantity = 1, $additional_fields = array()) {

    	$item = array(
    		'unitPrice' => $price,
    		'quantity' => $quantity,
    	);

    	$item = array_merge( $item, $additional_fields);

    	$this->items[] = $item;

    	return $this;

    }



    /**
     * Factory-pattern method for setting the billing information for this charge.
     *
     * Required fields are:
     *	firstName
     *	lastName
     *	street1
     *	city
     *	state
     *	postalCode
     *	country
     *	email
     *
     * @param array $info An associative array of the fields to set. Note the required fields above.
     * @return \CyberSource The current object.
     * @throws InvalidArgumentException Thrown when a required field is not present in the $info array.
     */

    public function billTo( $info = array() ) {

    	$fields = array(
    		'firstName',
    		'lastName',
    		'street1',
    		'city',
    		'state',
    		'postalCode',
    		'country',
    		'email',
    	);

    	foreach ( $fields as $field ) {
    		if ( !isset( $info[ $field ] ) ) {
    			throw new \InvalidArgumentException( 'The bill to field ' . $field . ' is missing!' );
    		}
    	}

        // if no ip address was specified, assume it's the remote host
    	if ( !isset( $info['ipAddress'] ) ) {
    		$info['ipAddress'] = $this->getIp();
    	}

    	$this->billTo = $info;

    	return $this;

    }

    public function createPurchaseTotals($currency = null, $amount = null){
        // build the currency obj

    	$purchaseTotals = new CybersourceSOAPModel();

    	if(!is_null($currency)){
    		$purchaseTotals->currency = $currency;
    	}

    	if(!is_null($amount)){
    		$purchaseTotals->grandTotalAmount = $amount;
    	}

    	return $purchaseTotals;

    }

    protected function createItems( ) {

        // there is no container for items, which annoys me
    	$items = array();

    	$i = 0;
    	foreach ( $this->items as $item ) {
    		$itemObj = new CybersourceSOAPModel();
    		$itemObj->unitPrice = $item['unitPrice'];
    		$itemObj->quantity = $item['quantity'];
    		$itemObj->id = $i;

    		$items[] = $itemObj;

    		$i++;
    	}

    	return $items;

    }

    private function createBillTo( ) {

        // build the billTo class
    	$billTo = new CybersourceSOAPModel();

        // add all the bill_to fields
    	foreach ( $this->billTo as $k => $v ) {
    		$billTo->$k = $v;
    	}

    	return $billTo;

    }

    private function createCard( ) {

        // build the credit card class
    	$card = new CybersourceSOAPModel();

    	foreach ( $this->card as $k => $v ) {
    		$card->$k = $v;
    	}

    	return $card;

    }

    /**
     * Get the remote IP address, but try and take into account common proxy headers and the like.
     *
     * @return string The client's IP address or 0.0.0.0 if we couldn't find it.
     */
    private function getIp( ) {

    	$headers = array(
    		'HTTP_CLIENT_IP',
    		'HTTP_FORWARDED',
    		'HTTP_X_FORWARDED',
    		'HTTP_X_FORWARDED_FOR',
    		'REMOTE_ADDR',
    	);

    	foreach ( $headers as $header ) {
    		if ( isset( $_SERVER[ $header ] ) ) {
    			return $_SERVER[ $header ];
    		}
    	}

        // just in case none of them are set
    	return '0.0.0.0';

    }


    /**
     * Try to determine the type of card based on its number.
     *
     * @see http://www.cybersource.com/support_center/management/best_practices/view.php?page_id=416
     * @param int $card_number The credit card number
     * @return string|null The name of the card type or null if it wasn't matched.
     */
    public function getCardType( $card_number ) {

    	$digits = str_split( $card_number );

    	if ( strlen( $card_number ) == 15 && $digits[0] == 3 && ( $digits[1] == 4 || $digits[1] == 7 ) ) {
    		return 'American Express';
    	}
    	else if ( strlen( $card_number ) == 14 && $digits[0] == 3 && in_array( $digits[1], array( 0, 6, 8 ) ) ) {
            return 'Diners Club';		// also Carte Blanche - how the hell am i supposed to know?
        }
        else if ( strlen( $card_number ) == 16 && (
        	( substr( $card_number, 0, 8 ) >= 60110000 && substr( $card_number, 0, 8 ) <= 60119999 ) ||
        	( substr( $card_number, 0, 8 ) >= 65000000 && substr( $card_number, 0, 8 ) <= 65999999 ) ||
        	( substr( $card_number, 0, 8 ) >= 62212600 && substr( $card_number, 0, 8 ) <= 62292599 )
        ) ) {
        	return 'Discover';
        }
        else if ( strlen( $card_number ) == 15 && in_array( substr( $card_number, 0, 4 ), array( 2014, 2149 ) ) ) {
        	return 'enRoute';
        }
        else if ( strlen( $card_number ) == 16 && (
        	in_array( substr( $card_number, 0, 4 ), array( 3088, 3096, 3112, 3158, 3337 ) ) ||
        	( substr( $card_number, 0, 8 ) >= 35280000 && substr( $card_number, 0, 8 ) <= 35899999 )
        ) ) {
        	return 'JCB';
        }
        else if ( strlen( $card_number ) == 16 && $digits[0] == 5 && $digits[1] >= 1 && $digits[1] <= 5 ) {
        	return 'MasterCard';
        }
        else if ( ( strlen( $card_number ) == 13 || strlen( $card_number ) == 16 ) && $digits[0] == 4 ) {
        	return 'Visa';
        }

        // otherwise, we don't know
        return null;

    }



} 
