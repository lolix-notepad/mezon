<?php
require_once (__DIR__ . '/../rest-exception.php');

class RestExceptionUnitTest extends PHPUnit\Framework\TestCase
{

	/**
	 * Testing invalid construction
	 */
	public function testConstructor()
	{
		$Object = new \Mezon\Service\ServiceRESTTransport\RESTException('msg', 1, 200, 'body', 'http://ya.ru', [
			1,
			2
		]);

		$this->assertEquals('msg', $Object->getMessage(), 'Invalid message');
		$this->assertEquals(1, $Object->getCode(), 'Invalid code');
		$this->assertEquals(200, $Object->getHTTPCode(), 'Invalid HTTP code');
		$this->assertEquals('body', $Object->getHTTPBody(), 'Invalid HTTP body');
		$this->assertEquals('http://ya.ru', $Object->getURL(), 'Invalid URL');
		$this->assertEquals(2,count($Object->getOptions()),'Invalid options');
	}
}

?>