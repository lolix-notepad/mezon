<?php
require_once ('autoload.php');

class CacheFoo extends \Mezon\Cache\Cache
{

    public function init_hack()
    {
        parent::init();
    }
}

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
        $Cache = CacheFoo::getInstance();

        $Cache->set('key', 'test');

        $this->assertTrue($Cache->exists('key'), 'Existence check failed');
        $this->assertFalse($Cache->exists('unexisting'), 'Existence check failed');
    }
}
