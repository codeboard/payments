<?php  namespace Codeboard\Payments;

use Codeboard\Payments;
use Illuminate\Support\Manager;

class PaymentManager extends Manager implements Contracts\Factory {

    /**
     * Specifies the driver
     *
     * @param $driver
     * @return mixed
     */
    public function with($driver)
    {
        return $this->driver($driver);
    }

    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new \InvalidArgumentException("No Payment driver was specified.");
    }


    /**
     * AfterpayDriver
     *
     * @return mixed
     */
    protected function createAfterpayDriver()
    {
        $config = $this->app['config']['services.afterpay'];

        return $this->buildProvider(
            'Codeboard\Payments\Local\AfterpayProvider', $config
        );
    }

    /**
     * @param $provider
     * @param $config
     * @return mixed
     */
    protected function buildProvider($provider, $config)
    {
        return new $provider(
            $this->app['request'], $config['merchant_id'],
            $config['portfolio_id'], $config['password'],
            $config['modus']
        );
    }
}