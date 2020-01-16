<?php
require_once ('autoload.php');

class TextFieldUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructor()
    {
        // setup
        $Field = new \Mezon\Gui\Field\TextField([
            'text' => 'name'
        ]);

        // test body
        $Content = $Field->html();

        // assertions
        $this->assertEquals('name', $Content, 'Text was not fetched');
    }
}
