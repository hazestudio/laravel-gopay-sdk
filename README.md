Laravel-GoPay-SDK
====================
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
Package automatically set GoPay language by App::getLocale() transformed to properly gopay lang format according to your gopay.php languages configuration.
When application returns locale that does not exists in GoPay, package will use app fallback locale transformed to GoPay language format.
You can also modify language by calling function lang($locale) on GoPay Facade. Parameter can be GoPay Language constant name, value or Laravel locale that you have mapped to GoPay lang.
```php
\GoPay::lang(GoPay\Definition\Languages::SLOVAK)
//or just
\GoPay::lang('sk')
//or
\GoPay::lang('SLOVAK')->createPayment(...);
```

### Scopes
Package will use defaultScope as scope for initial GoPay connection.
Of course you can change scope runtime by calling function scope($new_scope) on GoPay Facade. Parameter can be GoPay TokenScope constant name or value.
```php
\GoPay::scope(GoPay\Definition\TokenScope::ALL) //Override default scope
//or
\GoPay::scope('ALL')->createPayment(...);
```

### Cache access token
Package caching access tokens through Laravel Cache for config timeout minutes.

### Log Http Communication
You can log every Http request and response that GoPay make to api by using log function. Log has only one parameter with closure function. Here is some example:
```php
\GoPay::log(function($request, $response){
    \Log::info("{$request->method} {$request->url} -> {$response->statusCode}");
})->lang($user->locale)->scope('ALL')->refundPayment(...);
```
### All methods from official SDK working
https://doc.gopay.com/en/?php#establishment-of-payment

Just call them at GoPay Facade.

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

## License
Copyright (c) 2016 Haze Studio. MIT Licensed.