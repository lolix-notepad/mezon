<?php
require_once ('autoload.php');

class CustomClientTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing get method
     */
    public function testGetMethod()
    {
        $Client = new \Mezon\CustomClient\CustomClient('http://yandex.ru/');

        $Client->getRequest('unexisting');

        $this->addToAssertionCount(1);
    }

    /**
     * Testing post metthod
     */
    public function testPostMethod()
    {
        $Client = new \Mezon\CustomClient\CustomClient('http://yandex.ru/');

        $Client->postRequest('unexisting');

        $this->addToAssertionCount(1);
    }
}
