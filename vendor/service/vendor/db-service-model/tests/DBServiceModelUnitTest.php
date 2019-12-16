<?php
require_once (__DIR__ . '/../db-service-model.php');
require_once (__DIR__ . '/../../../../gui/vendor/fields-algorithms/fields-algorithms.php');

class DBServiceModelUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     * Method returns mock of the DB connection
     *
     * @return object Mock of the connection
     */
    protected function get_connection_mock()
    {
        $Mock = $this->getMockBuilder('PDOCRUD')
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
        $Mock = $this->getMockBuilder('DBServiceModel')
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
                new FieldsAlgorithms([
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
        $Model = new DBServiceModel($Data, 'entity_name');

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
            new DBServiceModel(new stdClass(), 'entity_name');
            // assertions
            $this->fail('Exception in constructor must be thrown');
        } catch (Exception $e) {
            // assertions
            $this->addToAssertionCount(1);
        }
    }
}

?>