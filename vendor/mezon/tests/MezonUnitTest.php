<?php
require_once (__DIR__ . '/../../conf/conf.php');

require_once (__DIR__ . '/../mezon.php');

\Mezon\add_connection_to_config('default-db-connection', 'mysql:dbname=record;host=localhost', 'root', '');

class MezonUnitTest extends PHPUnit\Framework\TestCase
{

	/**
	 * Testing get_db_connection
	 */
	public function test_get_db_connection()
	{
		$Connection = \Mezon\Mezon::get_db_connection();

		$this->assertInstanceOf('\Mezon\PDOCrud', $Connection);
	}
}

?>