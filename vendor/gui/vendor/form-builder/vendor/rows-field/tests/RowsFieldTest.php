<?php
require_once (__DIR__ . '/../../../../../../../autoloader.php');

class RowsFieldTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructor()
    {
        // setup
        $Field = new \Mezon\Gui\FormBuilder\RowsField([
            'text' => 'name'
        ], 'author');

        // test body
        $Content = $Field->html();

        // assertions
        $this->assertContains('add_element_by_template', $Content, 'Necessary JavaScripts were not found');
    }
}

?>