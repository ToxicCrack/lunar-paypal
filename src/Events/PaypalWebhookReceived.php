<?php

namespace Lichtblauit\LunarPaypal\Events;

use Illuminate\Foundation\Events\Dispatchable;

class PaypalWebhookReceived
{
    use Dispatchable;

    public function __construct(
        public array $data,
    ) {
    }
}
