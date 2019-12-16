<?php
require_once (__DIR__ . '/../custom-client.php');

class CustomClientTest extends PHPUnit\Framework\TestCase
{

	/**
	 * Testing get method
	 */
	public function test_get_method()
	{
		$Client = new CustomClient('http://yandex.ru/');

		try {
			$Client->get_request('unexisting');
			$this->fail('Exception was not thrown');
		} catch (Exception $e) {
			$this->addToAssertionCount(1);
		}
	}

	/**
	 * Testing post metthod
	 */
	public function test_post_method()
	{
		$Client = new CustomClient('http://yandex.ru/');

		try {
			$Client->post_request('unexisting');
			$this->fail('Exception was not thrown');
		} catch (Exception $e) {
			$this->addToAssertionCount(1);
		}
	}
}

?>