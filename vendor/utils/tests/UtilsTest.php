<?php
require_once (__DIR__ . '/../utils.php');

class UtilsTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing bot detection
     */
    public function test_bot_success()
    {
        // test body
        $Result = Utils::is_bot('YandexCalendar');

        // assertions
        $this->assertTrue($Result, 'Invalid result');
    }

    /**
     * Testing bot detection
     */
    public function test_bot_failed()
    {
        // test body
        $Result = Utils::is_bot('Unexisting Bot');

        // assertions
        $this->assertFalse($Result, 'Invalid result');
    }
}

?>