# Laravel GoPay SDK
[![Build Status](https://scrutinizer-ci.com/g/hazestudio/laravel-gopay-sdk/badges/build.png?b=master)](https://scrutinizer-ci.com/g/hazestudio/laravel-gopay-sdk/build-status/master) [![Scrutinizer](https://img.shields.io/scrutinizer/g/hazestudio/laravel-gopay-sdk.svg)](https://scrutinizer-ci.com/g/hazestudio/laravel-gopay-sdk/?branch=master) [![Latest Stable Version](https://img.shields.io/packagist/v/hazestudio/laravel-gopay-sdk.svg)](https://packagist.org/packages/hazestudio/laravel-gopay-sdk) [![Total Downloads](https://img.shields.io/packagist/dt/hazestudio/laravel-gopay-sdk.svg)]() [![Packagist](https://img.shields.io/packagist/l/hazestudio/laravel-gopay-sdk.svg?style=plastic)]()

### Docs

* [Installation](#installation)
* [Configuration](#configuration)
* [Features](#features)
* [Examples](#examples)

## Installation

### Step 1: Install package

Add the package in your composer.json by executing the command.

```bash
composer require hazestudio/laravel-gopay-sdk
```
This will both update composer.json and install the package into the vendor/ directory.

Next, add the service provider and facade to `config/app.php`

Add the service provider to providers:
```
'providers' => [
    ...
    HazeStudio\LaravelGoPaySDK\GopayServiceProvider::class,
    ...
]
```

And add the facade to aliases:
```
'aliases' => [
    ...
    'GoPay' => HazeStudio\LaravelGoPaySDK\Facade::class,
    ...
]
```
### Step 2: Configuration

First initialise the config file by running this command:

```bash
php artisan vendor:publish
```

With this command, initialize the configuration and modify the created file, located under `config/gopay.php`.

## Configuration
```php
return [
    'goid' => 'YOUR_GOID',
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_CLIENT_SECRET',
    'defaultScope' => 'ALL', //GoPay\Definition\TokenScope Constants
    'languages' => [
        'en' => 'ENGLISH',
        'sk' => 'SLOVAK',
        'cs' => 'CZECH'
    ], //Map Laravel languages to GoPay\Definition\Language Constants
    'timeout' => 30
];
```
## Features
### Languages
```php
\GoPay::lang(GoPay\Definition\Languages::SLOVAK)
//or just
\GoPay::lang('sk')
//or
\GoPay::lang('SLOVAK')->createPayment(...);
```

### Scopes
```php
\GoPay::scope(GoPay\Definition\TokenScope::ALL) //Override default scope
//or
\GoPay::scope('ALL')->createPayment(...);
```
### Events

|      **Name**      |                     **Class**                    |
|:--------------:|:------------------------------------------------:|
| PaymentCreated | HazeStudio\LaravelGoPaySDK\Events\PaymentCreated |

Example:
```php
Event::listen(\HazeStudio\LaravelGoPaySDK\Events\PaymentCreated::class, function ($event) {
    dd($event->payment);
});
```

## Examples

### Create standard payment
```php
use GoPay;
use GoPay\Definition\Payment\Currency;
use GoPay\Definition\Payment\PaymentInstrument;
use GoPay\Definition\Payment\BankSwiftCode;

//This will log every http request to the GoPay api
GoPay::log(function($request, $response){
    \PC::gp_request($request); //PHP Console package
    \PC::gp_response($response); //PHP Console package
    //Or Laravel Log
    \Log::info("{$request->method} {$request->url} -> {$response->statusCode}");
});

$user = \Auth::user();
$response = GoPay::lang($user->locale)->scope('CREATE_PAYMENT')->createPayment([
    'payer' => [
        'default_payment_instrument' => PaymentInstrument::BANK_ACCOUNT,
        'allowed_payment_instruments' => [PaymentInstrument::BANK_ACCOUNT],
        'default_swift' => BankSwiftCode::FIO_BANKA,
        'allowed_swifts' => [BankSwiftCode::FIO_BANKA, BankSwiftCode::MBANK],
        'contact' => [
            'first_name' => 'Zbynek',
            'last_name' => 'Zak',
            'email' => 'test@test.cz',
            'phone_number' => '+420777456123',
            'city' => 'C.Budejovice',
            'street' => 'Plana 67',
            'postal_code' => '373 01',
            'country_code' => 'CZE',
        ],
    ],
    'amount' => 1000,
    'currency' => Currency::CZECH_CROWNS,
    'order_number' => '001',
    'order_description' => 'pojisteni01',
    'items' => [
        ['name' => 'item01', 'amount' => 50],
        ['name' => 'item02', 'amount' => 100],
    ],
    'additional_params' => [
        array('name' => 'invoicenumber', 'value' => '2015001003')
    ],
    'callback' => [
        'return_url' => 'http://www.your-url.tld/return',
        'notification_url' => 'http://www.your-url.tld/notify'
    ]
]);

if ($response->hasSucceed()) {
    $url = $response->json['gw_url'];
    echo $response;
}
```
