<?php
require_once (__DIR__ . '/../select.php');

class SelectTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function test_constructor()
    {
        // setup
        $Field = new \Mezon\GUI\Field\Select([
            'name' => 'name',
            'required' => 1,
            'disabled' => 1,
            'name-prefix' => 'prefix',
            'batch' => 1,
            'toggler' => 'toggler-name',
            'toggle-value' => 3,
            'items' => [
                '1' => '111',
                '2' => '222'
            ],
            'type' => 'integer'
        ], '');

        // test body
        $Content = $Field->html();

        // assertions
        $this->assertContains('<select ', $Content, 'Open tag was not found');
        $this->assertContains('name="prefix-name[{_creation_form_items_counter}]"', $Content, '"Name" attribute was not found');
        $this->assertContains('required="required"', $Content, '"Required" attribute was not found');
        $this->assertContains('disabled', $Content, '"Disabled" attribute was not found');
        $this->assertContains('toggler="toggler-name"', $Content, '"Toggler" attribute was not found');
        $this->assertContains('toggle-value="3"', $Content, '"Toggle-value" attribute was not found');
    }
}

?>