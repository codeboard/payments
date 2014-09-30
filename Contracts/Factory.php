<?php  namespace Codeboard\Payments\Contracts;

interface Factory {

    /**
     * Get an Payment provider implementation.
     *
     * @param string $driver
     * @return mixed
     */
    public function driver($driver = null);

} 