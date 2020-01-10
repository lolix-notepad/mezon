<?php
require_once (__DIR__ . '/../../../../../../../autoloader.php');

class TextareaUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructor()
    {
        // setup
        $Field = new \Mezon\Gui\Field\Textarea(
            [
                'name' => 'name',
                'required' => 1,
                'disabled' => 1,
                'name-prefix' => 'prefix',
                'batch' => 1,
                'toggler' => 'toggler-name',
                'toggle-value' => 3,
                'type' => 'string'
            ],
            '');

        // test body
        $Content = $Field->html();

        // assertions
        $this->assertStringContainsString('<textarea ', $Content, 'Open tag was not found');
        $this->assertStringContainsString(
            'name="prefix-name[{_creation_form_items_counter}]"',
            $Content,
            '"Name" attribute was not found');
            $this->assertStringContainsString('required="required"', $Content, '"Required" attribute was not found');
            $this->assertStringContainsString('disabled', $Content, '"Disabled" attribute was not found');
            $this->assertStringContainsString('toggler="toggler-name"', $Content, '"Toggler" attribute was not found');
            $this->assertStringContainsString('toggle-value="3"', $Content, '"Toggle-value" attribute was not found');
    }
}
