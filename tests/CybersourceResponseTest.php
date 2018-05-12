<?php namespace LaravelCybersource;

use JustGeeky\LaravelCybersource\Models\CybersourceResponse;
use JustGeeky\LaravelCybersource\Models\CybersourceSOAPModel;
use \Mockery as m;

class CybersourceResponseTest extends TestCase {

    public function testResponseDoesNotBreak()
    {
        $model = $this->createModel(101, 'REJECT');
        $responseObj = new CybersourceResponse($model, $this->configs->getResultCodes());

        $this->assertFalse($responseObj->isValid());
        $this->assertNotEmpty($responseObj->getDetails());
    }

    public function testConstructionSetsValidity()
    {
        $model = $this->createModel(100, 'ACCEPT');
        $responseObj = new CybersourceResponse($model, $this->configs->getResultCodes());

        $this->assertTrue($responseObj->isValid());
    }

    public function testConstructionAcceptsArray()
    {
        $model = $this->createModel(100, 'ACCEPT');
        $array = $model->toArray();

        $responseObj = new CybersourceResponse($array, $this->configs->getResultCodes());

        $this->assertTrue($responseObj->isValid());
    }

    public function testEmptyCodeThrowsError()
    {
        $this->setExpectedException('JustGeeky\LaravelCybersource\Exceptions\CybersourceException', 'Response Code Not Provided');
        $model = $this->createModel(null, null);
        $responseObj = new CybersourceResponse($model, $this->configs->getResultCodes());
    }

    public function testEmptyDecisionThrowsError()
    {
        $this->setExpectedException('JustGeeky\LaravelCybersource\Exceptions\CybersourceException', 'Decision Not Provided');
        $model = $this->createModel(100, null);
        $responseObj = new CybersourceResponse($model, $this->configs->getResultCodes());
    }

    public function testInvalidCodeThrowsError()
    {
        $this->setExpectedException('JustGeeky\LaravelCybersource\Exceptions\CybersourceException', 'Invalid Response Code Provided');
        $model = $this->createModel(0, 'ACCEPT');
        $responseObj = new CybersourceResponse($model, $this->configs->getResultCodes());
    }

    public function testErrorIsSet() {
        $model = $this->createModel(102, 'REJECT');
        $responseObj = new CybersourceResponse($model, $this->configs->getResultCodes());

        $error = $responseObj->error();

        $this->assertNotEmpty($error);
    }

    public function testErrorIsNotSet() {
        $model = $this->createModel(100, 'ACCEPT');
        $responseObj = new CybersourceResponse($model, $this->configs->getResultCodes());

        $this->assertFalse($responseObj->error());
    }

    public function testSetRequestWithArray()
    {
        $array = array('value' => 'test', 'other-value' => 'other-test');
        $model = $this->createModel(100, 'ACCEPT');

        $responseObj = new CybersourceResponse($model, $this->configs->getResultCodes());
        $responseObj->setRequest($array);

        $returnedArray = $responseObj->getRequestData();

        $this->assertEquals($array['value'], $returnedArray['value']);
        $this->assertEquals($array['other-value'], $returnedArray['other-value']);
    }

    public function testSetRequestWithSOAPModel()
    {
        $requestObj = new CybersourceSOAPModel(
            'PHP', phpversion(),
            'test', 'test-id', 'test-merchant-code'
        );

        $model = $this->createModel('100', 'ACCEPT');
        $responseObj = new CybersourceResponse($model, $this->configs->getResultCodes());
        $responseObj->setRequest($requestObj);
        $requestArray = $responseObj->getRequestData();

        $this->assertEquals('PHP', $requestArray['clientLibrary']);
        $this->assertEquals(phpversion(), $requestArray['clientLibraryVersion']);
    }

    private function createModel($code, $decision)
    {
        $model = new CybersourceSOAPModel();
        $model->reasonCode = $code;
        $model->decision = $decision;

        return $model;
    }

}