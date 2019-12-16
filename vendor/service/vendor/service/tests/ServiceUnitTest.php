<?php
require_once (__DIR__ . '/../../service-unit-tests/service-unit-tests.php');

require_once (__DIR__ . '/test-service.php');

class ServiceUnitTest extends ServiceUnitTests
{

	/**
	 * Method tests does custom routes were loaded.
	 * Trying to read routes both from php and json file and call routes from them.
	 */
	public function test_custom_routes_loading()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$Service = new TestService('ServiceConsoleTransport', $this->get_security_provider(AS_STRING), 'TestLogic');

		try {
			// route from routes.php
			$_GET['r'] = 'test';
			$Service->run();
			$this->addToAssertionCount(1);
		} catch (Exception $e) {
			$this->fail('Route "test" was not handled');
		}

		try {
			// route from routes.json
			$_GET['r'] = 'test2';
			$Service->run();
			$this->addToAssertionCount(1);
		} catch (Exception $e) {
			$this->fail('Route "test2" was not handled');
		}
	}
}

?>