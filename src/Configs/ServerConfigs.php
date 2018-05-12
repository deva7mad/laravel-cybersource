<?php

namespace JustGeeky\LaravelCybersource\Configs;


class ServerConfigs {

    protected $env;
    protected $timezone;
    protected $organization_id;
    protected $wsdl_endpoint;
    protected $outbound_merchant_id;
    protected $currency;
    protected $merchant_id;
    protected $merchant_reference_code;
    protected $transaction_id;
    protected $timeout;
    protected $username;
    protected $password;
    /* @var array */
    protected $reports;
    /* @var array */
    protected $resultCodes;

    /**
     * Optionally build the configs object with array of configs
     *
     * @param array $configs
     */
    public function __construct(array $configs = [])
    {
        if (!empty($configs)) {
            $this->setConfigs($configs);
        }
    }


    /**
     * @param array $configs
     */
    public function setConfigs(array $configs = [])
    {
        $this->setEnv(isset($configs['env']) ? $configs['env'] : null);
        $this->setTimezone(isset($configs['timezone']) ? $configs['timezone'] : null);
        $this->setOrganizationId(isset($configs['organization_id']) ? $configs['organization_id'] : null);
        $this->setWsdlEndpoint(isset($configs['wsdl_endpoint']) ? $configs['wsdl_endpoint'] : null);
        $this->setOutboundMerchantId(isset($configs['outbound_merchant_id']) ? $configs['outbound_merchant_id'] : null);
        $this->setCurrency(isset($configs['currency']) ? $configs['currency'] : null);
        $this->setMerchantId(isset($configs['merchant_id']) ? $configs['merchant_id'] : null);
        $this->setMerchantReferenceCode(isset($configs['merchant_reference_code']) ? $configs['merchant_reference_code'] : null);
        $this->setTransactionId(isset($configs['transaction_id']) ? $configs['transaction_id'] : null);
        $this->setTimeout(isset($configs['timeout']) ? $configs['timeout'] : null);
        $this->setUsername(isset($configs['username']) ? $configs['username'] : null);
        $this->setPassword(isset($configs['password']) ? $configs['password'] : null);
        $this->setReports(isset($configs['reports']) ? $configs['reports'] : null);
        $this->setResultCodes(isset($configs['result_codes']) ? $configs['result_codes'] : null);
    }

    /**
     * @return mixed
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param mixed $env
     */
    public function setEnv($env)
    {
        $this->env = $env;
    }

    /**
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param mixed $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @return mixed
     */
    public function getOrganizationId()
    {
        return $this->organization_id;
    }

    /**
     * @param mixed $organization_id
     */
    public function setOrganizationId($organization_id)
    {
        $this->organization_id = $organization_id;
    }

    /**
     * @return mixed
     */
    public function getWsdlEndpoint()
    {
        return $this->wsdl_endpoint;
    }

    /**
     * @param mixed $wsdl_endpoint
     */
    public function setWsdlEndpoint($wsdl_endpoint)
    {
        $this->wsdl_endpoint = $wsdl_endpoint;
    }

    /**
     * @return mixed
     */
    public function getOutboundMerchantId()
    {
        return $this->outbound_merchant_id;
    }

    /**
     * @param mixed $outbound_merchant_id
     */
    public function setOutboundMerchantId($outbound_merchant_id)
    {
        $this->outbound_merchant_id = $outbound_merchant_id;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->merchant_id;
    }

    /**
     * @param mixed $merchant_id
     */
    public function setMerchantId($merchant_id)
    {
        $this->merchant_id = $merchant_id;
    }

    /**
     * @return mixed
     */
    public function getMerchantReferenceCode()
    {
        return $this->merchant_reference_code;
    }

    /**
     * @param mixed $merchant_reference_code
     */
    public function setMerchantReferenceCode($merchant_reference_code)
    {
        $this->merchant_reference_code = $merchant_reference_code;
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    /**
     * @param mixed $transaction_id
     */
    public function setTransactionId($transaction_id)
    {
        $this->transaction_id = $transaction_id;
    }

    /**
     * @return mixed
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param mixed $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return array
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @param array $reports
     */
    public function setReports($reports)
    {
        $this->reports = $reports;
    }

    /**
     * @return array
     */
    public function getResultCodes()
    {
        return $this->resultCodes;
    }

    /**
     * @param array $resultCodes
     */
    public function setResultCodes($resultCodes)
    {
        $this->resultCodes = $resultCodes;
    }
}