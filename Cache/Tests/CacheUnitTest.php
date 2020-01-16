<?php
require_once ('autoload.php');

class CacheUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing that data can be added to cache.
     */
    public function testAdditingDataToCache()
    {
        $Cache = $this->getMockBuilder(\Mezon\Cache\Cache::class)
            ->setMethods([
            'flush'
        ])
            ->disableOriginalClone()
            ->getMock();

        $Cache->set('key', 'test');

        $Cache->flush();

        $Result = $Cache->get('key');

        $Cache->destroy();

        $this->assertEquals('test', $Result, 'Cache is not working');
    }

    /**
     * Method checks exists() method.
     */
    public function testExistence()
    {
        $Cache = \Mezon\Cache\Cache::getInstance();

        $Cache->set('key', 'test');

        $this->assertTrue($Cache->exists('key'), 'Existence check failed');
        $this->assertFalse($Cache->exists('unexisting'), 'Existence check failed');
    }

    /**
     * Method checks exists() method.
     */
    public function testExistenceObject(): void
    {
        // setup
        $Cache = $this->getMockBuilder(\Mezon\Cache\Cache::class)
            ->setMethods([
            'fileGetContents'
        ])
            ->disableOriginalClone()
            ->getMock();

        $Cache->method('fileGetContents')->willReturn('{"key":1}');

        // test body and assertions
        $this->assertTrue($Cache->exists('key'), 'Existence check failed');
    }

    /**
     * Testing get method
     */
    public function testGetUnexisting(): void
    {
        // setup
        $Cache = \Mezon\Cache\Cache::getInstance();

        // assertions
        $this->expectException(\Exception::class);

        // test body
        $Cache->get('unexisting');
    }

    /**
     * Testing 'flush' method
     */
    public function testFlush(): void
    {
        // setup
        $Cache = $this->getMockBuilder(\Mezon\Cache\Cache::class)
            ->setMethods([
            'filePutContents',
            'fileGetContents'
        ])
            ->disableOriginalClone()
            ->getMock();

        // assertions
        $Cache->expects($this->once())
            ->method('filePutContents');
        $Cache->method('fileGetContents')->willReturn('{"key":1}');

        // test body
        $Cache->set('key', 'value');
        $Cache->flush();
    }
}
