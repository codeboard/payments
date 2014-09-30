<?php

use Mockery as m;
use Illuminate\Http\Request;

class AfterpayTest extends PHPUnit_Framework_TestCase {

    public function tearDown()
    {
        m::close();
    }

    public function testUrlResponse()
    {
        $service = m::mock('Codeboard\Payments\Local\Afterpay');

    }

}