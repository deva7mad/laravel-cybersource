<?php namespace LaravelCybersource;

use LaravelCybersource\TestCase;
use JustGeeky\LaravelCybersource\Models\CybersourceSOAPModel;

class CybersourceSOAPModelTest extends TestCase {

    public function testGetWorks()
    {
        $model = new CybersourceSOAPModel();
        $model->test = 'test';

        $this->assertEquals('test', $model->test);
    }

    public function testEmptyGetIsNull()
    {
        $model = new CybersourceSOAPModel();

        $this->assertNull($model->notExists);
    }

    public function testCreateNestedSOAPModel()
    {
        $model = $this->getCybersourceSOAPModel();
        $nested = new CybersourceSOAPModel();

        $model->nested = $nested;

        $this->assertEquals($nested, $model->nested);
        $this->assertNull($nested->clientEnvironment);
        $this->assertNull($nested->merchantID);
    }

    public function testToStdObjectCreatesValidObject()
    {
        $model = $this->getCybersourceSOAPModel();

        $obj = $model->toStdObject();

        $this->assertEquals($this->environment, $obj->clientEnvironment);
        $this->assertEquals('PHP', $obj->clientLibrary);
        $this->assertEquals(phpversion(), $obj->clientLibraryVersion);
        $this->assertEquals($this->merchantId, $obj->merchantID);
    }

    public function testToStdObjectWithNestedObjectsWorks()
    {
        $model = $this->getCybersourceSOAPModel();
        $nested = new CybersourceSOAPModel();

        $nested->run = 'true';

        $model->paySubscriptionRetrieveService = $nested;

        $obj = $model->toStdObject();

        $this->assertNotNull($obj->paySubscriptionRetrieveService);
        $this->assertInstanceOf('stdClass', $obj->paySubscriptionRetrieveService);
        $this->assertEquals('true', $obj->paySubscriptionRetrieveService->run);
    }

    public function testToArrayCreatesArray()
    {
        $model = $this->getCybersourceSOAPModel();
        $nested = new CybersourceSOAPModel();
        $nested->run = 'true';
        $model->paySubscriptionRetrieveService = $nested;

        $array = $model->toArray();

        $this->assertArrayHasKey('clientLibrary', $array);
        $this->assertArrayHasKey('clientLibraryVersion', $array);
        $this->assertArrayHasKey('clientEnvironment', $array);
        $this->assertArrayHasKey('merchantID', $array);
    }

    private function getCybersourceSOAPModel()
    {
        return new CybersourceSOAPModel('PHP', phpversion(), $this->environment, $this->merchantId);
    }

} 