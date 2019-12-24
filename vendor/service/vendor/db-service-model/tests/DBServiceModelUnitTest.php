<?php
require_once (__DIR__ . '/../db-service-model.php');
require_once (__DIR__ . '/../../../../gui/vendor/fields-algorithms/fields-algorithms.php');

class DBServiceModelUnitTest extends PHPUnit\Framework\TestCase
{

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
     * @dataProvider constructorTestData
     */
    public function testConstructor($Data, string $Origin)
    {
        // setup and test body
        $Model = new \Mezon\Service\DBServiceModel($Data, 'entity_name');

        // assertions
        $this->assertTrue($Model->hasField($Origin), 'Invalid contruction');
    }

    /**
     * Testing constructor with exception
     */
    public function testConstructorException()
    {
        try {
            // setup and test body
            new \Mezon\Service\DBServiceModel(new stdClass(), 'entity_name');
            // assertions
            $this->fail('Exception in constructor must be thrown');
        } catch (Exception $e) {
            // assertions
            $this->addToAssertionCount(1);
        }
    }
}

?>