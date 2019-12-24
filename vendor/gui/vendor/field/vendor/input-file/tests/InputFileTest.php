<?php
require_once (__DIR__ . '/../input-file.php');

class InputFileTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructor()
    {
        // setup
        $Field = new \Mezon\GUI\Field\InputFile([
            'name' => 'name',
            'required' => 1,
            'disabled' => 1,
            'name-prefix' => 'prefix',
            'batch' => 1,
            'toggler' => 'toggler-name',
            'toggle-value' => 3,
            'type' => 'file'
        ], '');

        // test body
        $Content = $Field->html();

        // assertions
        $this->assertContains('<input ', $Content, 'Open tag was not found');
        $this->assertContains('type="file"', $Content, '"Name" attribute was not found');
        $this->assertContains('name="prefix-name[{_creation_form_items_counter}]"', $Content, '"Name" attribute was not found');
        $this->assertContains('required="required"', $Content, '"Required" attribute was not found');
        $this->assertContains('disabled', $Content, '"Disabled" attribute was not found');
        $this->assertContains('toggler="toggler-name"', $Content, '"Toggler" attribute was not found');
        $this->assertContains('toggle-value="3"', $Content, '"Toggle-value" attribute was not found');
    }
}

?>