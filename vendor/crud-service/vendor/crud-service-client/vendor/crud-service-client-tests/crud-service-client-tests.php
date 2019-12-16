<?php
/**
 * Class CRUDServiceClientTests
 *
 * @package     CRUDServiceClient
 * @subpackage  CRUDServiceClientTests
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/17)
 * @copyright   Copyright (c) 2019, aeon.org
 */

require_once(__DIR__.'/../../../service/vendor/service-client/vendor/service-client-tests.php');

/**
 * Common unit tests for CRUDServiceClient and all derived client classes
 * 
 * @author Dodonov A.A.
 */
class CRUDServiceClientTests extends ServiceClientTests
{

    /**
     * Client class name
     */
    var $ClientClassName = '';

    /**
     * Method creates client object
     * 
     * @param string $Password
     */
    protected function construct_client(string $Password = 'root')
    {
    	$Client = new $this->ClientClassName(EXISTING_LOGIN, $Password);

    	return($Client);
    }

    /**
     * Testing API connection
     */
    public function test_valid_connect()
    {
    	$Client = $this->construct_client();

        $this->assertNotEquals($Client->get_session_id(), false, 'Connection failed');
        $this->assertEquals($Client->Login, EXISTING_LOGIN, 'Login was not saved');
    }

    /**
     * Testing invalid API connection
     */
    public function test_in_valid_connect()
    {
        try {
        	$this->construct_client('1234567');

            $this->fail('No exception was thrown');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing setting valid token
     */
    public function test_set_valid_token()
    {
    	$Client = $this->construct_client();

        $NewClient = new $this->ClientClassName();
        $NewClient->set_token($Client->get_session_id());

        $this->assertNotEquals($NewClient->get_session_id(), false, 'Token was not set(1)');
    }

    /**
     * Testing setting valid token and login
     */
    public function test_set_valid_token_and_login()
    {
    	$Client = $this->construct_client();

        $NewClient = new $this->ClientClassName();
        $NewClient->set_token($Client->get_session_id(), 'alexey@dodonov.none');

        $this->assertNotEquals($NewClient->get_session_id(), false, 'Token was not set(2)');
        $this->assertNotEquals($NewClient->get_stored_login(), false, 'Login was not saved');
    }

    /**
     * Testing setting invalid token
     */
    public function test_set_in_valid_token()
    {
        $Client = new $this->ClientClassName();

        try {
            $Client->set_token('unexistingtoken');

            $this->fail('Invalid token was set');
        } catch (Exception $e) {
            $this->assertEquals(1, 1, 'Token was not set(3)');
        }
    }

    /**
     * Testing login_as method
     */
    public function test_login_as()
    {
    	$Client = $this->construct_client();

        try {
        	$Client->login_as(EXISTING_LOGIN);
        } catch (Exception $e) {
            $this->assertEquals(0, 1, 'Login was was not called properly');
        }
    }

    /**
     * Testing login_as method with failed call
     */
    public function test_failed_login_as()
    {
    	$Client = $this->construct_client();

        try {
            $Client->login_as('alexey@dodonov.none');

            $this->fail('Unexisting user logged in');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing situation that login_as will not be called after the connect() call with the same login
     */
    public function test_single_login_as()
    {
        $this->assertEquals(0, 1, 'Test was not created');
    }
}

?>