<?php
require_once ('autoload.php');

class RecordFieldUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Method returns mock object of the custom field
     *
     * @return object mock object of the custom field
     */
    protected function getFieldMock(): object
    {
        $Mock = $this->getMockBuilder(\Mezon\Gui\Field\RecordField::class)
            ->setConstructorArgs(
            [
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
            'getFields'
        ])
            ->getMock();

        $Mock->method('getFields')->willReturn(
            [
                'id' => [
                    'type' => 'integer'
                ],
                'remote' => [
                    'type' => 'string',
                    'title' => 'remote-title'
                ]
            ]);

        return $Mock;
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
        $this->assertStringContainsString('name="prefix-remote"', $Content, 'Name of the remote record was not found');
    }
}
