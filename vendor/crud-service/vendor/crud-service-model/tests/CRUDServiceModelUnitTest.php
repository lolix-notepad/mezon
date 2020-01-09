<?php
require_once (__DIR__ . '/../crud-service-model.php');
require_once (__DIR__ . '/../../../../gui/vendor/fields-algorithms/fields-algorithms.php');

class CrudServiceModelUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Method returns mock of the DB connection
     *
     * @param int $Mode
     *            - Mock creation mode
     * @param array $Methods
     *            - Methods tto be mocked
     * @return object Mock of the connection
     */
    protected function getConnectionMock_old(int $Mode, array $Methods = [
        'select'
    ])
    {
        $Mock = $this->getMockBuilder(\Mezon\PdoCrud::class)
            ->disableOriginalConstructor()
            ->setMethods($Methods)
            ->getMock();

        switch ($Mode) {
            case 0:
                $Mock->method('select')->willReturn([]);
                break;
            case 1:
                $Mock->method('select')->willReturn([
                    [
                        'records_count' => 1
                    ]
                ]);
                break;
            case 2:
                $Mock->method('select')->willReturn([
                    [],
                    []
                ]);
                break;
            case 3:
                $Mock->expects($this->once())
                    ->method('insert')
                    ->willReturn(1);
                break;
            case 4:
                $Mock->expects($this->once())
                    ->method('delete')
                    ->willReturn(1);
                break;
        }

        return ($Mock);
    }

    /**
     * Method returns mock of the DB connection
     *
     * @return object Mock of the connection
     */
    protected function getConnectionMock()
    {
        $Mock = $this->getMockBuilder(\Mezon\PdoCrud::class)
            ->disableOriginalConstructor()
            ->setMethods([
            'select',
            'delete',
            'update',
            'insert'
        ])
            ->getMock();

        return ($Mock);
    }

    /**
     * Method returns model's mock
     *
     * @param object $ConnectionMock
     *            Mock of the connection
     * @return object Mock of the model
     */
    protected function getModelMock($ConnectionMock)
    {
        $Mock = $this->getMockBuilder(\Mezon\CrudService\CrudServiceModel::class)
            ->setConstructorArgs([
            [
                'id' => [
                    'type' => 'integer'
                ]
            ],
            'table-name'
        ])
            ->setMethods([
            'getConnection',
            'getRecordsTransformer'
        ])
            ->getMock();

        $Mock->method('getConnection')->willReturn($ConnectionMock);

        return ($Mock);
    }

    /**
     * Method is testing default value return for empty table
     */
    public function testRecordsCount0()
    {
        // setup
        $Connection = $this->getConnectionMock();
        $Connection->expects($this->once())
            ->method('select')
            ->willReturn([]);
        $Mock = $this->getModelMock($Connection);

        // test body and asssertions
        $this->assertEquals(0, $Mock->recordsCount(), 'Invalid error was returned');
    }

    /**
     * Method is testing default value return for empty table
     */
    public function testRecordsCount1()
    {
        // setup
        $Connection = $this->getConnectionMock();
        $Connection->expects($this->once())
            ->method('select')
            ->willReturn([
            [
                'records_count' => 1
            ]
        ]);
        $Mock = $this->getModelMock($Connection);

        // test body and assertions
        $this->assertEquals(1, $Mock->recordsCount(), 'Invalid error was returned');
    }

    /**
     * Method tests insertBasicFields method
     */
    public function testInsertBasicFields()
    {
        // setup
        $Connection = $this->getConnectionMock();
        $Connection->expects($this->once())
            ->method('insert')
            ->willReturn(1);
        $Mock = $this->getModelMock($Connection);

        // test body
        $Result = $Mock->insertBasicFields([
            'title' => 'title'
        ]);

        // assertions
        $this->assertTrue(isset($Result['id']), 'Invalid record was returned');
        $this->assertTrue(isset($Result['title']), 'Invalid record was returned');
    }

    /**
     * Data provider for the testDeleteFiltered
     *
     * @return array Data
     */
    public function deleteFilteredTestData(): array
    {
        return ([
            [
                false
            ],
            [
                1
            ]
        ]);
    }

    /**
     * Method tests deleteFiltered method
     *
     * @param mixed $DomainId
     *            Domain id
     *            
     * @dataProvider deleteFilteredTestData
     */
    public function testDeleteFiltered($DomainId)
    {
        // setup
        $Connection = $this->getConnectionMock();
        $Connection->expects($this->once())
            ->method('delete')
            ->willReturn(1);
        $Mock = $this->getModelMock($Connection);

        // test body and assertions
        $Mock->deleteFiltered($DomainId, [
            'title LIKE "title"'
        ]);
    }

    /**
     * Test data for test_constructor test
     *
     * @return array
     */
    public function constructorTestData(): array
    {
        return ([
            [
                [
                    'id' => [
                        'type' => 'intger'
                    ]
                ],
                'id'
            ],
            [
                '*',
                '*'
            ],
            [
                new \Mezon\Gui\FieldsAlgorithms([
                    'id' => [
                        'type' => 'intger'
                    ]
                ]),
                'id'
            ]
        ]);
    }

    /**
     * Testing constructor
     *
     * @param mixed $Data
     *            Parameterfor constructor
     * @param string $Origin
     *            original data for validation
     * @dataProvider constructorTestData
     */
    public function testConstructor($Data, string $Origin)
    {
        // setup and test body
        $Model = new \Mezon\CrudService\CrudServiceModel($Data, 'entity_name');

        // assertions
        $this->assertTrue($Model->hasField($Origin), 'Invalid contruction');
    }

    /**
     * Testing constructor with exception
     */
    public function testConstructorException()
    {
        $this->expectException(Exception::class);

        new \Mezon\CrudService\CrudServiceModel(new stdClass(), 'entity_name');
    }

    /**
     * Testing newRecordsSince
     */
    public function testNewRecordsSince()
    {
        // setup
        $Connection = $this->getConnectionMock();
        $Connection->method('select')->willReturn([
            [],
            []
        ]);

        $Model = $this->getModelMock($Connection);

        // test body
        $Records = $Model->newRecordsSince(false, '2012-01-01');

        // assertions
        $this->assertEquals(2, count($Records), 'Invalid count of new records');
    }

    /**
     * Testing getSimpleRecords without domain
     */
    public function testGetSimpleRecordsWithoutDomain()
    {
        // setup
        $Connection = $this->getConnectionMock();
        $Connection->method('select')->willReturn([
            [],
            []
        ]);

        $Model = $this->getModelMock($Connection);

        // test body
        $Records = $Model->getSimpleRecords(false, 0, 2, [], [
            'field' => 'id',
            'order' => 'ASC'
        ]);

        // assertions
        $this->assertEquals(2, count($Records), 'Invalid count of not transformed records');
    }

    /**
     * Testing getSimpleRecords with domain
     */
    public function testGetSimpleRecordsWithDomain()
    {
        // setup
        $Connection = $this->getConnectionMock();
        $Connection->method('select')->willReturn([
            [],
            []
        ]);

        $Model = $this->getModelMock($Connection);

        // test body
        $Records = $Model->getSimpleRecords(1, 0, 2, [], [
            'field' => 'id',
            'order' => 'ASC'
        ]);

        // assertions
        $this->assertEquals(2, count($Records), 'Invalid count of not transformed records');
    }

    /**
     * Testing fetchRecordsByIds with domain
     */
    public function testFetchRecordsByIdsWithDomain()
    {
        // setup
        $Connection = $this->getConnectionMock();
        $Connection->method('select')->willReturn([
            [],
            []
        ]);

        $Model = $this->getModelMock($Connection);

        // test body
        $Records = $Model->fetchRecordsByIds(1, "1,2");

        // assertions
        $this->assertEquals(2, count($Records), 'Invalid count of fetched by ids records');
    }

    /**
     * Testing fetchRecordsByIds with domain
     */
    public function testFetchRecordsByIdsWithoutDomain()
    {
        // setup
        $Connection = $this->getConnectionMock();
        $Connection->method('select')->willReturn([
            [],
            []
        ]);

        $Model = $this->getModelMock($Connection);

        // test body
        $Records = $Model->fetchRecordsByIds(false, "1,2");

        // assertions
        $this->assertEquals(2, count($Records), 'Invalid count of fetched by ids records');
    }

    /**
     * Testing fetchRecordsByIds not found
     */
    public function testFetchRecordsByIdsNotFound()
    {
        // setup
        $Connection = $this->getConnectionMock();
        $Connection->method('select')->willReturn([]);

        $Model = $this->getModelMock($Connection);

        // test body and assertions
        $this->expectException(Exception::class);

        $Model->fetchRecordsByIds("1,2", false);
    }

    /**
     * Data provider
     *
     * @return array Test data
     */
    public function recordsCountByFieldProvider(): array
    {
        return ([
            [
                [
                    'id' => 1,
                    'records_count' => 2
                ],
                2
            ],
            [
                [],
                0
            ]
        ]);
    }

    /**
     * Testing recordsCountByField method
     *
     * @dataProvider recordsCountByFieldProvider
     */
    public function testRecordsCountByField(array $SelectResult, int $Count)
    {
        // setup
        $Connection = $this->getConnectionMock();
        $Connection->method('select')->willReturn($SelectResult);

        $Model = $this->getModelMock($Connection);

        // test body
        $Result = $Model->recordsCountByField(false, 'id', []);

        // assertions
        $this->assertEquals($Count, $Result['records_count'], 'Invalid records count was fetched');
    }

    /**
     * Method tests last N records returning
     */
    public function testLastRecords()
    {
        // setup
        $Connection = $this->getConnectionMock();
        $Connection->method('select')->willReturn([
            [],
            []
        ]);
        $Mock = $this->getModelMock($Connection);
        $Mock->expects($this->once())
            ->method('getRecordsTransformer');

        // test body
        $Records = $Mock->lastRecords(false, 2, [
            '1 = 1'
        ]);

        // assertions
        $this->assertEquals(2, count($Records), 'Invalid amount of records was returned');
    }

    /**
     * Testing getRecords method
     */
    public function testGetRecords()
    {
        // setup
        $Connection = $this->getConnectionMock();
        $Connection->method('select')->willReturn([
            [
                'id' => 1
            ]
        ]);

        $Mock = $this->getModelMock($Connection);
        $Mock->expects($this->once())
            ->method('getRecordsTransformer');

        // test body
        $Result = $Mock->getRecords(0, 0, 1);

        // assertions
        $this->assertCount(1, $Result);
    }

    /**
     * Method tests updateBasicFields method
     */
    public function testUpdateBasicFields()
    {
        // setup
        $Connection = $this->getConnectionMock();
        $Connection->expects($this->once())
            ->method('update');
        $Mock = $this->getModelMock($Connection);

        // test body and assertions
        $Mock->updateBasicFields(false, [
            'id' => 1
        ], [
            '1=1'
        ]);
    }
}
