<?php
require_once (__DIR__ . '/../service-mock-security-provider.php');

class ServiceMockSecurityProviderUnitTest extends PHPUnit\Framework\TestCase
{

	/**
	 * Testing session creation.
	 */
	public function test_create_session_1()
	{
		$Provider = new ServiceMockSecurityProvider();

		$Token = $Provider->create_session();

		$this->assertEquals(32, strlen($Token), 'Invalid token was returned');
	}
}

?>