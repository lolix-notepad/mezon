<?php

class FormHeaderUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructor()
    {
        // setup
        $Field = new \Mezon\Gui\Field\FormHeader([
            'text' => 'name'
        ]);

        // test body
        $Content = $Field->html();

        // assertions
        $this->assertStringContainsString('<h3>name</h3>', $Content, 'Header was not built');
    }
}
