<?php

class CustomFieldTest extends PHPUnit\Framework\TestCase
{

    /**
     * Method returns mock object of the custom field
     *
     * @return object mock object of the custom field
     */
    protected function getFieldMock(): object
    {
        $Mock = $this->getMockBuilder('\Mezon\GUI\Field\CustomField')
            ->setConstructorArgs([
            [
                'name' => 'name',
                'required' => 1,
                'disabled' => 1,
                'custom' => 1,
                'name-prefix' => 'prefix',
                'batch' => 1,
                'toggler' => 'toggler-name',
                'toggle-value' => 3,
                'type' => 'integer',
                'fields' => []
            ],
            ''
        ])
            ->setMethods([
            'get_field_template'
        ])
            ->getMock();

        $Mock->method('get_field_template')->willReturn('name:{name} required:{required} disabled:{disabled} custom:{custom} name-prefix:{name-prefix} batch:{batch} toggler:{toggler} toggler:{toggle-value}');

        return ($Mock);
    }

    /**
     * Testing constructor
     */
    public function testConstructor()
    {
        // setup
        $Field = $this->getFieldMock();

        // test body
        $Content = $Field->html();

        // assertions
        $this->assertContains('name:name', $Content, 'Name was not substitute');
        $this->assertContains('required:1', $Content, 'Required was not substitute');
        $this->assertContains('disabled:1', $Content, 'Disabled was not substitute');
        $this->assertContains('custom:1', $Content, 'Custom was not substitute');
        $this->assertContains('name-prefix:prefix', $Content, 'Name prefix was not substitute');
        $this->assertContains('batch:1', $Content, 'Batch was not substitute');
        $this->assertContains('toggler:toggler-name', $Content, 'Toggler name was not substitute');
        $this->assertContains('toggler:3', $Content, 'Toggler value was not substitute');
    }
}

?>