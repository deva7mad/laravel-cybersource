<?php namespace JustGeeky\LaravelCybersource;

use BeSimple\SoapClient\SoapClient as BeSimpleSoapClient;
use JustGeeky\LaravelCybersource\Configs\ServerConfigs;
use JustGeeky\LaravelCybersource\Exceptions\CybersourceException;

class SOAPClient extends BeSimpleSoapClient {

    public static $app;

    const DOM_VERSION = '1.0';

    const SOAP_HEADER_PRE_USER = '<SOAP-ENV:Header xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"><wsse:Security SOAP-ENV:mustUnderstand="1"><wsse:UsernameToken><wsse:Username>';
    const SOAP_HEADER_PRE_PASS = '</wsse:Username><wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">';
    const SOAP_HEADER_FINAL = '</wsse:Password></wsse:UsernameToken></wsse:Security></SOAP-ENV:Header>';

    const WSSE_NAMESPACE = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
    const TYPE_NAMESPACE = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText';

    protected $wsdl;
    protected $merchantId;
    protected $transactionId;

    /**
     * Constructs a client off of the
     * configured WSDL
     */
    public function __construct(ServerConfigs $configs, array $options = [])
    {
        $this->wsdl = $configs->getWsdlEndpoint();
        $this->merchantId = $configs->getMerchantId();
        $this->transactionId = $configs->getTransactionId();

        parent::__construct($this->wsdl, $options);
    }

    public function addWSSEToken()
    {
        $user = new \SoapVar($this->merchantId, XSD_STRING, null, self::WSSE_NAMESPACE, null, self::WSSE_NAMESPACE);
        $password = new \SoapVar($this->transactionId, XSD_STRING, null, self::TYPE_NAMESPACE, null, self::WSSE_NAMESPACE);

        $userToken = new \stdClass();
        $userToken->Username = $user;
        $userToken->Password = $password;

        $userToken = new \SoapVar($userToken, SOAP_ENC_OBJECT, null, self::WSSE_NAMESPACE, 'UsernameToken', self::WSSE_NAMESPACE);

        $security = new \stdClass();
        $security->UsernameToken = $userToken;

        $security = new \SoapVar($security, SOAP_ENC_OBJECT, null, self::WSSE_NAMESPACE, 'Security', self::WSSE_NAMESPACE);

        $header = new \SoapHeader(self::WSSE_NAMESPACE, 'Security', $security, true);

        $this->__setSoapHeaders($header);
    }


    /**
     * Static getInstance Method for updating SOAP options
     * @param array $options
     * @return SOAPClient
     */
    public static function getInstance(array $options = [])
    {
        $factory = new SOAPClientFactory(static::$app);
        return $factory->getInstance($options);
    }


    /**
     * Runs the request
     * @param $request
     * @param $location
     * @param $action
     * @param $version
     * @param null $one_way
     * @return string
     * @throws Exceptions\CybersourceException
     * @codeCoverageIgnore
     */
    public function doRequest($request, $location, $action, $version, $one_way = null)
    {
        $header = self::SOAP_HEADER_PRE_USER . $this->merchantId .
            self::SOAP_HEADER_PRE_PASS . $this->transactionId .
            self::SOAP_HEADER_FINAL;

        $requestDOM = new \DOMDocument(self::DOM_VERSION);
        $soapHeaderDOM = new \DOMDocument(self::DOM_VERSION);

        try {
            $requestDOM->loadXML($request);
            $soapHeaderDOM->loadXML($header);

            $node = $requestDOM->importNode($soapHeaderDOM->firstChild, true);

            $requestDOM->firstChild->insertBefore($node, $requestDOM->firstChild->firstChild);

            $request = $requestDOM->saveXML();
        } catch (\DOMException $e) {
            \Log::error($e);
            throw new CybersourceException();
        }

        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }


} 