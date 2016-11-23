<?php
return [
    'goid' => '...',
    'clientId' => '...',
    'clientSecret' => '...',
    'defaultScope' => 'ALL', //GoPay\Definition\TokenScope Constants
    'languages' => [
        'en' => 'ENGLISH',
        'sk' => 'SLOVAK',
        'cs' => 'CZECH'
    ], //Map Laravel languages to GoPay\Definition\Language Constants
    'timeout' => 30
];