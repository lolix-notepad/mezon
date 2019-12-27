<?php

class TextFieldTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructor()
    {
        // setup
        $Field = new \Mezon\GUI\Field\TextField([
            'text' => 'name'
        ]);

        // test body
        $Content = $Field->html();

        // assertions
        $this->assertEquals('name', $Content, 'Text was not fetched');
    }
}

?>