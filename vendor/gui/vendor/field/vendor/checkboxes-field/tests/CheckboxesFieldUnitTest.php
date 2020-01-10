<?php
require_once (__DIR__ . '/../../../../../../../autoloader.php');

class CheckboxesFieldUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Method returns mock object of the custom field
     *
     * @return object mock object of the custom field
     */
    protected function getFieldMock(): object
    {
        $Mock = $this->getMockBuilder(\Mezon\Gui\Field\CheckboxesField::class)
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
                    'type' => 'int'
                ],
                ''
            ])
            ->setMethods([
            'getExternalRecords'
        ])
            ->getMock();

        $Mock->method('getExternalRecords')->willReturn([
            [
                'id' => 1
            ]
        ]);

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
        $this->assertStringContainsString('type="checkbox"', $Content, 'Name of the remote record was not found');
    }
}
