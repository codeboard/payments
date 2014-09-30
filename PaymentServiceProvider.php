<?php namespace Codeboard\Payments;

use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider {

    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('Codeboard\Payments\Contracts\Factory', function($app) {
            return new PaymentManager($app);
        });
    }

    public function provides()
    {
        return ['Codeboard\Payments\Contracts\Factory'];
    }
}