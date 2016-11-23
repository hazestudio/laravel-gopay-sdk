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

    protected $scope;
    protected $lang;

    public function __construct($app)
    {
        $this->app = $app;

        $fallback = config('app.fallback_locale');

        if(isset(config('gopay.languages')[\Lang::locale()])){
            $language = config('gopay.languages')[\Lang::locale()];
        } else {
            $language = config('gopay.languages')[$fallback];
        }

        if(defined($langConst = 'GoPay\Definition\Language::'.$language)) {
            $this->lang = constant($langConst);
        } else {
            $this->lang = GoPay\Definition\Language::ENGLISH;
        }

        if(defined($scopeConst = 'GoPay\Definition\TokenScope::'.config('gopay.defaultScope'))){
            $this->scope = constant($scopeConst);
        } else {
            $this->scope = GoPay\Definition\TokenScope::CREATE_PAYMENT;
        }
    }

    protected function initGoPay()
    {
        $this->gopay = GoPay\Api::payments([
            'goid' => config('gopay.goid'),
            'clientId' => config('gopay.clientId'),
            'clientSecret' => config('gopay.clientSecret'),
            'isProductionMode' => !Config::get('APP_DEBUG'),
            'scope' => $this->scope,
            'language' => $this->lang,
            'timeout' => config('gopay.timeout')
        ]);
        return true;
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

    public function scope($scope)
    {
        if(defined($scopeConst = 'GoPay\Definition\TokenScope::'.$scope))
        {
            $this->scope = constant($scopeConst);
        } else {
            $this->scope = $scope;
        }

        while($this->initGoPay() != true){}

        return $this;
    }

    public function lang($lang)
    {
        if(defined($scopeConst = 'GoPay\Definition\Language::'.$lang))
        {
            $this->scope = constant($lang);
        } else {
            $this->scope = $lang;
        }

        while($this->initGoPay() != true){}

        return $this;
    }
}