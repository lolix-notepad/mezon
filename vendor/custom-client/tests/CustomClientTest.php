<?php
require_once (__DIR__ . '/../../../autoloader.php');

class CustomClientTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing get method
     */
    public function testGetMethod()
    {
        $Client = new \Mezon\CustomClient('http://yandex.ru/');

        $this->expectException(Exception::class);

        $Client->getRequest('unexisting');
    }

    /**
     * Testing post metthod
     */
    public function testPostMethod()
    {
        $Client = new \Mezon\CustomClient('http://yandex.ru/');

        $this->expectException(Exception::class);

        $Client->postRequest('unexisting');
    }
}
