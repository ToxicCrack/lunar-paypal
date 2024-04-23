<?php

namespace Lichtblauit\LunarPaypal\Components;

use Livewire\Component;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart;

class PaymentForm extends Component
{
    public Cart $cart;

    public $returnUrl;

    public $policy;

    public $payPalScript;

    protected $listeners = [
        'cartUpdated' => 'refreshCart',
        'selectedShippingOption' => 'refreshCart',
    ];

    public function refreshCart()
    {
        $this->cart = CartSession::current()?->calculate();
    }

    public function render()
    {
        return view('lunar-paypal::paypal.components.payment-form');
    }
}
