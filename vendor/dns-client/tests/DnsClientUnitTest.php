<?php

$DNSRecords = [
	'auth' => 'auth.local',
	'author' => 'author.local',
	'invalid'=> 1
];

// output list of services for debug purposes if any test will fail
var_dump($DNSRecords);

class DnsClientUnitTest extends PHPUnit\Framework\TestCase
{

	/**
	 * Testing constructor
	 */
	public function testGetServices()
	{
		// setup and test body
		$Services = \Mezon\DNS::getServices();

		// assertions
		$this->assertEquals('auth, author, invalid', $Services);
	}

	/**
	 * Testing service existence
	 */
	public function testServiceExists()
	{
	    $this->assertTrue(\Mezon\DNS::serviceExists('auth'), 'Existing service was not found');

	    $this->assertFalse(\Mezon\DNS::serviceExists('unexisting'), 'Unexisting service was found');
	}

	/**
	 * Testing resolving unexisting host
	 */
	public function testResolveUnexistingHost()
	{
		// test body and assertions
		try {
		    \Mezon\DNS::resolveHost('unexisting');
			$this->fails('Exception must be thrown');
		} catch (Exception $e) {
			$this->addToAssertionCount(1);
		}
	}

	/**
	 * Testing resolving existing host
	 */
	public function testResolveHost()
	{
		// test body and assertions
	    $URL = \Mezon\DNS::resolveHost('auth');
		$this->assertEquals('auth.local', $URL, 'Invalid URL was fetched');
	}

	/**
	 * Testing resolving invalid host
	 */
	public function testResolveInvalidHost()
	{
		// test body and assertions
		try {
		    \Mezon\DNS::resolveHost('invalid');
			$this->fails('Exception must be thrown');
		} catch (Exception $e) {
			$this->addToAssertionCount(1);
		}
	}
}

?>