<?php
require_once (__DIR__ . '/../dns-client.php');

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
	public function test_get_services()
	{
		// setup and test body
		$Services = \Mezon\DNS::get_services();

		// assertions
		$this->assertEquals('auth, author, invalid', $Services);
	}

	/**
	 * Testing service existence
	 */
	public function test_service_exists()
	{
	    $this->assertTrue(\Mezon\DNS::service_exists('auth'), 'Existing service was not found');

	    $this->assertFalse(\Mezon\DNS::service_exists('unexisting'), 'Unexisting service was found');
	}

	/**
	 * Testing resolving unexisting host
	 */
	public function test_resolve_unexisting_host()
	{
		// test body and assertions
		try {
		    \Mezon\DNS::resolve_host('unexisting');
			$this->fails('Exception must be thrown');
		} catch (Exception $e) {
			$this->addToAssertionCount(1);
		}
	}

	/**
	 * Testing resolving existing host
	 */
	public function test_resolve_host()
	{
		// test body and assertions
	    $URL = \Mezon\DNS::resolve_host('auth');
		$this->assertEquals('auth.local', $URL, 'Invalid URL was fetched');
	}

	/**
	 * Testing resolving invalid host
	 */
	public function test_resolve_invalid_host()
	{
		// test body and assertions
		try {
		    \Mezon\DNS::resolve_host('invalid');
			$this->fails('Exception must be thrown');
		} catch (Exception $e) {
			$this->addToAssertionCount(1);
		}
	}
}

?>