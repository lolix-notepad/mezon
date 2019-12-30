<?php
require_once (__DIR__ . '/../../../autoloader.php');

class CacheFoo extends \Mezon\Cache
{

    public function init_hack()
    {
        parent::init();
    }
}

class CacheTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing that data can be added to cache.
     */
    public function testAdditingDataToCache()
    {
        $Cache = CacheFoo::getInstance();

        $Cache->set('key', 'test');

        $Cache->flush();

        $Cache->destroy();

        $Cache = CacheFoo::getInstance();

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

?>