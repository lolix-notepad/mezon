<?php
require_once (__DIR__ . '/../../../../../../../autoloader.php');

class InputDateTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructor()
    {
        // setup
        $Field = new \Mezon\Gui\Field\InputDate([
            'name' => 'name',
            'required' => 1,
            'disabled' => 1,
            'name-prefix' => 'prefix',
            'batch' => 1,
            'toggler' => 'toggler-name',
            'toggle-value' => 3,
            'type' => 'date'
        ], '');

        // test body
        $Content = $Field->html();

        // assertions
        $this->assertContains('<input ', $Content, 'Open tag was not found');
        $this->assertContains('type="text"', $Content, '"Name" attribute was not found');
        $this->assertContains('date-input', $Content, 'Date class was not found');
        $this->assertContains('name="prefix-name[{_creation_form_items_counter}]"', $Content, '"Name" attribute was not found');
        $this->assertContains('required="required"', $Content, '"Required" attribute was not found');
        $this->assertContains('disabled', $Content, '"Disabled" attribute was not found');
        $this->assertContains('toggler="toggler-name"', $Content, '"Toggler" attribute was not found');
        $this->assertContains('toggle-value="3"', $Content, '"Toggle-value" attribute was not found');
    }
}

?>