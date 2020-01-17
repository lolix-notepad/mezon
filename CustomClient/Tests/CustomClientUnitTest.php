<?php

class CustomClientUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing invalid construction
     */
    public function testConstructorInvalid(): void
    {
        $this->expectException(Exception::class);

        new \Mezon\CustomClient\CustomClient(false);

        $this->expectException(Exception::class);

        new \Mezon\CustomClient\CustomClient('');
    }

    /**
     * Testing valid construction
     */
    public function testConstructorValid(): void
    {
        $client = new \Mezon\CustomClient\CustomClient('http://yandex.ru/', [
            'header'
        ]);

        $this->assertEquals('http://yandex.ru', $client->getUrl(), 'Invalid URL');
        $this->assertEquals(1, count($client->getHeaders()), 'Invalid headers');
    }

    /**
     * Testing getters/setters for the field
     */
    public function testIdempotencyGetSet(): void
    {
        // setup
        $client = new \Mezon\CustomClient\CustomClient('some url', []);

        // test bodyand assertions
        $client->setIdempotencyKey('i-key');

        $this->assertEquals('i-key', $client->getIdempotencyKey(), 'Invalid idempotency key');
    }

    /**
     * Creating mock
     */
    protected function getMock(): object
    {
        // TODO replace setMethods with something else what is recommended
        return $this->getMockBuilder(\Mezon\CustomClient\CustomClient::class)
            ->setMethods([
            'sendRequest'
        ])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Testing getRequest method
     */
    public function testGetRequest(): void
    {
        // setup
        $client = $this->getMock();
        $client->method('sendRequest')->willReturn([
            'result',
            1
        ]);

        // test body
        $result = $client->getRequest('/end-point/');

        // assertions
        $this->assertEquals('result', $result);
    }

    /**
     * Testing postRequest method
     */
    public function testPostRequest(): void
    {
        // setup
        $client = $this->getMock();
        $client->method('sendRequest')->willReturn([
            'result',
            1
        ]);

        // test body
        $result = $client->postRequest('/end-point/');

        // assertions
        $this->assertEquals('result', $result);
    }
}
