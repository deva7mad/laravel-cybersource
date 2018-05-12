<?php

namespace LaravelCybersource;

use \Mockery as m;
use JustGeeky\LaravelCybersource\Configs\Factory as ConfigsFactory;
use JustGeeky\LaravelCybersource\Configs\ServerConfigs;

class TestCase extends \PHPUnit_Framework_TestCase {

    protected $environment = 'testing';
    protected $merchantId = 'test-merchant-id';
    protected $merchantRefCode = 'test-merchant-code';
    /** @var ServerConfigs */
    protected $configs;

    public function setUp()
    {
        $this->configs = (new ConfigsFactory())->getFromConfigFile();
    }

    public function tearDown()
    {
        m::close();
    }

} 