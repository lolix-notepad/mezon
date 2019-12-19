<?php
require_once (__DIR__ . '/../checkboxes-field.php');

class CheckboxesFieldTest extends PHPUnit\Framework\TestCase
{

    /**
     * Method returns mock object of the custom field
     *
     * @return object mock object of the custom field
     */
    protected function get_field_mock(): object
    {
        $Mock = $this->getMockBuilder('\Mezon\GUI\Field\CheckboxesField')
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
                'bind-field' => 'id',
                'session-id' => 'sid',
                'remote-source' => 'http://ya.ru',
                'type' => 'int'
            ],
            ''
        ])
            ->setMethods([
            'get_external_records'
        ])
            ->getMock();

        $Mock->method('get_external_records')->willReturn([
            [
                'id' => 1
            ]
        ]);

        return ($Mock);
    }

    /**
     * Testing constructor
     */
    public function test_constructor()
    {
        // setup
        $Field = $this->get_field_mock();

        // test body
        $Content = $Field->html();

        // assertions
        $this->assertContains('type="checkbox"', $Content, 'Name of the remote record was not found');
    }
}

?>