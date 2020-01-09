<?php
require_once (__DIR__ . '/../../../../../autoloader.php');

class CrudServiceModelDBTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Setting up testing environment
     */
    public function setUp()
    {
        \Mezon\addConnectionToConfig('default-db-connection', 'mysql:host=localhost;dbname=record', 'root', '');
    }

    /**
     * Method returns model's mock.
     *
     * @param object $Connection
     *            - Connection to the database.
     * @return object Mock of the model.
     */
    protected function getModelMock($Connection)
    {
        $Mock = $this->getMockBuilder(\Mezon\CrudService\CrudServiceModel::class)
            ->setMethods([
            'getConnection'
        ])
            ->getMock();

        $Mock->method('getConnection')->willReturn($Connection);

        $Mock->TableName = 'records';

        return ($Mock);
    }

    /**
     * Method returns connection to the database.
     *
     * @return Connection.
     */
    protected function getConnection()
    {
        $Connection = new \Mezon\PdoCrud();
        $Connection->connect([
            "dsn" => "mysql:host=localhost;dbname=record",
            "user" => "root",
            "password" => ""
        ]);

        return ($Connection);
    }

    /**
     * Method tests last N records returning.
     */
    public function testLastRecordsAll()
    {
        $Mock = $this->getModelMock($this->getConnection());

        $this->assertEquals(count($Mock->lastRecords(2, [
            '1 = 1'
        ])), 2, 'Invalid amount of records was returned (all)');
    }

    /**
     * Method tests last N records returning.
     */
    public function testLastRecordsLimited()
    {
        $Mock = $this->getModelMock($this->getConnection());

        $this->assertEquals(count($Mock->lastRecords(1, [
            '1 = 1'
        ])), 1, 'Invalid amount of records was returned (limited)');
    }

    /**
     * Method tests last N records returning.
     */
    public function testLastRecordsQuery()
    {
        $Mock = $this->getModelMock($this->getConnection());

        $Result = $Mock->lastRecords(2, [
            'id > 1'
        ]);

        $this->assertEquals('field', $Result[0]['name'], 'Invalid amount of records was returned');
    }

    /**
     * Method tests record insertion.
     */
    public function testInsertBasicFields()
    {
        $Mock = $this->getModelMock($this->getConnection());

        $Record = $Mock->insertBasicFields([
            'name' => 'new name'
        ]);

        $this->assertTrue(isset($Record['id']), 'Record was not created');
    }

    /**
     * Method tests record deletion.
     */
    public function testDeleteFiltered()
    {
        $Mock = $this->getModelMock($this->getConnection());

        $Mock->deleteFiltered(false, [
            'name LIKE "new name"'
        ]);

        $this->addToAssertionCount(1);
    }

    /**
     * Testing records fetching.
     */
    public function testGetSimpleRecords()
    {
        $Model = new \Mezon\CrudService\CrudServiceModel('id', 'records');
        $Records = $Model->getSimpleRecords(false, 0, 1, [], [
            'field' => 'id',
            'order' => 'asc'
        ]);
        $this->assertEquals(1, count($Records), 'getSimpleRecords have returned nothing');
    }
}
