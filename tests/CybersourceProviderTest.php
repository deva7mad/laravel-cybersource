<?php

use JustGeeky\LaravelCybersource\Configs\Factory as ConfigsFactory;
use LaravelCybersource\TestCase;

class CybersourceProviderTest extends TestCase {

    public function testConfigs()
    {
        $configs = (new ConfigsFactory())->getFromConfigFile();

        $this->assertInstanceOf('JustGeeky\LaravelCybersource\Configs\ServerConfigs', $configs);
    }

} 