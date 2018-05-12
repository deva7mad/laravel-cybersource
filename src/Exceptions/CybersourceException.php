<?php

namespace JustGeeky\LaravelCybersource\Exceptions;

use \Exception;

class CybersourceException extends Exception {}

class CybersourceMissingResponseCodeException extends CybersourceException {}

class CybersourceMissingDecisionException extends CybersourceException {}

class CybersourceInvalidResponseCodeException extends CybersourceException {}

class CybersourceConnectionException extends CybersourceException {}
