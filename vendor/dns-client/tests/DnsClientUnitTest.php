<?php
require_once (__DIR__ . '/../../../autoloader.php');

$DNSRecords = [
    'auth' => 'auth.local',
    'author' => 'author.local',
    'invalid' => 1
];

class DnsClientUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testGetServices(): void
    {
        // setup and test body
        $Services = \Mezon\DnsClient::getServices();

        // assertions
        $this->assertEquals('auth, author, invalid', $Services);
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
        // test body and assertions
        try {
            \Mezon\DnsClient::resolveHost('unexisting');
            $this->fails('Exception must be thrown');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
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
     * Testing resolving invalid host
     */
    public function testResolveInvalidHost(): void
    {
        // test body and assertions
        try {
            \Mezon\DnsClient::resolveHost('invalid');
            $this->fails('Exception must be thrown');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
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
