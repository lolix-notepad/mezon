<?php
require_once (__DIR__ . '/../curl-wrapper.php');

class CurlWrapperTest extends PHPUnit\Framework\TestCase
{

	/**
	 * Testing GET requests
	 */
	public function test_get_request()
	{
		list ($Body, $Code) = \Mezon\CustomClient\CurlWrapper::send_request('http://google.com', [], 'GET');

		$this->assertContains('', $Body, 'Invalid HTML was returned');
		$this->assertEquals(301, $Code, 'Invalid HTTP code');
	}

	/**
	 * Testing POST requests
	 */
	public function test_post_request()
	{
	    list ($Body, $Code) = \Mezon\CustomClient\CurlWrapper::send_request('http://google.com', [], 'POST', [
			'data' => 1
		]);

		$this->assertContains('', $Body, 'Invalid HTML was returned');
		$this->assertEquals(405, $Code, 'Invalid HTTP code');
	}
}

?>