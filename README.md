# Laravel Cybersource SOAP & Secure Acceptance

This package wraps the Cybersource Secure Acceptance & SOAP API in a convenient, easy to use package for Laravel.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

## Installation 

```
composer require a17mad/laravel-cybersource
```

### If you use laravel < 5.5 you must add this to config\app.php
```
 Providers Array 
   JustGeeky\LaravelCybersource\Providers\LaravelCybersourceServiceProvider::class

 Facade Array 
   "Cybersource" => JustGeeky\LaravelCybersource\Facades\Cybersource::class

```

## Publishing Configuration

```
php artisan vendor:publish --tag=cybersource
```

### Usage Of Secure Acceptance Form

Add your Cybersource Profile Credentials 
 <br> *  config\cybersource-profiles.php
```
- cd into your app 
- php artisan serve
- Visit (http://127.0.0.1:8000/cybersource/payment/form)

```

### Usage SOAP 

Example usage using Facade:
 <br> *  Create New Subscription (Receive Cybersource Profile Token)
```
$response = Cybersource::createSubscription(
    $paymentToken,
    $productId,
    $productTotal,
    $frequency
);

if($response->isValid()) {
    $responseDetails = $response->getDetails();
    echo $responseDetails['paySubscriptionCreateReply']['subscriptionID'];
} else {
    echo $response->error();
}
```

Get The Current Subscription Details:

```
$response = Cybersource::getSubscriptionStatus(
    $subscriptionID
);

if($response->isValid()) {
    $responseDetails = $response->getDetails();
    echo $responseDetails['message'];
} else {
    echo $response->error();
}
```

## Author

* **Ahmad Elknany** - *Development* - [Linkedin](https://www.linkedin.com/in/ahmad-elkenany/)

## License

This project is licensed under the MIT License - see the [LICENSE.md](https://github.com/a17mad/laravel-cybersource/blob/master/LICENSE) file for details

## Acknowledgments
- For Secure Acceptance Web / Mobile Check Out CyberSource DOCS at [W/M](https://www.cybersource.com/developers/getting_started/integration_methods/secure_acceptance_wm/)
- For Secure Acceptance Silent Order POST Check Out CyberSource DOCS at [SOP](https://www.cybersource.com/developers/getting_started/integration_methods/secure_acceptance_sop/)

- For SOAP Toolkit API Check Out CyberSource DOCS at [SOAP](https://www.cybersource.com/developers/getting_started/integration_methods/soap_toolkit_api/)


## Support on Beerpay
Hey dude! Help me out for a couple of :beers:!

[![Beerpay](https://beerpay.io/deva7mad/laravel-cybersource/badge.svg?style=beer-square)](https://beerpay.io/deva7mad/laravel-cybersource)  [![Beerpay](https://beerpay.io/deva7mad/laravel-cybersource/make-wish.svg?style=flat-square)](https://beerpay.io/deva7mad/laravel-cybersource?focus=wish)