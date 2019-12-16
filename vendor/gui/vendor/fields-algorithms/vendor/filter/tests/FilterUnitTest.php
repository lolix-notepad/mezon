<?php
require_once (__DIR__ . '/../filter.php');
require_once (__DIR__ . '/../../../../../../functional/functional.php');

class FilterUnitTest extends PHPUnit\Framework\TestCase
{
    /**
     * Testing add_filter_condition_from_arr method
     */
    function test_add_filter_condition_from_arr()
    {
        // setup and test body
        $Result = Filter::add_filter_condition_from_arr([
            [
                'arg1' => '$id',
                'op' => '>',
                'arg2' => '1'
            ]
        ], []);

        // asssertions
        $this->assertContains('id > 1', $Result, 'Compilation error');
    }

    /**
     * Testing add_filter_condition_from_arr method
     */
    function test_add_filter_condition_from_arr_simple()
    {
        // setup and test body
        $Result = Filter::add_filter_condition_from_arr([
            'field1' => 1 ,
            'field2'=> 'null',
            'field3'=>'not null',
            'field4'=>'some string'
        ], []);

        // asssertions
        $this->assertContains('field1 = 1', $Result, 'Integer compilation error');
        $this->assertContains('field2 IS NULL', $Result, 'Null compilation error');
        $this->assertContains('field3 IS NOT NULL', $Result, 'Not null compilation error');
        $this->assertContains('field4 LIKE "some string"', $Result, 'String compilation error');
    }
}

?>