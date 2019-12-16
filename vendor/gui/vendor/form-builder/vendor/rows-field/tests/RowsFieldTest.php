<?php
require_once (__DIR__ . '/../rows-field.php');

class RowsFieldTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function test_constructor()
    {
        // setup
        $Field = new RowsField([
            'text' => 'name'
        ], 'author');

        // test body
        $Content = $Field->html();

        // assertions
        $this->assertContains('add_element_by_template', $Content, 'Necessary JavaScripts were not found');
    }
}

?>