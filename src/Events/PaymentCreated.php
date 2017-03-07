<?php
/**
 * Created by Damián Imrich / Haze Studio.
 * Date: 23.11.2016
 * Time: 22:13
 */

namespace HazeStudio\LaravelGoPaySDK\Events;

use GoPay\Http\Response;

class PaymentCreated
{
    public $payment;
    /**
     * Create a new event instance.
     *
     * @param  Order  $order
     * @return void
     */
    public function __construct(Response $paymentResponse)
    {
        $this->payment = $paymentResponse->json;
    }
}