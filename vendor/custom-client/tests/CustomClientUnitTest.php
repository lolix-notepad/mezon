<?php
require_once (__DIR__ . '/../custom-client.php');

class CustomClientUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing invalid construction
     */
    public function test_constructor_invalid(): void
    {
        try {
            $Client = new CustomClient(false);
            $this->fail('Exception was not thrown ' . serialize($Client));
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }

        try {
            $Client = new CustomClient('');
            $this->fail('Exception was not thrown ' . serialize($Client));
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing valid construction
     */
    public function test_constructor_valid(): void
    {
        $Client = new CustomClient('http://yandex.ru/', [
            'header'
        ]);

        $this->assertEquals('http://yandex.ru', $Client->get_url(), 'Invalid URL');
        $this->assertEquals(1, count($Client->get_headers()), 'Invalid headers');
    }

    /**
     * Testing getters/setters for the field
     */
    public function test_idempotency_get_set(): void
    {
        // setup
        $Client = new CustomClient('some url', []);

        // test bodyand assertions
        $Client->set_idempotency_key('i-key');

        $this->assertEquals('i-key', $Client->get_idempotency_key(), 'Invalid idempotency key');
    }

    /**
     * Creating mock
     */
    protected function get_mock(): object
    {
        $Mock = $this->getMockBuilder('CustomClient')
            ->setMethods([
            'send_request'
        ])
            ->disableOriginalConstructor()
            ->getMock();

        return ($Mock);
    }

    /**
     * Testing get_request method
     */
    public function test_get_request(): void
    {
        // setup
        $Client = $this->get_mock();
        $Client->method('send_request')->willReturn([
            'result',
            1
        ]);

        // test body
        $Result = $Client->get_request('/end-point/');

        // assertions
        $this->assertEquals('result', $Result);
    }

    /**
     * Testing post_request method
     */
    public function test_post_request(): void
    {
        // setup
        $Client = $this->get_mock();
        $Client->method('send_request')->willReturn([
            'result',
            1
        ]);

        // test body
        $Result = $Client->post_request('/end-point/');

        // assertions
        $this->assertEquals('result', $Result);
    }
}

?>