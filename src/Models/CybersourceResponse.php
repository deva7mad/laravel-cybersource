<?php namespace JustGeeky\LaravelCybersource\Models;

use JustGeeky\LaravelCybersource\Exceptions\CybersourceException;

class CybersourceResponse {

    private $valid;

    /** @var array */
    private $response;
    private $reasonCode;

    private $request;

    /** @var array */
    private $resultCodes;

    /**
     * Response object constructor method
     *
     * @param $response Array|CybersourceSoapModel [required]
     * @param $resultCodes Array [required]
     * @throws \JustGeeky\LaravelCybersource\Exceptions\CybersourceException
     */
    public function __construct($response, $resultCodes)
    {
        if ($response instanceof CybersourceSOAPModel) {
            $response = $response->toArray();
        }

        $this->resultCodes = $resultCodes;

        $this->reasonCode = $this->getReasonCode($response);

        if(is_null($this->reasonCode)) {
            throw new CybersourceException('Response Code Not Provided');
        }

        if(!isset($response['decision'])) {
            throw new CybersourceException('Decision Not Provided');
        }

        if(!isset($this->resultCodes[$this->reasonCode])) {
            throw new CybersourceException('Invalid Response Code Provided');
        }
        $this->valid = $response['decision'] == 'ACCEPT' ? true : false;
        $this->response = $response;
        $this->response['message'] = $this->resultCodes[$this->reasonCode];
    }

    private function getReasonCode($responseArray) {
        $code = null;

        if (isset($responseArray['reasonCode'])) {
            $code = $responseArray['reasonCode'];
        } elseif (isset($responseArray['reason_code'])) {
            $code = $responseArray['reason_code'];
        }

        return $code;
    }

    // @codeCoverageIgnoreStart
    public function __set($name, $value)
    {
        $this->response[$name] = $value;
    }

    public function __get($name)
    {
        if(isset($this->response[$name])) {
            return $this->response[$name];
        }
        return null;
    }
    // @codeCoverageIgnoreEnd

    public function setRequest($request) {
        if($request instanceof CybersourceSOAPModel) {
            $this->request = $request->toArray();
        } else {
            $this->request = $request;
        }
    }

    public function getRequestData() {
        return $this->request;
    }

    /**
     * Checks whether the request was successful or failed
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    public function error() {
        if ($this->isValid()) {
            return false;
        } else {
            return !empty($this->response['message']) ? $this->response['message'] : false;
        }
    }

    /**
     * Returns an array of response data
     * @return array|mixed
     */
    public function getDetails()
    {
        return $this->response;
    }

}