<?php namespace LaravelCybersource;

use JustGeeky\LaravelCybersource\SOAPClient;
use LaravelCybersource\TestCase;
use \Mockery as m;

class SOAPClientTest extends TestCase {

    private $client;

    public function setUp()
    {
        parent::setUp();
        $this->client = new SOAPClient($this->configs);
    }

    public function testConstruct()
    {
        //only need to test creation with WSDL
        $this->assertNotNull($this->client);
    }

    public function testAddWSSEToken()
    {
        $this->client->addWSSEToken();

        $headers = $this->client->__default_headers[0];

        $this->assertInstanceOf('SoapHeader', $headers);
        $this->assertEquals(SOAPClient::WSSE_NAMESPACE, $headers->namespace);
        $this->assertEquals('Security', $headers->name);
    }

} 