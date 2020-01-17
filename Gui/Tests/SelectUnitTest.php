<?php

class SelectUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructor()
    {
        // setup
        $field = new \Mezon\Gui\Field\Select(
            [
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
            ],
            '');

        // test body
        $content = $field->html();

        // assertions
        $this->assertStringContainsString('<select ', $content, 'Open tag was not found');
        $this->assertStringContainsString(
            'name="prefix-name[{_creation_form_items_counter}]"',
            $content,
            '"Name" attribute was not found');
            $this->assertStringContainsString('required="required"', $content, '"Required" attribute was not found');
            $this->assertStringContainsString('disabled', $content, '"Disabled" attribute was not found');
            $this->assertStringContainsString('toggler="toggler-name"', $content, '"toggler" attribute was not found');
            $this->assertStringContainsString('toggle-value="3"', $content, '"Toggle-value" attribute was not found');
    }
}
