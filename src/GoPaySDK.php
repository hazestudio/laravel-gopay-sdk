<?php
/**
 * Created by Damián Imrich / Haze Studio.
 * Date: 22.11.2016
 * Time: 14:54
 */

namespace HazeStudio\LaravelGoPaySDK;

use GoPay;

class GoPaySDK
{
    protected $app;
    protected $gopay;

    public function __construct($app)
    {
        $this->app = $app;

        $language = config('gopay.fallbackLanguage');

        if(isset(config('gopay.languages')[\Lang::locale()])){
            $language = config('gopay.languages')[\Lang::locale()];
        }

        $this->gopay = GoPay\Api::payments([
            'goid' => config('gopay.goid'),
            'clientId' => config('gopay.clientId'),
            'clientSecret' => config('gopay.clientSecret'),
            'isProductionMode' => config('gopay.isProductionMode'),
            'scope' => config('gopay.defaultScope'),
            'language' => $language,
            'timeout' => config('gopay.timeout')
        ]);
    }

    public function __call($name, $arguments)
    {
        if(method_exists($this, $name))
        {
            return $this->{$name}(...$arguments);
        } else if(method_exists($this->gopay, $name)){
            return $this->gopay->{$name}(...$arguments);
        }

        return null;
    }
}