<?php
/**
 * Created by Damián Imrich / Haze Studio.
 * Date: 22.11.2016
 * Time: 14:55
 */
namespace HazeStudio\LaravelGoPaySDK\Facades;

use Illuminate\Support\Facades\Facade;

class GoPay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'gopay-sdk'; }
}