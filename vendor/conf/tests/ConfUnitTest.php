<?php
require_once (__DIR__ . '/../../../autoloader.php');

class ConfUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing setup of the existing key.
     * It's value must be overwritten.
     */
    public function testSetExistingKey()
    {
        $Value = \Mezon\Conf::getConfigValue([
            '@app-http-path'
        ]);

        $this->assertEquals(false, $Value, 'Invalid @app-http-path value');

        \Mezon\Conf::setConfigValue('@app-http-path', 'set-value');

        $Value = \Mezon\Conf::getConfigValue([
            '@app-http-path'
        ]);

        $this->assertEquals('set-value', $Value, 'Invalid @app-http-path value');
    }

    /**
     * Testing setup of the unexisting key.
     * It's value must be overwritten.
     */
    public function testSetUnexistingKey()
    {
        $Value = \Mezon\Conf::getConfigValue([
            'unexisting-key'
        ]);

        $this->assertEquals(false, $Value, 'Invalid unexisting-key processing');

        \Mezon\Conf::setConfigValue('unexisting-key', 'set-value');

        $Value = \Mezon\Conf::getConfigValue([
            'unexisting-key'
        ]);

        $this->assertEquals('set-value', $Value, 'Invalid unexisting-key value');
    }

    /**
     * Testing setup of the unexisting key with complex route.
     * It's value must be overwritten.
     */
    public function testSetComplexUnexistingKey()
    {
        $Value = \Mezon\Conf::getConfigValue([
            'res',
            'images',
            'unexisting-key'
        ]);

        $this->assertEquals(false, $Value, 'Invalid res/images/unexisting-key processing');

        $Value = \Mezon\Conf::getConfigValue([
            'res',
            'images',
            'unexisting-key'
        ], 'default');

        $this->assertEquals('default', $Value, 'Invalid res/images/unexisting-key processing');

        \Mezon\Conf::setConfigValue('res/images/unexisting-key', 'set-value');

        $Value = \Mezon\Conf::getConfigValue([
            'res',
            'images',
            'unexisting-key'
        ]);

        $this->assertEquals('set-value', $Value, 'Invalid res/images/unexisting-key value');
    }

    /**
     * Testing setup of the existing array.
     */
    public function testAddComplexExistingArray()
    {
        $Value = \Mezon\Conf::getConfigValue([
            'res',
            'css'
        ]);

        $this->assertStringContainsString('', $Value, 'Invalid css files list');

        \Mezon\Conf::addConfigValue('res/css', 'set-value');

        $Value = \Mezon\Conf::getConfigValue([
            'res',
            'css'
        ]);

        $this->assertContains('set-value', $Value, 'Invalid css files list');
    }

    /**
     * Testing setup of the unexisting array.
     */
    public function testAddComplexUnexistingArray()
    {
        \Mezon\Conf::deleteConfigValue([
            'unexisting-key'
        ]);

        $Value = \Mezon\Conf::getConfigValue([
            'unexisting-key'
        ]);

        $this->assertEquals(false, $Value, 'Invalid unexisting-key processing');

        \Mezon\Conf::addConfigValue('unexisting-key', 'set-value');

        $Value = \Mezon\Conf::getConfigValue([
            'unexisting-key'
        ]);

        $this->assertContains('set-value', $Value, 'Invalid unexisting-key value');
    }

    /**
     * Testing setup of the unexisting array with simple route.
     */
    public function testAddUnexistingArray()
    {
        \Mezon\Conf::deleteConfigValue([
            'unexisting-key'
        ]);

        $Value = \Mezon\Conf::getConfigValue([
            'unexisting-key'
        ]);

        $this->assertEquals(false, $Value, 'Invalid unexisting-key processing');

        \Mezon\Conf::addConfigValue('unexisting-key', 'set-value');

        $Value = \Mezon\Conf::getConfigValue([
            'unexisting-key'
        ]);

        $this->assertContains('set-value', $Value, 'Invalid unexisting-key value');
    }

    /**
     * Testing setup of the existing array with simple route.
     */
    public function testAddExistingArray()
    {
        \Mezon\Conf::addConfigValue('unexisting-key', 'set-value-1');
        \Mezon\Conf::addConfigValue('unexisting-key', 'set-value-2');

        $Value = \Mezon\Conf::getConfigValue([
            'unexisting-key'
        ]);

        $this->assertContains('set-value-2', $Value, 'Invalid unexisting-key value');
    }

    /**
     * Testing setup of the existing array with simple route.
     */
    public function testComplexStringRoutes()
    {
        \Mezon\Conf::setConfigValue('f1/f2/unexisting-key', 'set-value-1');

        $Value = \Mezon\Conf::getConfigValue('f1/f2/unexisting-key');

        $this->assertEquals('set-value-1', $Value, 'Invalid unexisting-key value');
    }

    /**
     * Deleting simple key.
     */
    public function testDeleteFirstValue()
    {
        \Mezon\Conf::setConfigValue('key-1', 'value');

        $Value = \Mezon\Conf::getConfigValue('key-1');

        $this->assertEquals('value', $Value, 'Invalid setting value');

        \Mezon\Conf::deleteConfigValue('key-1');

        $Value = \Mezon\Conf::getConfigValue('key-1', false);

        $this->assertEquals(false, $Value, 'Key was not deleted');
    }

    /**
     * Deleting deep key.
     */
    public function testDeleteNextValue()
    {
        \Mezon\Conf::setConfigValue('key-2/key-3', 'value');

        $Value = \Mezon\Conf::getConfigValue('key-2/key-3');

        $this->assertEquals('value', $Value, 'Invalid setting value');

        \Mezon\Conf::deleteConfigValue('key-2/key-3');

        $Value = \Mezon\Conf::getConfigValue('key-2/key-3', false);

        $this->assertEquals(false, $Value, 'Key was not deleted');
    }

    /**
     * Deleting empty keys.
     */
    public function testDeleteEmptyKeys()
    {
        \Mezon\Conf::setConfigValue('key-4/key-5', 'value');

        \Mezon\Conf::deleteConfigValue('key-4/key-5');

        $Value = \Mezon\Conf::getConfigValue('key-4', false);

        $this->assertEquals(false, $Value, 'Key was not deleted');
    }

    /**
     * No deleting not empty keys.
     */
    public function testNoDeleteNotEmptyKeys()
    {
        \Mezon\Conf::setConfigValue('key-6/key-7', 'value');
        \Mezon\Conf::setConfigValue('key-6/key-8', 'value');

        \Mezon\Conf::deleteConfigValue('key-6/key-7');

        $Value = \Mezon\Conf::getConfigValue('key-6', false);

        $this->assertEquals(true, is_array($Value), 'Key was deleted');

        $Value = \Mezon\Conf::getConfigValue('key-6/key-8', false);

        $this->assertEquals('value', $Value, 'Key was deleted');
    }

    /**
     * Testing delete results.
     */
    public function testDeleteResult()
    {
        \Mezon\Conf::setConfigValue('key-9/key-10', 'value');

        // deleting unexisting value
        $Result = \Mezon\Conf::deleteConfigValue('key-9/key-unexisting');

        $this->assertEquals(false, $Result, 'Invalid deleting result');

        // deleting existing value
        $Result = \Mezon\Conf::deleteConfigValue('key-9/key-10');

        $this->assertEquals(true, $Result, 'Invalid deleting result');
    }

    /**
     * Testing fas BD setup.
     */
    public function testFastDbSetup()
    {
        \Mezon\Conf::addConnectionToConfig('connection', 'dsn', 'user', 'password');

        $Value = \Mezon\Conf::getConfigValue('connection/dsn', false);
        $this->assertEquals('dsn', $Value, 'Key connection/dsn was not found');

        $Value = \Mezon\Conf::getConfigValue('connection/user', false);
        $this->assertEquals('user', $Value, 'Key connection/user was not found');

        $Value = \Mezon\Conf::getConfigValue('connection/password', false);
        $this->assertEquals('password', $Value, 'Key connection/password was not found');
    }
}
