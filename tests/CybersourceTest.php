<?php

use JustGeeky\LaravelCybersource\Cybersource;
use JustGeeky\LaravelCybersource\Models\CybersourceSOAPModel;
use LaravelCybersource\TestCase;
use \Mockery as m;

class CybersourceTest extends TestCase {

    private $mockRequester;
    /**
     * @var Cybersource
     */
    private $cybersource;

    public function setUp()
    {
        parent::setUp();
        $this->mockRequester = m::mock('soapRequester');
        $this->cybersource = new Cybersource($this->mockRequester);
    }

    public function testCreateNewRequest()
    {
        $model = $this->cybersource->createNewRequest();

        $this->assertNotNull($model);
        $this->assertInstanceOf('JustGeeky\LaravelCybersource\Models\CybersourceSOAPModel', $model);
    }

    public function testCreateSubscriptionStatusRequest()
    {
        $request = $this->cybersource->createSubscriptionStatusRequest('123');

        $this->assertInstanceOf('JustGeeky\LaravelCybersource\Models\CybersourceSOAPModel', $request);
        $this->assertNotNull($request->clientEnvironment);
        $this->assertNotNull($request->merchantID);

        $this->assertEquals('true', $request->paySubscriptionRetrieveService->run);
        $this->assertEquals('123', $request->recurringSubscriptionInfo->subscriptionID);
    }

    public function testSendRequestReturnsCybersourceResponse()
    {
        $request = $this->cybersource->createSubscriptionStatusRequest('123');

        $model = new CybersourceSOAPModel();
        $model->reasonCode = 100;
        $model->decision = 'ACCEPT';
        $this->mockRequester->shouldReceive('send')->andReturn($model);

        $response = $this->cybersource->sendRequest($request);
        $requestData = $response->getRequestData();

        $this->assertInstanceOf('JustGeeky\LaravelCybersource\Models\CybersourceResponse', $response);
        $this->assertEquals('123', $requestData['recurringSubscriptionInfo']['subscriptionID']);
        $this->assertTrue($response->isValid());
    }

    public function testCreateNewSubscriptionRequestCreatesProperRequest()
    {
        $paymentToken = 'test123';
        $productTitle = 'Test Title';
        $amount = 100.00;

        $request = $this->cybersource->createNewSubscriptionRequest($paymentToken, $productTitle, $amount);

        $startDate = $request->recurringSubscriptionInfo->startDate;
        $autoRenew = $request->recurringSubscriptionInfo->automaticRenew;
        $frequency = $request->recurringSubscriptionInfo->frequency;

        $this->assertEquals($paymentToken, $request->paySubscriptionCreateService->paymentRequestID);
        $this->assertEquals($productTitle, $request->subscription->title);
        $this->assertEquals($amount, $request->recurringSubscriptionInfo->amount);
        $this->assertEquals('weekly', $frequency);
        $this->assertEquals('true', $autoRenew);
        $this->assertEquals(date('Ymd'), $startDate);
    }

    public function testCreateNewSubscriptionWithMerchantReferenceNumber()
    {
        $paymentToken = 'test123';
        $productTitle = 'Test Title';
        $amount = 100.00;

        $request = $this->cybersource->createNewSubscriptionRequest($paymentToken, $productTitle,
            $amount, 'weekly', 'true', null, 'merchant_ref_num');

        $startDate = $request->recurringSubscriptionInfo->startDate;
        $autoRenew = $request->recurringSubscriptionInfo->automaticRenew;
        $frequency = $request->recurringSubscriptionInfo->frequency;

        $this->assertEquals('merchant_ref_num', $request->merchantReferenceCode);
        $this->assertEquals($paymentToken, $request->paySubscriptionCreateService->paymentRequestID);
        $this->assertEquals($productTitle, $request->subscription->title);
        $this->assertEquals($amount, $request->recurringSubscriptionInfo->amount);
        $this->assertEquals('weekly', $frequency);
        $this->assertEquals('true', $autoRenew);
        $this->assertEquals(date('Ymd'), $startDate);
    }

    public function testCreateUpdateSubscriptionRequest()
    {
        $paymentToken = 'test123';
        $subscriptionId = 'testingSubs';

        $request = $this->cybersource->createUpdateSubscriptionRequest($subscriptionId, $paymentToken);

        $this->assertEquals('true', $request->paySubscriptionUpdateService->run);
        $this->assertEquals($paymentToken, $request->paySubscriptionUpdateService->paymentRequestID);
        $this->assertEquals($subscriptionId, $request->recurringSubscriptionInfo->subscriptionID);
    }

    public function testCreateCancelSubscriptionRequest()
    {
        $subId = 'testing123';

        $request = $this->cybersource->createCancelSubscriptionRequest($subId);

        $this->assertEquals('true', $request->paySubscriptionUpdateService->run);
        $this->assertEquals($subId, $request->recurringSubscriptionInfo->subscriptionID);
        $this->assertEquals('cancel', $request->recurringSubscriptionInfo->status);
    }

    public function testCreateOneTimeChargeRequest()
    {
        $amt = 100.00;
        $pmtToken = 'test-token';

        $request = $this->cybersource->createOneTimeChargeRequest($amt, $pmtToken);

        $startDate = $request->recurringSubscriptionInfo->startDate;
        $autoRenew = $request->recurringSubscriptionInfo->automaticRenew;
        $frequency = $request->recurringSubscriptionInfo->frequency;

        $this->assertEquals($pmtToken, $request->paySubscriptionCreateService->paymentRequestID);
        $this->assertEquals('one-time-charge', $request->subscription->title);
        $this->assertEquals($amt, $request->recurringSubscriptionInfo->amount);
        $this->assertEquals('on-demand', $frequency);
        $this->assertEquals('false', $autoRenew);
        $this->assertEquals(date('Ymd'), $startDate);
    }

    public function testCreateRefundRequest()
    {
        $requestId = 'test123';
        $currency = 'USD';
        $total = 100.00;

        $request = $this->cybersource->createRefundRequest($requestId, $currency, $total);


        $this->assertEquals('true', $request->ccCreditService->run);
        $this->assertEquals($requestId, $request->ccCreditService->captureRequestID);
        $this->assertEquals($currency, $request->purchaseTotals->currency);
        $this->assertEquals($total, $request->purchaseTotals->grandTotalAmount);
    }

}
