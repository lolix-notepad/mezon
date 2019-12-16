<?php
require_once (__DIR__ . '/../conf.php');

class ConfTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing setup of the existing key.
     * It's value must be overwritten.
     */
    public function test_set_existing_key()
    {
        $Value = get_config_value(array(
            '@app-http-path'
        ));

        $this->assertEquals('http:///', $Value, 'Invalid @app-http-path value');

        set_config_value('@app-http-path', 'set-value');

        $Value = get_config_value(array(
            '@app-http-path'
        ));

        $this->assertEquals('set-value', $Value, 'Invalid @app-http-path value');
    }

    /**
     * Testing setup of the unexisting key.
     * It's value must be overwritten.
     */
    public function test_set_unexisting_key()
    {
        $Value = get_config_value(array(
            'unexisting-key'
        ));

        $this->assertEquals(false, $Value, 'Invalid unexisting-key processing');

        set_config_value('unexisting-key', 'set-value');

        $Value = get_config_value(array(
            'unexisting-key'
        ));

        $this->assertEquals('set-value', $Value, 'Invalid unexisting-key value');
    }

    /**
     * Testing setup of the existing key with complex route.
     * It's value must be overwritten.
     */
    public function test_set_complex_existing_key()
    {
        $Value = get_config_value(array(
            'res',
            'images',
            'favicon'
        ));

        $this->assertEquals('http:////res/images/favicon.ico', $Value, 'Invalid unexisting-key processing');

        set_config_value('res/images/favicon', 'set-value');

        $Value = get_config_value(array(
            'res',
            'images',
            'favicon'
        ));

        $this->assertEquals('set-value', $Value, 'Invalid res/images/favicon value');
    }

    /**
     * Testing setup of the unexisting key with complex route.
     * It's value must be overwritten.
     */
    public function test_set_complex_unexisting_key()
    {
        $Value = get_config_value([
            'res',
            'images',
            'unexisting-key'
        ]);

        $this->assertEquals(false, $Value, 'Invalid res/images/unexisting-key processing');
        
        $Value = get_config_value([
        	'res',
        	'images',
        	'unexisting-key'
        ], 'default');
        
        $this->assertEquals('default', $Value, 'Invalid res/images/unexisting-key processing');

        set_config_value('res/images/unexisting-key', 'set-value');

        $Value = get_config_value([
            'res',
            'images',
            'unexisting-key'
        ]);

        $this->assertEquals('set-value', $Value, 'Invalid res/images/unexisting-key value');
    }

    /**
     * Testing setup of the existing array.
     */
    public function test_add_complex_existing_array()
    {
        $Value = get_config_value(array(
            'res',
            'css'
        ));

        $this->assertContains('http:////res/css/application.css', $Value, 'Invalid css files list');

        add_config_value('res/css', 'set-value');

        $Value = get_config_value(array(
            'res',
            'css'
        ));

        $this->assertContains('set-value', $Value, 'Invalid css files list');
    }

    /**
     * Testing setup of the unexisting array.
     */
    public function test_add_complex_unexisting_array()
    {
        delete_config_value(array(
            'unexisting-key'
        ));

        $Value = get_config_value(array(
            'unexisting-key'
        ));

        $this->assertEquals(false, $Value, 'Invalid unexisting-key processing');

        add_config_value('unexisting-key', 'set-value');

        $Value = get_config_value(array(
            'unexisting-key'
        ));

        $this->assertContains('set-value', $Value, 'Invalid unexisting-key value');
    }

    /**
     * Testing setup of the unexisting array with simple route.
     */
    public function test_add_unexisting_array()
    {
        delete_config_value(array(
            'unexisting-key'
        ));

        $Value = get_config_value(array(
            'unexisting-key'
        ));

        $this->assertEquals(false, $Value, 'Invalid unexisting-key processing');

        add_config_value('unexisting-key', 'set-value');

        $Value = get_config_value(array(
            'unexisting-key'
        ));

        $this->assertContains('set-value', $Value, 'Invalid unexisting-key value');
    }

    /**
     * Testing setup of the existing array with simple route.
     */
    public function test_add_existing_array()
    {
        add_config_value('unexisting-key', 'set-value-1');
        add_config_value('unexisting-key', 'set-value-2');

        $Value = get_config_value(array(
            'unexisting-key'
        ));

        $this->assertContains('set-value-2', $Value, 'Invalid unexisting-key value');
    }

    /**
     * Testing setup of the existing array with simple route.
     */
    public function test_complex_string_routes()
    {
        set_config_value('f1/f2/unexisting-key', 'set-value-1');

        $Value = get_config_value('f1/f2/unexisting-key');

        $this->assertEquals('set-value-1', $Value, 'Invalid unexisting-key value');
    }

    /**
     * Deleting simple key.
     */
    public function test_delete_first_value()
    {
        set_config_value('key-1', 'value');

        $Value = get_config_value('key-1');

        $this->assertEquals('value', $Value, 'Invalid setting value');

        delete_config_value('key-1');

        $Value = get_config_value('key-1', false);

        $this->assertEquals(false, $Value, 'Key was not deleted');
    }

    /**
     * Deleting deep key.
     */
    public function test_delete_next_value()
    {
        set_config_value('key-2/key-3', 'value');

        $Value = get_config_value('key-2/key-3');

        $this->assertEquals('value', $Value, 'Invalid setting value');

        delete_config_value('key-2/key-3');

        $Value = get_config_value('key-2/key-3', false);

        $this->assertEquals(false, $Value, 'Key was not deleted');
    }

    /**
     * Deleting empty keys.
     */
    public function test_delete_empty_keys()
    {
        set_config_value('key-4/key-5', 'value');

        delete_config_value('key-4/key-5');

        $Value = get_config_value('key-4', false);

        $this->assertEquals(false, $Value, 'Key was not deleted');
    }

    /**
     * No deleting not empty keys.
     */
    public function test_no_delete_not_empty_keys()
    {
        set_config_value('key-6/key-7', 'value');
        set_config_value('key-6/key-8', 'value');

        delete_config_value('key-6/key-7');

        $Value = get_config_value('key-6', false);

        $this->assertEquals(true, is_array($Value), 'Key was deleted');

        $Value = get_config_value('key-6/key-8', false);

        $this->assertEquals('value', $Value, 'Key was deleted');
    }

    /**
     * Testing delete results.
     */
    public function test_delete_result()
    {
        set_config_value('key-9/key-10', 'value');

        // deleting unexisting value
        $Result = delete_config_value('key-9/key-unexisting');

        $this->assertEquals(false, $Result, 'Invalid deleting result');

        // deleting existing value
        $Result = delete_config_value('key-9/key-10');

        $this->assertEquals(true, $Result, 'Invalid deleting result');
    }

    /**
     * Testing fas BD setup.
     */
    public function test_fast_db_setup()
    {
        add_connection_to_config('connection', 'dsn', 'user', 'password');

        $Value = get_config_value('connection/dsn', false);
        $this->assertEquals('dsn', $Value, 'Key connection/dsn was not found');

        $Value = get_config_value('connection/user', false);
        $this->assertEquals('user', $Value, 'Key connection/user was not found');

        $Value = get_config_value('connection/password', false);
        $this->assertEquals('password', $Value, 'Key connection/password was not found');
    }
}

?>