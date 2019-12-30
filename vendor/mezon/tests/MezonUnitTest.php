<?php
require_once (__DIR__ . '/../../../autoloader.php');

class MezonUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     *
     * {@inheritdoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    public function setUp()
    {
        \Mezon\Conf::addConnectionToConfig('default-db-connection', 'mysql:dbname=record;host=localhost', 'root', '');
    }

    /**
     * Testing get_db_connection
     */
    public function testGetDbConnection()
    {
        $Connection = \Mezon\Mezon::getDbConnection();

        $this->assertInstanceOf(\Mezon\PDOCrud::class, $Connection);
    }
}

?>