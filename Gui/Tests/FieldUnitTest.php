<?php

class FieldUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testNoNameException()
    {
        $this->expectException(Exception::class);
        new \Mezon\Gui\Field([], '');
    }

    /**
     * Testing setters
     */
    public function testNameSetter()
    {
        // test body
        $Field = new \Mezon\Gui\Field(json_decode(file_get_contents(__DIR__ . '/conf/name-setter.json'), true), '');

        // assertions
        $this->assertStringContainsString('prefixfield-name000', $Field->html(), 'Invalid field "name" value');
    }

    /**
     * Testing setters
     */
    public function testRequiredSetter()
    {
        // test body
        $Field = new \Mezon\Gui\Field(json_decode(file_get_contents(__DIR__ . '/conf/required-setter.json'), true), '');

        // assertions
        $this->assertStringContainsString('prefixfield-name1111select2', $Field->html(), 'Invalid field "name" value');
    }
}
