<?php

namespace JustGeeky\LaravelCybersource\Configs;


class Factory {

    public function make()
    {
        return new ServerConfigs();
    }


    public function getFromConfigFile()
    {
        $configFile = config_path('cybersource.php');

        $defaultConfigs = require(__DIR__.'/../../app/config/cybersource.php');

        $userProvidedConfigs = is_file($configFile) ? require($configFile) : [];

        $configsEntity = $this->make();

        $configsEntity->setConfigs(array_merge($defaultConfigs, $userProvidedConfigs));

        return $configsEntity;

    }

}
