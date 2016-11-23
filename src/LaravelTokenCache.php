<?php
/**
 * Created by Damián Imrich / Haze Studio.
 * Date: 23.11.2016
 * Time: 12:31
 */
namespace HazeStudio\LaravelGoPaySDK;

use Cache;
use GoPay\Token\TokenCache;
use GoPay\Token\AccessToken;

class LaravelTokenCache implements TokenCache
{
    public function setAccessToken($client, AccessToken $t)
    {
        Cache::put('gopay_token_'.$client, serialize($t), config('gopay.timeout'));
    }

    public function getAccessToken($client)
    {
        $token = Cache::get('gopay_token_'.$client);
        if (!is_null($token)) {
            return unserialize($token);
        }
        return null;
    }
}