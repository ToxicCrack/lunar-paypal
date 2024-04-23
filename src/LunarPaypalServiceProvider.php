<?php

namespace Lichtblauit\LunarPaypal;

use Illuminate\Support\Facades\Blade;
use Lichtblauit\LunarPaypal\Components\PaymentForm;
use Lichtblauit\LunarPaypal\Models\Paypal;
use Livewire\Livewire;
use Lunar\Facades\Payments;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LunarPaypalServiceProvider extends PackageServiceProvider
{
    public function boot()
    {
        parent::boot();

        $payPal = new Paypal();
        $clientId = $payPal->clientId;
        $clientToken = $payPal->getClientToken();

        // Register our payment type
        Payments::extend('paypal', function ($app) {
            return $app->make(PaypalPaymentType::class);
        });

        Blade::directive('paypalScripts', function ($currency="USD") use ($clientId, $clientToken) {
            return <<<EOT
                <script src="https://www.paypal.com/sdk/js?components=buttons,hosted-fields&client-id={$clientId}&disable-funding=credit&currency={$currency}" data-client-token="{$clientToken}"></script>
            EOT;
        });

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'lunar-paypal');

        // Register the stripe payment component.
        Livewire::component('paypal.payment', PaymentForm::class);
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('lunar-paypal')
            ->hasRoutes(['web']);
    }
}
