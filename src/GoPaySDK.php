<?php
/**
 * Created by Damián Imrich / Haze Studio.
 * Date: 22.11.2016
 * Time: 14:54
 */

namespace HazeStudio\LaravelGoPaySDK;

use GoPay;
use HazeStudio\LaravelGoPaySDK\Events\PaymentCreated;

class GoPaySDK
{
    protected $gopay;

    protected $config = [];
    protected $services = [];
    protected $needReInit = false;

    protected $logsBefore = [];
    private $logClosure;

    public function __construct()
    {
        $this->config = [
            'goid' => config('gopay.goid'),
            'clientId' => config('gopay.clientId'),
            'clientSecret' => config('gopay.clientSecret'),
            'isProductionMode' => !filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN),
            'timeout' => config('gopay.timeout')
        ];

        $fallback = config('app.fallback_locale');

        if(isset(config('gopay.languages')[\App::getLocale()])){
            $language = config('gopay.languages')[\App::getLocale()];
        } else {
            $language = config('gopay.languages')[$fallback];
        }

        if(defined($langConst = 'GoPay\Definition\Language::'.$language)) {
            $this->config['language'] = constant($langConst);
        } else {
            $this->config['language'] = GoPay\Definition\Language::ENGLISH;
        }

        if(defined($scopeConst = 'GoPay\Definition\TokenScope::'.config('gopay.defaultScope'))){
            $this->config['scope'] = constant($scopeConst);
        } else {
            $this->config['scope'] = GoPay\Definition\TokenScope::CREATE_PAYMENT;
        }

        $this->services['cache'] = new LaravelTokenCache();
        $this->services['logger'] = new Logger();

        $this->initGoPay();
    }

    protected function initGoPay()
    {
        $this->gopay = GoPay\Api::payments($this->config, $this->services);
        if($this->needReInit)
            $this->needReInit = false;
        return $this->gopay;
    }

    public function __call($name, $arguments)
    {
        if(method_exists($this, $name))
        {
            return $this->{$name}(...$arguments);
        } else if(method_exists($this->gopay, $name)){
            if($this->needReInit){
                $gp = $this->initGoPay();
            } else {
                $gp = $this->gopay;
            }
            $methodResult = $gp->{$name}(...$arguments);

            switch ($name){
                case 'createPayment':
                    event(new PaymentCreated($methodResult));
                    break;
                default:
            }

            return $methodResult;
        }

        return null;
    }

    public function scope($scope)
    {
        if(defined($scopeConst = 'GoPay\Definition\TokenScope::'.$scope))
        {
            $this->config['scope'] = constant($scopeConst);
        } else {
            $this->config['scope'] = $scope;
        }
        $this->needReInit = true;
        return $this;
    }

    public function lang($lang)
    {
        if(defined($langConst = 'GoPay\Definition\Language::'.$lang))
        {
            $this->config['language'] = constant($langConst);
        } else if(isset(config('gopay.languages')[$lang]) && defined($langConst = 'GoPay\Definition\Language::'.config('gopay.languages')[$lang])) {
            $this->config['language'] = constant($langConst);
        } else {
            $this->config['language'] = $lang;
        }
        $this->needReInit = true;
        return $this;
    }

    public function logHttpCommunication($request, $response)
    {
        if($this->logClosure == null) {

            $this->logsBefore[] = [$request, $response];
        }else{
            call_user_func($this->logClosure, $request, $response);
        }
    }

    public function log($closure)
    {
        $this->logClosure = $closure;

        foreach($this->logsBefore as $log)
        {
            call_user_func_array($this->logClosure, $log);
        }
        $this->logsBefore = [];
        return $this;
    }
}
