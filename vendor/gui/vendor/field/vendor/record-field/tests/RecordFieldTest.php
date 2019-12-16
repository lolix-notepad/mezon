<?php
require_once (__DIR__ . '/../record-field.php');

class RecordFieldTest extends PHPUnit\Framework\TestCase
{

    /**
     * Method returns mock object of the custom field
     *
     * @return object mock object of the custom field
     */
    protected function get_field_mock(): object
    {
        $Mock = $this->getMockBuilder('RecordField')
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
                'type' => 'remote',
                'layout' => [
                    'rows' => [
                        [
                            "remote" => [
                                "width" => 10
                            ]
                        ]
                    ]
                ]
            ],
            ''
        ])
            ->setMethods([
            'get_fields'
        ])
            ->getMock();

        $Mock->method('get_fields')->willReturn([
            'id' => [
                'type' => 'integer'
            ],
            'remote' => [
                'type' => 'string',
                'title' => 'remote-title'
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
        $this->assertContains('name="prefix-remote"', $Content, 'Name of the remote record was not found');
    }
}

?>