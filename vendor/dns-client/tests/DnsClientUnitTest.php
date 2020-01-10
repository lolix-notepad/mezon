<?php
require_once (__DIR__ . '/../../../autoloader.php');

class DnsClientUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Common setup for all tests
     */
    public function setUp(): void
    {
        \Mezon\DnsClient::clear();
        \Mezon\DnsClient::setService('auth', 'auth.local');
        \Mezon\DnsClient::setService('author', 'author.local');
    }

    /**
     * Testing constructor
     */
    public function testGetServices(): void
    {
        // setup and test body
        $Services = \Mezon\DnsClient::getServices();

        // assertions
        $this->assertEquals('auth, author', $Services);
    }

    /**
     * Testing service existence
     */
    public function testServiceExists(): void
    {
        $this->assertTrue(\Mezon\DnsClient::serviceExists('auth'), 'Existing service was not found');

        $this->assertFalse(\Mezon\DnsClient::serviceExists('unexisting'), 'Unexisting service was found');
    }

    /**
     * Testing resolving unexisting host
     */
    public function testResolveUnexistingHost(): void
    {
        // setup
        $this->expectException(\Exception::class);

        // test body and assertions
        \Mezon\DnsClient::resolveHost('unexisting');
    }

    /**
     * Testing resolving existing host
     */
    public function testResolveHost(): void
    {
        // test body and assertions
        $URL = \Mezon\DnsClient::resolveHost('auth');
        $this->assertEquals('auth.local', $URL, 'Invalid URL was fetched');
    }

    /**
     * Testing setService method
     */
    public function testSetService(): void
    {
        // setup and test body
        \Mezon\DnsClient::setService('service-name', 'http://example.com');

        // assertions
        $this->assertTrue(\Mezon\DnsClient::serviceExists('service-name'));
    }
}
