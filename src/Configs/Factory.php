<?php

namespace JustGeeky\LaravelCybersource\Configs;


class Factory {

    public function make()
    {
        return new ServerConfigs();
    }


    public function getFromConfigFile()
    {
        $configFile = __DIR__ .'/../../../../../../../config/cybersource.php';

        $defaultConfigs = require(__DIR__ . '/../../../config/config.php');

        $userProvidedConfigs = is_file($configFile) ? require($configFile) : [];

        $configsEntity = $this->make();

        $configsEntity->setConfigs(array_merge($defaultConfigs, $userProvidedConfigs));

        return $configsEntity;

    }

}