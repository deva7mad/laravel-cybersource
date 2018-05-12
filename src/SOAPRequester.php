<?php namespace JustGeeky\LaravelCybersource;

use JustGeeky\LaravelCybersource\Exceptions\CybersourceConnectionException;
use JustGeeky\LaravelCybersource\Configs\Factory as ConfigsFactory;
use JustGeeky\LaravelCybersource\models\CybersourceSOAPModel;

/**
 * Class SOAPRequester creates SOAP requests for Cybersource and uses
 * the $soapClient to send requests out to a specific url
 * @package JustGeeky\LaravelCybersource
 */
class SOAPRequester {

    /** @var  SOAPClient */
    public $soapClient;
    /** @var  SOAPClientFactory */
    public $clientFactory;
    public $timeout;

    public function __construct($soapClient, $factory)
    {
        $this->soapClient = $soapClient;
        $this->clientFactory = $factory;

        $configs = (new ConfigsFactory())->getFromConfigFile();
        $this->timeout = $configs->getTimeout();
    }

    public function send(CybersourceSOAPModel $request)
    {
        $requestObj = $this->convertToStdClass($request);
        $responseObj = $this->soapClient->runTransaction($requestObj);
        $cybsSOAPObj = new CybersourceSOAPModel();
        return $this->convertToModel($cybsSOAPObj, $responseObj);
    }

    public function convertToStdClass(CybersourceSOAPModel $request)
    {
        $contextOpts = [
            'http' => [
                'timeout' => $this->timeout
            ]
        ];

        $context = stream_context_create($contextOpts);

        $soapOts = [
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
            'encoding' => 'utf-8',
            'exceptions' => true,
            'connection_timeout' => $this->timeout,
            'stream_context' => $context,
            'cache_wsdl' => WSDL_CACHE_MEMORY
        ];

        try {
            $this->soapClient = $this->clientFactory->getInstance($soapOts);
        } catch(\SoapFault $sf) {
            throw new CybersourceConnectionException($sf->getMessage(), $sf->getCode());
        }

        $this->soapClient->addWSSEToken();

        return $request->toStdObject();
    }

    public function convertToModel(&$model, $responseObj)
    {
        foreach($responseObj as $key => $value) {
            if($value instanceof \stdClass) {
                $newModel = new CybersourceSOAPModel();
                $this->convertToModel($newModel, $value);
                $model->$key = $newModel;
            } else {
                if(!is_null($value)) {
                    $model->$key = $value;
                }
            }
        }
        return $model;
    }

}
