<?php

namespace Lichtblauit\LunarPaypal\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Lichtblauit\LunarPaypal\PaypalPaymentType
 */
class LunarPaypal extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Lichtblauit\LunarPaypal\PaypalPaymentType::class;
    }
}
