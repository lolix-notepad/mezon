<?php
require_once ('autoload.php');

class SecurityUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing edge cases of getFileValue
     */
    public function testGetFileValue(): void
    {
        // setup
        $_FILES = [
            'test-file' => [
                'size' => 0
            ]
        ];

        // test body
        $Result = \Mezon\Security\Security::getFileValue('test-file', false);

        // assertions
        $this->assertEquals('', $Result);
    }
}
