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

        try {
            $Client->getRequest('unexisting');
            $this->fail('Exception was not thrown');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing post metthod
     */
    public function testPostMethod()
    {
        $Client = new \Mezon\CustomClient('http://yandex.ru/');

        try {
            $Client->postRequest('unexisting');
            $this->fail('Exception was not thrown');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }
}
