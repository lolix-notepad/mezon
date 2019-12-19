<?php
require_once (__DIR__ . '/../crud-service-model.php');
require_once (__DIR__ . '/../../../../gui/vendor/fields-algorithms/fields-algorithms.php');

class CRUDServiceModelUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     * Method returns mock of the DB connection
     *
     * @param integer $Mode
     *            - Mock creation mode
     * @param array $Methods
     *            - Methods tto be mocked
     * @return object Mock of the connection
     */
    protected function get_connection_mock_old(int $Mode, array $Methods = [
        'select'
    ])
    {
        $Mock = $this->getMockBuilder('\Mezon\PDOCRUD')
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
    protected function get_connection_mock()
    {
        $Mock = $this->getMockBuilder('\Mezon\PDOCRUD')
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
    protected function get_model_mock($ConnectionMock)
    {
        $Mock = $this->getMockBuilder('\Mezon\CRUDService\CRUDServiceModel')
            ->setConstructorArgs([
            [
                'id' => [
                    'type' => 'integer'
                ]
            ],
            'table-name'
        ])
            ->setMethods([
            'get_connection',
            'get_records_transformer'
        ])
            ->getMock();

        $Mock->method('get_connection')->willReturn($ConnectionMock);

        return ($Mock);
    }

    /**
     * Method is testing default value return for empty table
     */
    public function test_records_count0()
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->expects($this->once())
            ->method('select')
            ->willReturn([]);
        $Mock = $this->get_model_mock($Connection);

        // test body and asssertions
        $this->assertEquals(0, $Mock->records_count(), 'Invalid error was returned');
    }

    /**
     * Method is testing default value return for empty table
     */
    public function test_records_count1()
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->expects($this->once())
            ->method('select')
            ->willReturn([
            [
                'records_count' => 1
            ]
        ]);
        $Mock = $this->get_model_mock($Connection);

        // test body and assertions
        $this->assertEquals(1, $Mock->records_count(), 'Invalid error was returned');
    }

    /**
     * Method tests insert_basic_fields method
     */
    public function test_insert_basic_fields()
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->expects($this->once())
            ->method('insert')
            ->willReturn(1);
        $Mock = $this->get_model_mock($Connection);

        // test body
        $Result = $Mock->insert_basic_fields([
            'title' => 'title'
        ]);

        // assertions
        $this->assertTrue(isset($Result['id']), 'Invalid record was returned');
        $this->assertTrue(isset($Result['title']), 'Invalid record was returned');
    }

    /**
     * Data provider for the test_delete_filtered
     *
     * @return array Data
     */
    public function delete_filtered_test_data(): array
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
     * Method tests delete_filtered method
     *
     * @param mixed $DomainId
     *            Domain id
     *            
     * @dataProvider delete_filtered_test_data
     */
    public function test_delete_filtered($DomainId)
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->expects($this->once())
            ->method('delete')
            ->willReturn(1);
        $Mock = $this->get_model_mock($Connection);

        // test body and assertions
        $Mock->delete_filtered($DomainId, [
            'title LIKE "title"'
        ]);
    }

    /**
     * Test data for test_constructor test
     *
     * @return array
     */
    public function constructor_test_data(): array
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
                new \Mezon\GUI\FieldsAlgorithms([
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
     * @dataProvider constructor_test_data
     */
    public function test_constructor($Data, string $Origin)
    {
        // setup and test body
        $Model = new \Mezon\CRUDService\CRUDServiceModel($Data, 'entity_name');

        // assertions
        $this->assertTrue($Model->has_field($Origin), 'Invalid contruction');
    }

    /**
     * Testing constructor with exception
     */
    public function test_constructor_exception()
    {
        try {
            // setup and test body
            new \Mezon\CRUDService\CRUDServiceModel(new stdClass(), 'entity_name');
            // assertions
            $this->fail('Exception in constructor must be thrown');
        } catch (Exception $e) {
            // assertions
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing new_records_since
     */
    public function test_new_records_since()
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->method('select')->willReturn([
            [],
            []
        ]);

        $Model = $this->get_model_mock($Connection);

        // test body
        $Records = $Model->new_records_since(false, '2012-01-01');

        // assertions
        $this->assertEquals(2, count($Records), 'Invalid count of new records');
    }

    /**
     * Testing get_simple_records without domain
     */
    public function test_get_simple_records_without_domain()
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->method('select')->willReturn([
            [],
            []
        ]);

        $Model = $this->get_model_mock($Connection);

        // test body
        $Records = $Model->get_simple_records(false, 0, 2, [], [
            'field' => 'id',
            'order' => 'ASC'
        ]);

        // assertions
        $this->assertEquals(2, count($Records), 'Invalid count of not transformed records');
    }

    /**
     * Testing get_simple_records with domain
     */
    public function test_get_simple_records_with_domain()
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->method('select')->willReturn([
            [],
            []
        ]);

        $Model = $this->get_model_mock($Connection);

        // test body
        $Records = $Model->get_simple_records(1, 0, 2, [], [
            'field' => 'id',
            'order' => 'ASC'
        ]);

        // assertions
        $this->assertEquals(2, count($Records), 'Invalid count of not transformed records');
    }

    /**
     * Testing fetch_records_by_ids with domain
     */
    public function test_fetch_records_by_ids_with_domain()
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->method('select')->willReturn([
            [],
            []
        ]);

        $Model = $this->get_model_mock($Connection);

        // test body
        $Records = $Model->fetch_records_by_ids(1, "1,2");

        // assertions
        $this->assertEquals(2, count($Records), 'Invalid count of fetched by ids records');
    }

    /**
     * Testing fetch_records_by_ids with domain
     */
    public function test_fetch_records_by_ids_without_domain()
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->method('select')->willReturn([
            [],
            []
        ]);

        $Model = $this->get_model_mock($Connection);

        // test body
        $Records = $Model->fetch_records_by_ids(false, "1,2");

        // assertions
        $this->assertEquals(2, count($Records), 'Invalid count of fetched by ids records');
    }

    /**
     * Testing fetch_records_by_ids not found
     */
    public function test_fetch_records_by_ids_not_found()
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->method('select')->willReturn([]);

        $Model = $this->get_model_mock($Connection);

        // test body and assertions
        try {
            $Model->fetch_records_by_ids("1,2", false);
            $this->fail('Exception mustbe thrown');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Data provider
     *
     * @return array Test data
     */
    public function records_count_by_field_provider(): array
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
     * Testing records_count_by_field method
     *
     * @dataProvider records_count_by_field_provider
     */
    public function test_records_count_by_field(array $SelectResult, int $Count)
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->method('select')->willReturn($SelectResult);

        $Model = $this->get_model_mock($Connection);

        // test body
        $Result = $Model->records_count_by_field(false, 'id', []);

        // assertions
        $this->assertEquals($Count, $Result['records_count'], 'Invalid records count was fetched');
    }

    /**
     * Method tests last N records returning
     */
    public function test_last_records()
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->method('select')->willReturn([
            [],
            []
        ]);
        $Mock = $this->get_model_mock($Connection);
        $Mock->expects($this->once())
            ->method('get_records_transformer');

        // test body
        $Records = $Mock->last_records(false, 2, [
            '1 = 1'
        ]);

        // assertions
        $this->assertEquals(2, count($Records), 'Invalid amount of records was returned');
    }

    /**
     * Testing get_records method
     */
    public function test_get_records()
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->method('select')->willReturn([
            [
                'id' => 1
            ]
        ]);

        $Mock = $this->get_model_mock($Connection);
        $Mock->expects($this->once())
            ->method('get_records_transformer');

        // test body
        $Result = $Mock->get_records(0, 0, 1);

        // assertions
        $this->assertCount(1, $Result);
    }

    /**
     * Method tests update_basic_fields method
     */
    public function test_update_basic_fields()
    {
        // setup
        $Connection = $this->get_connection_mock();
        $Connection->expects($this->once())
            ->method('update');
        $Mock = $this->get_model_mock($Connection);

        // test body and assertions
        $Mock->update_basic_fields(false, [
            'id' => 1
        ], [
            '1=1'
        ]);
    }
}

?>