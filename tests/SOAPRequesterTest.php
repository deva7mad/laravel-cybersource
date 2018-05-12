<?php namespace LaravelCybersource;

use JustGeeky\LaravelCybersource\Exceptions\CybersourceConnectionException;
use JustGeeky\LaravelCybersource\Models\CybersourceSOAPModel;
use JustGeeky\LaravelCybersource\SOAPRequester;
use Exception;
use LaravelCybersource\TestCase;
use \Mockery as m;

class SOAPRequesterTest extends TestCase {

    /** @var SOAPRequester */
    private $soapRequester;
    /** @var  m\Mock */
    private $mockClient;
    /** @var  m\Mock */
    private $factory;

    public function setUp()
    {
        parent::setUp();
        $this->mockClient = m::mock('SOAPClient');
        $this->factory = m::mock('SOAPFactory');
        $this->soapRequester = new SOAPRequester($this->mockClient, $this->factory);
    }

    public function testConvertToModelCreatesCybersourceSOAPModel()
    {
        $obj = new \stdClass();
        $obj->requestID = '12345';
        $obj->decision = 'REJECT';

        $testModel = new CybersourceSOAPModel();
        $model = $this->soapRequester->convertToModel($testModel, $obj);

        $this->assertEquals('12345', $model->requestID);
        $this->assertEquals('REJECT', $model->decision);
    }

    public function testNestedConvertToModelCreatesCybersourceSOAPModel()
    {
        $obj = new \stdClass();
        $obj->requestID = '12345';
        $obj->decision = 'REJECT';

        $newObj = new \stdClass();
        $newObj->testReason = 101;

        $obj->reasonCode = $newObj;

        $testModel = new CybersourceSOAPModel();
        $model = $this->soapRequester->convertToModel($testModel, $obj);

        $this->assertEquals('12345', $model->requestID);
        $this->assertInstanceOf('JustGeeky\LaravelCybersource\Models\CybersourceSOAPModel', $model->reasonCode);
    }

    public function testConvertToStdClass()
    {
        $this->factory
            ->shouldReceive('getInstance')
            ->with(m::type('array'))
            ->once()
            ->andReturn($this->mockClient);

        $this->mockClient
            ->shouldReceive('addWSSEToken')
            ->once();

        $request = $this->getCybersourceSOAPModel();

        $obj = $this->soapRequester->convertToStdClass($request);

        $this->assertInstanceOf('stdClass', $obj);
        $this->assertEquals('PHP', $obj->clientLibrary);
        $this->assertEquals(phpversion(), $obj->clientLibraryVersion);
        $this->assertEquals($this->environment, $obj->clientEnvironment);
        $this->assertEquals($this->merchantId, $obj->merchantID);
    }

    public function testExceptionPropagatesOnConversionToStdClass()
    {
        $this->setExpectedException('JustGeeky\LaravelCybersource\Exceptions\CybersourceConnectionException');

        $this->factory
            ->shouldReceive('getInstance')
            ->with(m::any('array'))
            ->once()
            ->andThrow(new \SoapFault('test-code', 'test-string'));

        $this->mockClient
            ->shouldReceive('addWSSEToken')
            ->never();

        $request = $this->getCybersourceSOAPModel();

        $this->soapRequester->convertToStdClass($request);
    }

    private function getCybersourceSOAPModel()
    {
        return new CybersourceSOAPModel('PHP', phpversion(), $this->environment, $this->merchantId);
    }

}