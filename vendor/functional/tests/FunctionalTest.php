<?php
require_once (__DIR__ . '/../functional.php');

/**
 * Transformation function multiplies 'foo' field.
 */
function transform2x($Object)
{
    $Object->foo *= 2;

    return ($Object);
}

class FunctionalTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing getting field function.
     */
    public function test_get_field_array(): void
    {
        // setup
        $Arr = [
            'foo' => 'bar'
        ];

        // test body
        $Result = Functional::get_field($Arr, 'foo');

        // assertions
        $this->assertEquals($Result, 'bar', 'Invalid value');
    }

    /**
     * Testing getting field function.
     */
    public function test_get_field_2_array(): void
    {
        // setup
        $Arr = [
            'foo' => 'bar',
            'foo2' => 'bar2'
        ];

        // test body
        $Result = Functional::get_field($Arr, 'foo2');

        // assertions
        $this->assertEquals($Result, 'bar2', 'Invalid value');
    }

    /**
     * Testing getting field function.
     */
    public function test_get_field_object(): void
    {
        // setup
        $obj = new stdClass();
        $obj->foo = 'bar';

        // test body
        $Result = Functional::get_field($obj, 'foo');

        // assertions
        $this->assertEquals($Result, 'bar', 'Invalid value');
    }

    /**
     * Testing getting field function.
     */
    public function test_get_field_2_object(): void
    {
        // setup
        $obj = new stdClass();
        $obj->foo = 'bar';
        $obj->foo2 = 'bar2';

        // test body
        $Result = Functional::get_field($obj, 'foo2');

        // assertions
        $this->assertEquals($Result, 'bar2', 'Invalid value');
    }

    /**
     * Testing fields fetching.
     */
    public function test_fields_fetching(): void
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
        $Result = Functional::get_fields($Data, 'foo');

        // assertions
        $this->assertEquals(count($Result), 3, 'Invalid count');

        $this->assertEquals($Result[0], 1, 'Invalid value');
        $this->assertEquals($Result[1], 2, 'Invalid value');
        $this->assertEquals($Result[2], 3, 'Invalid value');
    }

    /**
     * Testing fields setting.
     */
    public function test_fields_setting(): void
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
        Functional::set_fields_in_objects($Data, 'foo', $Values);

        // assertions
        $this->assertEquals(count($Data), 3, 'Invalid count');

        $this->assertEquals($Data[0]->foo, 1, 'Invalid value');
        $this->assertEquals($Data[1]->foo, 2, 'Invalid value');
        $this->assertEquals($Data[2]->foo, 3, 'Invalid value');
    }

    /**
     * Testing fields summation.
     */
    public function test_fields_sum(): void
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
        $this->assertEquals(Functional::sum_fields($Data, 'foo'), 6, 'Invalid sum');
    }

    /**
     * Method will test transformation function.
     */
    public function test_transform(): void
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
        Functional::transform($Data, 'transform2x');

        // assertions
        $this->assertEquals($Data[0]->foo, 2, 'Invalid value');
        $this->assertEquals($Data[1]->foo, 4, 'Invalid value');
        $this->assertEquals($Data[2]->foo, 6, 'Invalid value');
    }

    /**
     * Testing recursive fields summation.
     */
    public function test_recursive_sum(): void
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
        $this->assertEquals(Functional::sum_fields($Data, 'foo'), 6, 'Invalid sum');
    }

    /**
     * This method is testing filtration function.
     */
    public function test_filter_simple(): void
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
        $this->assertEquals(count(Functional::filter($Data, 'foo', '==', 1)), 2, 'Invalid filtration');
    }

    /**
     * This method is testing filtration function in a recursive mode.
     */
    public function test_filter_recursive(): void
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
        $this->assertEquals(count(Functional::filter($Data, 'foo', '==', 1)), 2, 'Invalid filtration');
    }

    /**
     * This method is testing filtration function in a recursive mode.
     */
    public function test_get_field_recursive(): void
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
        $this->assertEquals(Functional::get_field($obj1, 'eak'), 3, 'Invalid getting');
    }

    /**
     * Testing fields replacing in arrays.
     */
    public function test_replace_field_in_arrays(): void
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
        Functional::replace_field($Records, 'from', 'to');

        // assertions
        $this->assertTrue(isset($Records[0]['to']), 'Field was not replaced');
        $this->assertTrue(isset($Records[1]['to']), 'Field was not replaced');

        $this->assertEquals($Records[0]['to'], 0, 'Field was not replaced correctly');
        $this->assertEquals($Records[1]['to'], 1, 'Field was not replaced correctly');
    }

    /**
     * Testing fields replacing in objects.
     */
    public function test_replace_field_in_objects(): void
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
        Functional::replace_field($Records, 'from', 'to');

        // assertions
        $this->assertTrue(isset($Records[0]->to), 'Field was not replaced');
        $this->assertTrue(isset($Records[1]->to), 'Field was not replaced');

        $this->assertEquals(0, $Records[0]->to, 'Field was not replaced correctly');
        $this->assertEquals(1, $Records[1]->to, 'Field was not replaced correctly');
    }

    /**
     * Testing 'replace_fields' method
     */
    public function test_replace_fields(): void
    {
        // setup
        $Objects = [
            [
                'id' => 1,
                'field' => 'f'
            ]
        ];

        // test body
        Functional::replace_fields($Objects, [
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
     * Testing 'replace_field_in_entity' method
     */
    public function test_replace_field_in_entity(): void
    {
        // setup
        $Object = [
            'id' => 1
        ];

        // test body
        Functional::replace_field_in_entity($Object, 'id', 'id2');

        // assertions
        $this->assertArrayHasKey('id2', $Object);
        $this->assertEquals(1, $Object['id2']);
    }

    /**
     * Testing 'replace_fields_in_entity' method
     */
    public function test_replace_fields_in_entity(): void
    {
        // setup
        $Object = [
            'id' => 1,
            'field' => 'f'
        ];

        // test body
        Functional::replace_fields_in_entity($Object, [
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
     * Testing children setting.
     */
    public function test_set_children(): void
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
        Functional::set_children('children', $Objects, 'id', $Records, 'f');

        // assertions
        $this->assertTrue(isset($Objects[0]['children']), 'Field was not created correctly');
        $this->assertTrue(isset($Objects[1]['children']), 'Field was not created correctly');

        $this->assertEquals(2, count($Objects[0]['children']), 'Records were not joined');
        $this->assertEquals(0, count($Objects[1]['children']), 'Records were not joined');
    }

    /**
     * Method tests records expansion.
     */
    public function test_expanding_records(): void
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
        Functional::expand_records_with($Arr1, 'id', $Arr2, 'id');

        // assertions
        $this->assertTrue(isset($Arr1[0]['f']), 'Field was not merged');
        $this->assertEquals(11, $Arr1[0]['f'], 'Field was not merged');

        $this->assertFalse(isset($Arr1[1]['f']), 'Field was merged, but it must not');
    }

    /**
     * Testing records sorting.
     */
    function test_records_sorting(): void
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
        Functional::sort_records($Arr, 'i');

        // assertions
        $this->assertEquals(1, $Arr[0]['i'], 'Array was not sorted');
        $this->assertEquals(2, $Arr[1]['i'], 'Array was not sorted');
        $this->assertEquals(3, $Arr[2]['i'], 'Array was not sorted');
    }

    /**
     * Testing records sorting.
     */
    function test_records_sorting_desc(): void
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
        Functional::sort_records_desc($Arr, 'i');

        // assertions
        $this->assertEquals(3, $Arr[0]['i'], 'Array was not sorted');
        $this->assertEquals(2, $Arr[1]['i'], 'Array was not sorted');
        $this->assertEquals(1, $Arr[2]['i'], 'Array was not sorted');
    }

    /**
     * Method tests nested record's addition.
     */
    function test_set_child(): void
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
        Functional::set_child('nested', $Objects, 'id', $Records, 'f');

        // assertions
        $this->assertEquals(1, $Objects[0]['nested']['f'], 'Record was not nested');
        $this->assertEquals(3, $Objects[1]['nested']['f'], 'Record was not nested');
    }

    /**
     * Method checks does the field exists.
     */
    function test_field_exists_plain(): void
    {
        // setup
        $Arr = [
            'f1' => 1,
            'f2' => 2
        ];

        // test body and assertions
        $this->assertTrue(Functional::field_exists($Arr, 'f1'));
        $this->assertTrue(Functional::field_exists($Arr, 'f2'));
        $this->assertFalse(Functional::field_exists($Arr, 'f3'));
    }

    /**
     * Method checks does the field recursive.
     */
    function test_field_exists_recursive(): void
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
        $this->assertTrue(Functional::field_exists($Arr, 'f2'));
        $this->assertTrue(Functional::field_exists($Arr, 'f22'));
        $this->assertFalse(Functional::field_exists($Arr, 'f22', false));
    }
}

?>