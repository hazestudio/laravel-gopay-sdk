Laravel-GoPay-SDK
====================
### Docs

* [Installation](#installation)
* [Configuration](#configuration)
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
    'GoPay' => HazeStudio\LaravelGoPaySDK\GoPay::class,
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

## Examples

### Create standard payment
```php
use GoPay;
use GoPay\Definition\Payment\Currency;
use GoPay\Definition\Payment\PaymentInstrument;
use GoPay\Definition\Payment\BankSwiftCode;

$response = GoPay::createPayment([
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
### All methods from official SDK working
https://doc.gopay.com/en/?php#establishment-of-payment
Only replace part "$gopay->" to Laravel Facade call "\GoPay::"

### Other Methods
```php
\GoPay::scope(GoPay\Definition\TokenScope::ALL) //Override default scope
//or just
\GoPay::scope('ALL')->createPayment(...);


\GoPay::lang(GoPay\Definition\Languages::SLOVAK) //Override client or fallback language
//or just
\GoPay::lang('SLOVAK')->createPayment(...);
```