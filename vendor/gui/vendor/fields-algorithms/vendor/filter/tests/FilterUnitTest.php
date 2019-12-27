<?php

class FilterUnitTest extends PHPUnit\Framework\TestCase
{
    /**
     * Testing addFilterConditionFromArr method
     */
    function testAddFilterConditionFromArr()
    {
        // setup and test body
        $Result = \Mezon\GUI\FieldsAlgorithms\Filter::addFilterConditionFromArr([
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
     * Testing addFilterConditionFromArr method
     */
    function testAddFilterConditionFromArr_simple()
    {
        // setup and test body
        $Result = \Mezon\GUI\FieldsAlgorithms\Filter::addFilterConditionFromArr([
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