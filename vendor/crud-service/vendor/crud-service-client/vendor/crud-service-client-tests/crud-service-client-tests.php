<?php

/**
 * Class CrudServiceClientTests
 *
 * @package     CrudServiceClient
 * @subpackage  CrudServiceClientTests
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/17)
 * @copyright   Copyright (c) 2019, aeon.org
 */

/**
 * Common unit tests for CrudServiceClient and all derived client classes
 *
 * @author Dodonov A.A.
 */
class CrudServiceClientTests extends ServiceClientTests
{

    /**
     * Client class name
     */
    protected $ClientClassName = '';

    /**
     * Method creates client object
     *
     * @param string $Password
     */
    protected function constructClient(string $Password = 'root')
    {
        $Client = new $this->ClientClassName(EXISTING_LOGIN, $Password);

        return ($Client);
    }

    /**
     * Testing API connection
     */
    public function testValidConnect()
    {
        $Client = $this->constructClient();

        $this->assertNotEquals($Client->getSessionId(), false, 'Connection failed');
        $this->assertEquals($Client->Login, EXISTING_LOGIN, 'Login was not saved');
    }

    /**
     * Testing invalid API connection
     */
    public function testInValidConnect()
    {
        try {
            $this->constructClient('1234567');

            $this->fail('No exception was thrown');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing setting valid token
     */
    public function testSetValidToken()
    {
        $Client = $this->constructClient();

        $NewClient = new $this->ClientClassName();
        $NewClient->setToken($Client->getSessionId());

        $this->assertNotEquals($NewClient->getSessionId(), false, 'Token was not set(1)');
    }

    /**
     * Testing setting valid token and login
     */
    public function testSetValidTokenAndLogin()
    {
        $Client = $this->constructClient();

        $NewClient = new $this->ClientClassName();
        $NewClient->setToken($Client->getSessionId(), 'alexey@dodonov.none');

        $this->assertNotEquals($NewClient->getSessionId(), false, 'Token was not set(2)');
        $this->assertNotEquals($NewClient->getStoredLogin(), false, 'Login was not saved');
    }

    /**
     * Testing setting invalid token
     */
    public function testSetInValidToken()
    {
        $Client = new $this->ClientClassName();

        try {
            $Client->setToken('unexistingtoken');

            $this->fail('Invalid token was set');
        } catch (Exception $e) {
            $this->assertEquals(1, 1, 'Token was not set(3)');
        }
    }

    /**
     * Testing loginAs method
     */
    public function testLoginAs()
    {
        $Client = $this->construct_client();

        try {
            $Client->loginAs(EXISTING_LOGIN);
        } catch (Exception $e) {
            $this->assertEquals(0, 1, 'Login was was not called properly');
        }
    }

    /**
     * Testing loginAs method with failed call
     */
    public function testFailedLoginAs()
    {
        $Client = $this->construct_client();

        try {
            $Client->loginAs('alexey@dodonov.none');

            $this->fail('Unexisting user logged in');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing situation that loginAs will not be called after the connect() call with the same login
     */
    public function testSingleLoginAs()
    {
        $this->assertEquals(0, 1, 'Test was not created');
    }
}
