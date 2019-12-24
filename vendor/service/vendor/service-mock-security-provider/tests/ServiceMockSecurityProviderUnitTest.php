<?php
require_once (__DIR__ . '/../service-mock-security-provider.php');

class ServiceMockSecurityProviderUnitTest extends PHPUnit\Framework\TestCase
{

	/**
	 * Testing session creation.
	 */
	public function testCreateSession1()
	{
		$Provider = new \Mezon\Service\ServiceMockSecurityProvider();

		$Token = $Provider->createSession();

		$this->assertEquals(32, strlen($Token), 'Invalid token was returned');
	}
}

?>