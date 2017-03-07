<?php
/**
 * Created by Damián Imrich / Haze Studio.
 * Date: 22.11.2016
 * Time: 14:55
 */
namespace HazeStudio\LaravelGoPaySDK;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class Facade extends LaravelFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'GopaySDK'; }
}