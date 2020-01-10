<?php
require_once (__DIR__ . '/../functional.php');

/**
 * Transformation function multiplies 'foo' field
 */
function transform2x($Object)
{
    $Object->foo *= 2;

    return ($Object);
}

class FunctionalUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing getting field function
     */
    public function testGetFieldArray(): void
    {
        // setup
        $Arr = [
            'foo' => 'bar'
        ];

        // test body
        $Result = \Mezon\Functional::getField($Arr, 'foo');

        // assertions
        $this->assertEquals($Result, 'bar', 'Invalid value');
    }

    /**
     * Testing getting field function
     */
    public function testGetField2Array(): void
    {
        // setup
        $Arr = [
            'foo' => 'bar',
            'foo2' => 'bar2'
        ];

        // test body
        $Result = \Mezon\Functional::getField($Arr, 'foo2');

        // assertions
        $this->assertEquals($Result, 'bar2', 'Invalid value');
    }

    /**
     * Testing getting field function
     */
    public function testGetFieldObject(): void
    {
        // setup
        $obj = new stdClass();
        $obj->foo = 'bar';

        // test body
        $Result = \Mezon\Functional::getField($obj, 'foo');

        // assertions
        $this->assertEquals($Result, 'bar', 'Invalid value');
    }

    /**
     * Testing getting field function
     */
    public function testGetField2Object(): void
    {
        // setup
        $obj = new stdClass();
        $obj->foo = 'bar';
        $obj->foo2 = 'bar2';

        // test body
        $Result = \Mezon\Functional::getField($obj, 'foo2');

        // assertions
        $this->assertEquals($Result, 'bar2', 'Invalid value');
    }

    /**
     * Testing fields fetching
     */
    public function testFieldsFetching(): void
    {
        // setup
        $obj1 = new stdClass();
        $obj1->foo = 1;

        $obj2 = new stdClass();
        $obj2->foo = 2;

        $obj3 = new stdClass();
        $obj3->foo = 3;

        $Data = [
            $obj1,
            $obj2,
            $obj3
        ];

        // test body
        $Result = \Mezon\Functional::getFields($Data, 'foo');

        // assertions
        $this->assertEquals(count($Result), 3, 'Invalid count');

        $this->assertEquals($Result[0], 1, 'Invalid value');
        $this->assertEquals($Result[1], 2, 'Invalid value');
        $this->assertEquals($Result[2], 3, 'Invalid value');
    }

    /**
     * Testing fields setting
     */
    public function testFieldsSetting(): void
    {
        // setup
        $Values = [
            1,
            2,
            3
        ];
        $obj1 = new stdClass();
        $obj2 = new stdClass();

        $Data = [
            $obj1,
            $obj2
        ];

        // test body
        \Mezon\Functional::setFieldsInObjects($Data, 'foo', $Values);

        // assertions
        $this->assertEquals(count($Data), 3, 'Invalid count');

        $this->assertEquals($Data[0]->foo, 1, 'Invalid value');
        $this->assertEquals($Data[1]->foo, 2, 'Invalid value');
        $this->assertEquals($Data[2]->foo, 3, 'Invalid value');
    }

    /**
     * Testing fields summation
     */
    public function testFieldsSum(): void
    {
        // setup
        $obj1 = new stdClass();
        $obj1->foo = 1;

        $obj2 = new stdClass();
        $obj2->foo = 2;

        $obj3 = new stdClass();
        $obj3->foo = 3;

        $Data = [
            $obj1,
            $obj2,
            $obj3
        ];

        // test body and assertions
        $this->assertEquals(\Mezon\Functional::sumFields($Data, 'foo'), 6, 'Invalid sum');
    }

    /**
     * Method will test transformation function
     */
    public function testTransform(): void
    {
        // setup
        $obj1 = new stdClass();
        $obj1->foo = 1;

        $obj2 = new stdClass();
        $obj2->foo = 2;

        $obj3 = new stdClass();
        $obj3->foo = 3;

        $Data = [
            $obj1,
            $obj2,
            $obj3
        ];

        // test body
        \Mezon\Functional::transform($Data, 'transform2x');

        // assertions
        $this->assertEquals($Data[0]->foo, 2, 'Invalid value');
        $this->assertEquals($Data[1]->foo, 4, 'Invalid value');
        $this->assertEquals($Data[2]->foo, 6, 'Invalid value');
    }

    /**
     * Testing recursive fields summation
     */
    public function testRecursiveSum(): void
    {
        // setup
        $obj1 = new stdClass();
        $obj1->foo = 1;

        $obj2 = new stdClass();
        $obj2->foo = 2;

        $obj3 = new stdClass();
        $obj3->foo = 3;

        $Data = [
            $obj1,
            [
                $obj2,
                $obj3
            ]
        ];

        // test body and assertions
        $this->assertEquals(\Mezon\Functional::sumFields($Data, 'foo'), 6, 'Invalid sum');
    }

    /**
     * This method is testing filtration function
     */
    public function testFilterSimple(): void
    {
        // setup
        $obj1 = new stdClass();
        $obj1->foo = 1;

        $obj2 = new stdClass();
        $obj2->foo = 2;

        $obj3 = new stdClass();
        $obj3->foo = 1;

        $Data = [
            $obj1,
            $obj2,
            $obj3
        ];

        // test body and assertions
        $this->assertEquals(count(\Mezon\Functional::filter($Data, 'foo', '==', 1)), 2, 'Invalid filtration');
    }

    /**
     * This method is testing filtration function in a recursive mode
     */
    public function testFilterRecursive(): void
    {
        // setup
        $obj1 = new stdClass();
        $obj1->foo = 1;

        $obj2 = new stdClass();
        $obj2->foo = 2;

        $obj3 = new stdClass();
        $obj3->foo = 1;

        $Data = [
            $obj1,
            [
                $obj2,
                $obj3
            ]
        ];

        // test body and assertions
        $this->assertEquals(count(\Mezon\Functional::filter($Data, 'foo', '==', 1)), 2, 'Invalid filtration');
    }

    /**
     * This method is testing filtration function in a recursive mode
     */
    public function testGetFieldRecursive(): void
    {
        // setup
        $obj1 = new stdClass();
        $obj1->foo = 1;

        $obj2 = new stdClass();
        $obj2->bar = 2;
        $obj1->obj2 = $obj2;

        $obj3 = new stdClass();
        $obj3->eak = 3;
        $obj1->obj3 = $obj3;

        // test body and assertions
        $this->assertEquals(\Mezon\Functional::getField($obj1, 'eak'), 3, 'Invalid getting');
    }

    /**
     * Testing fields replacing in arrays
     */
    public function testReplaceFieldInArrays(): void
    {
        // setup
        $Records = [
            [
                'from' => 0
            ],
            [
                'from' => 1
            ]
        ];

        // test body
        \Mezon\Functional::replaceField($Records, 'from', 'to');

        // assertions
        $this->assertTrue(isset($Records[0]['to']), 'Field was not replaced');
        $this->assertTrue(isset($Records[1]['to']), 'Field was not replaced');

        $this->assertEquals($Records[0]['to'], 0, 'Field was not replaced correctly');
        $this->assertEquals($Records[1]['to'], 1, 'Field was not replaced correctly');
    }

    /**
     * Testing fields replacing in objects
     */
    public function testReplaceFieldInObjects(): void
    {
        // setup
        $Object0 = new stdClass();
        $Object0->from = 0;

        $Object1 = new stdClass();
        $Object1->from = 1;

        $Records = [
            $Object0,
            $Object1
        ];

        // test body
        \Mezon\Functional::replaceField($Records, 'from', 'to');

        // assertions
        $this->assertTrue(isset($Records[0]->to), 'Field was not replaced');
        $this->assertTrue(isset($Records[1]->to), 'Field was not replaced');

        $this->assertEquals(0, $Records[0]->to, 'Field was not replaced correctly');
        $this->assertEquals(1, $Records[1]->to, 'Field was not replaced correctly');
    }

    /**
     * Testing 'replaceFields' method
     */
    public function testReplaceFields(): void
    {
        // setup
        $Objects = [
            [
                'id' => 1,
                'field' => 'f'
            ]
        ];

        // test body
        \Mezon\Functional::replaceFields($Objects, [
            'id',
            'field'
        ], [
            '_id',
            '_field'
        ]);

        // assertions
        $this->assertEquals(1, $Objects[0]['_id']);
        $this->assertEquals('f', $Objects[0]['_field']);
    }

    /**
     * Testing 'replaceFieldInEntity' method
     */
    public function testReplaceFieldInEntity(): void
    {
        // setup
        $Object = [
            'id' => 1
        ];

        // test body
        \Mezon\Functional::replaceFieldInEntity($Object, 'id', 'id2');

        // assertions
        $this->assertArrayHasKey('id2', $Object);
        $this->assertEquals(1, $Object['id2']);
    }

    /**
     * Testing 'replaceFieldsInEntity' method
     */
    public function testReplaceFieldsInEntity(): void
    {
        // setup
        $Object = [
            'id' => 1,
            'field' => 'f'
        ];

        // test body
        \Mezon\Functional::replaceFieldsInEntity($Object, [
            'id',
            'field'
        ], [
            'id2',
            'field2'
        ]);

        // assertions
        $this->assertArrayHasKey('id2', $Object);
        $this->assertArrayHasKey('field2', $Object);
        $this->assertEquals(1, $Object['id2']);
        $this->assertEquals('f', $Object['field2']);
    }

    /**
     * Testing children setting
     */
    public function testSetChildren(): void
    {
        // setup
        $Objects = [
            [
                'id' => 1
            ],
            [
                'id' => 3
            ]
        ];
        $Records = [
            [
                'f' => 1
            ],
            [
                'f' => 1
            ],
            [
                'f' => 2
            ]
        ];

        // test body
        \Mezon\Functional::setChildren('children', $Objects, 'id', $Records, 'f');

        // assertions
        $this->assertTrue(isset($Objects[0]['children']), 'Field was not created correctly');
        $this->assertTrue(isset($Objects[1]['children']), 'Field was not created correctly');

        $this->assertEquals(2, count($Objects[0]['children']), 'Records were not joined');
        $this->assertEquals(0, count($Objects[1]['children']), 'Records were not joined');
    }

    /**
     * Method tests records expansion
     */
    public function testExpandingRecords(): void
    {
        // setup
        $Arr1 = [
            [
                'id' => 1
            ],
            [
                'id' => 2
            ]
        ];

        $Arr2 = [
            [
                'id' => 1,
                'f' => 11
            ],
            [
                'id' => 3,
                'f' => 22
            ]
        ];

        // test body
        \Mezon\Functional::expandRecordsWith($Arr1, 'id', $Arr2, 'id');

        // assertions
        $this->assertTrue(isset($Arr1[0]['f']), 'Field was not merged');
        $this->assertEquals(11, $Arr1[0]['f'], 'Field was not merged');

        $this->assertFalse(isset($Arr1[1]['f']), 'Field was merged, but it must not');
    }

    /**
     * Testing records sorting
     */
    function testRecordsSorting(): void
    {
        // setup
        $Arr = [
            [
                'i' => 2
            ],
            [
                'i' => 1
            ],
            [
                'i' => 3
            ]
        ];

        // test body
        \Mezon\Functional::sortRecords($Arr, 'i');

        // assertions
        $this->assertEquals(1, $Arr[0]['i']);
        $this->assertEquals(2, $Arr[1]['i']);
        $this->assertEquals(3, $Arr[2]['i']);
    }

    /**
     * Testing records sorting
     */
    function testRecordsSortingReverse(): void
    {
        // setup
        $Arr = [
            [
                'i' => 1
            ],
            [
                'i' => 1
            ],
            [
                'i' => 3
            ],
            [
                'i' => 2
            ]
        ];

        // test body
        \Mezon\Functional::sortRecords($Arr, 'i', \Mezon\Functional::SORT_DIRECTION_DESC);

        // assertions
        $this->assertEquals(3, $Arr[0]['i']);
        $this->assertEquals(2, $Arr[1]['i']);
        $this->assertEquals(1, $Arr[2]['i']);
        $this->assertEquals(1, $Arr[3]['i']);
    }

    /**
     * Method tests nested record's addition
     */
    function testSetChild(): void
    {
        // setup
        $Objects = [
            [
                'id' => 1
            ],
            [
                'id' => 3
            ]
        ];
        $Records = [
            [
                'f' => 1
            ],
            [
                'f' => 3
            ],
            [
                'f' => 2
            ]
        ];

        // test body
        \Mezon\Functional::setChild('nested', $Objects, 'id', $Records, 'f');

        // assertions
        $this->assertEquals(1, $Objects[0]['nested']['f'], 'Record was not nested');
        $this->assertEquals(3, $Objects[1]['nested']['f'], 'Record was not nested');
    }

    /**
     * Method checks does the field exists
     */
    function testFieldExistsPlain(): void
    {
        // setup
        $Arr = [
            'f1' => 1,
            'f2' => 2
        ];
        $Arr2 = new stdClass();
        $Arr2->f3 = 3;

        // test body and assertions
        $this->assertTrue(\Mezon\Functional::fieldExists($Arr, 'f1'));
        $this->assertTrue(\Mezon\Functional::fieldExists($Arr, 'f2'));
        $this->assertFalse(\Mezon\Functional::fieldExists($Arr, 'f3'));
        $this->assertTrue(\Mezon\Functional::fieldExists($Arr2, 'f3'));
        $this->assertFalse(\Mezon\Functional::fieldExists($Arr2, 'f4'));
    }

    /**
     * Method checks does the field recursive.
     */
    function testFieldExistsRecursive(): void
    {
        // setup
        $Arr = [
            'f1' => 1,
            'f2' => [
                'f21' => 21,
                'f22' => 22
            ],
            'f3' => 3
        ];

        // test body and assertions
        $this->assertTrue(\Mezon\Functional::fieldExists($Arr, 'f2'));
        $this->assertTrue(\Mezon\Functional::fieldExists($Arr, 'f22'));
        $this->assertFalse(\Mezon\Functional::fieldExists($Arr, 'f22', false));
    }
}
