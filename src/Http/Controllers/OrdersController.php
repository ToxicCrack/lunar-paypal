<?php

namespace Lichtblauit\LunarPaypal\Http\Controllers;

use Illuminate\Http\Request;
use Lichtblauit\LunarPaypal\Models\Paypal;
use Lunar\Models\Cart;
use Lunar\Models\Order;
use Lunar\Models\Transaction;
use Lunar\Facades\Discounts;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class OrdersController
{
    public function create(Request $request)
    {
        $cart = Cart::find($request->cart_id)->calculate();
        if($cart->customer) {
            Discounts::customerGroup($cart->customer->customerGroups);
        } else {
            Discounts::customerGroup($cart->user->customers->first()->customerGroups);
        }
        Discounts::apply($cart);
        $cart = $cart->calculate();

        $order = Order::where('cart_id', $cart->id)->first() ?? $cart->createOrder();

        if (! $order->customer()->exists() && $cart->user()->exists()) {
            $customer = $cart->user->customers()->first();
            $newCustomer = $customer->orders()->count() === 0;

            $order->update([
                'customer_id' => $customer->id,
                'new_customer' => $newCustomer,
            ]);

            $order->save();
        }

        $paypal = new PayPalClient();
        $paypal->getAccessToken();
        $paypal->getClientToken();

        $purchaseUnits = [];

        $divider = 100;
        if($cart->currency->decimal_places == 3) {
            $divider = 1000;
        } elseif($cart->currency->decimal_places == 4) {
            $divider = 10000;
        }
        $purchaseUnits[] = [
            'amount' => [
                'currency_code' => $cart->total->currency->code,
                'value' => round($cart->total->value / $divider, 2),
            ],
            'description' => __("Order - :appname", ['appname' => config("app.name")])
        ];

        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => $purchaseUnits,
        ];

        $paypalOrder = $paypal->createOrder($data);
        if(isset($paypalOrder["error"]) && !empty($paypalOrder["error"])) {
            echo $paypalOrder["error"]['name'].": ".$paypalOrder["error"]["message"];
            die();
        }

        $transaction = Transaction::create([
            'order_id' => $order->id,
            'reference' => $paypalOrder['id'],
            'amount' => $cart->total->value,
            'success' => true,
            'driver' => 'paypal',
            'status' => 'pending',
            'card_type' => 'paypal',
            'type' => 'intent',
        ]);

        return $paypalOrder;
    }

    public function capture($orderId)
    {
        $transaction = Transaction::where('reference', $orderId)->first();

        $paypal = new Paypal();

        return $paypal->capture($transaction);
    }
}
