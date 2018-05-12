<?php namespace JustGeeky\LaravelCybersource\Models;

class CybersourceSOAPModel {

    /**
     * @var array
     */
    private $data;

    public function __construct($clientLibrary = null, $clientLibVersion = null,
                                $clientEnv = null, $merchantId = null, $merchantReferenceCode = null)
    {
        $this->clientLibrary = $clientLibrary;
        $this->clientLibraryVersion = $clientLibVersion;
        $this->clientEnvironment = $clientEnv;
        $this->merchantID = $merchantId;
        $this->merchantReferenceCode = $merchantReferenceCode;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return null;
    }

    public function toArray()
    {
        $stdObj = $this->toStdObject();
        return json_decode(json_encode($stdObj), true);
    }

    public function toStdObject()
    {
        $root = new \stdClass();
        $this->createNestedStdObject($root, $this->data);
        return $root;
    }

    private function createNestedStdObject(&$root, $data)
    {
        foreach($data as $key => $value) {
            if($value instanceof CybersourceSOAPModel) {
                $obj = new \stdClass();
                $this->createNestedStdObject($obj, $value->data);
                $root->$key = $obj;
            } else {
                if(!is_null($value)) {
                    $root->$key = $value;
                }
            }
        }
    }

} 