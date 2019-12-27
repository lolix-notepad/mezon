<?php

class ConfTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing setup of the existing key.
     * It's value must be overwritten.
     */
    public function testSetExistingKey()
    {
        $Value = \Mezon\getConfigValue([
            '@app-http-path'
        ]);

        $this->assertEquals(false, $Value, 'Invalid @app-http-path value');

        \Mezon\setConfigValue('@app-http-path', 'set-value');

        $Value = \Mezon\getConfigValue([
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
        $Value = \Mezon\getConfigValue([
            'unexisting-key'
        ]);

        $this->assertEquals(false, $Value, 'Invalid unexisting-key processing');

        \Mezon\setConfigValue('unexisting-key', 'set-value');

        $Value = \Mezon\getConfigValue([
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
        $Value = \Mezon\getConfigValue([
            'res',
            'images',
            'unexisting-key'
        ]);

        $this->assertEquals(false, $Value, 'Invalid res/images/unexisting-key processing');
        
        $Value = \Mezon\getConfigValue([
        	'res',
        	'images',
        	'unexisting-key'
        ], 'default');
        
        $this->assertEquals('default', $Value, 'Invalid res/images/unexisting-key processing');

        \Mezon\setConfigValue('res/images/unexisting-key', 'set-value');

        $Value = \Mezon\getConfigValue([
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
        $Value = \Mezon\getConfigValue([
            'res',
            'css'
        ]);

        $this->assertStringContainsString('', $Value, 'Invalid css files list');

        \Mezon\addConfigValue('res/css', 'set-value');

        $Value = \Mezon\getConfigValue([
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
        \Mezon\deleteConfigValue([
            'unexisting-key'
        ]);

        $Value = \Mezon\getConfigValue([
            'unexisting-key'
        ]);

        $this->assertEquals(false, $Value, 'Invalid unexisting-key processing');

        \Mezon\addConfigValue('unexisting-key', 'set-value');

        $Value = \Mezon\getConfigValue([
            'unexisting-key'
        ]);

        $this->assertContains('set-value', $Value, 'Invalid unexisting-key value');
    }

    /**
     * Testing setup of the unexisting array with simple route.
     */
    public function testAddUnexistingArray()
    {
        \Mezon\deleteConfigValue([
            'unexisting-key'
        ]);

        $Value = \Mezon\getConfigValue([
            'unexisting-key'
        ]);

        $this->assertEquals(false, $Value, 'Invalid unexisting-key processing');

        \Mezon\addConfigValue('unexisting-key', 'set-value');

        $Value = \Mezon\getConfigValue([
            'unexisting-key'
        ]);

        $this->assertContains('set-value', $Value, 'Invalid unexisting-key value');
    }

    /**
     * Testing setup of the existing array with simple route.
     */
    public function testAddExistingArray()
    {
        \Mezon\addConfigValue('unexisting-key', 'set-value-1');
        \Mezon\addConfigValue('unexisting-key', 'set-value-2');

        $Value = \Mezon\getConfigValue([
            'unexisting-key'
        ]);

        $this->assertContains('set-value-2', $Value, 'Invalid unexisting-key value');
    }

    /**
     * Testing setup of the existing array with simple route.
     */
    public function testComplexStringRoutes()
    {
        \Mezon\setConfigValue('f1/f2/unexisting-key', 'set-value-1');

        $Value = \Mezon\getConfigValue('f1/f2/unexisting-key');

        $this->assertEquals('set-value-1', $Value, 'Invalid unexisting-key value');
    }

    /**
     * Deleting simple key.
     */
    public function testDeleteFirstValue()
    {
        \Mezon\setConfigValue('key-1', 'value');

        $Value = \Mezon\getConfigValue('key-1');

        $this->assertEquals('value', $Value, 'Invalid setting value');

        \Mezon\deleteConfigValue('key-1');

        $Value = \Mezon\getConfigValue('key-1', false);

        $this->assertEquals(false, $Value, 'Key was not deleted');
    }

    /**
     * Deleting deep key.
     */
    public function testDeleteNextValue()
    {
        \Mezon\setConfigValue('key-2/key-3', 'value');

        $Value = \Mezon\getConfigValue('key-2/key-3');

        $this->assertEquals('value', $Value, 'Invalid setting value');

        \Mezon\deleteConfigValue('key-2/key-3');

        $Value = \Mezon\getConfigValue('key-2/key-3', false);

        $this->assertEquals(false, $Value, 'Key was not deleted');
    }

    /**
     * Deleting empty keys.
     */
    public function testDeleteEmptyKeys()
    {
        \Mezon\setConfigValue('key-4/key-5', 'value');

        \Mezon\deleteConfigValue('key-4/key-5');

        $Value = \Mezon\getConfigValue('key-4', false);

        $this->assertEquals(false, $Value, 'Key was not deleted');
    }

    /**
     * No deleting not empty keys.
     */
    public function testNoDeleteNotEmptyKeys()
    {
        \Mezon\setConfigValue('key-6/key-7', 'value');
        \Mezon\setConfigValue('key-6/key-8', 'value');

        \Mezon\deleteConfigValue('key-6/key-7');

        $Value = \Mezon\getConfigValue('key-6', false);

        $this->assertEquals(true, is_array($Value), 'Key was deleted');

        $Value = \Mezon\getConfigValue('key-6/key-8', false);

        $this->assertEquals('value', $Value, 'Key was deleted');
    }

    /**
     * Testing delete results.
     */
    public function testDeleteResult()
    {
        \Mezon\setConfigValue('key-9/key-10', 'value');

        // deleting unexisting value
        $Result = \Mezon\deleteConfigValue('key-9/key-unexisting');

        $this->assertEquals(false, $Result, 'Invalid deleting result');

        // deleting existing value
        $Result = \Mezon\deleteConfigValue('key-9/key-10');

        $this->assertEquals(true, $Result, 'Invalid deleting result');
    }

    /**
     * Testing fas BD setup.
     */
    public function testFastDbSetup()
    {
        \Mezon\addConnectionToConfig('connection', 'dsn', 'user', 'password');

        $Value = \Mezon\getConfigValue('connection/dsn', false);
        $this->assertEquals('dsn', $Value, 'Key connection/dsn was not found');

        $Value = \Mezon\getConfigValue('connection/user', false);
        $this->assertEquals('user', $Value, 'Key connection/user was not found');

        $Value = \Mezon\getConfigValue('connection/password', false);
        $this->assertEquals('password', $Value, 'Key connection/password was not found');
    }
}

?>