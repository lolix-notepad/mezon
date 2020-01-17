<?php

class RowsFieldUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructor()
    {
        // setup
        $field = new \Mezon\Gui\FormBuilder\RowsField([
            'text' => 'name',
        ], 'author');

        // test body
        $content = $field->html();

        // assertions
        $this->assertStringContainsString('add_element_by_template', $content, 'Necessary JavaScripts were not found');
    }
}
