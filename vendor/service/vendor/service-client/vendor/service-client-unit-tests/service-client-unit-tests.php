<?php
/**
 * Class ServiceClientUnitTests
 *
 * @package     ServiceClient
 * @subpackage  ServiceClientUnitTests
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/09/20)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../service-client.php');

require_once (__DIR__ . '/dns.php');
//TODO replace fast-auth service
/**
 * Basic tests for service client
 */
class ServiceClientUnitTests extends PHPUnit\Framework\TestCase
{

    /**
     * Client class name
     */
    var $ClientClassName = '';

    /**
     * Constructor
     *
     * @param string $ClientClassName
     *            Service client class name
     */
    public function __construct(string $ClientClassName = '\Mezon\Service\ServiceClient')
    {
        parent::__construct();

        $this->ClientClassName = $ClientClassName;
    }

    /**
     * Method creates mock for the service client
     *
     * @param array $Methods
     *            mocking methods
     * @return object Mock
     */
    protected function get_service_client_raw_mock(array $Methods = [
        'post_request',
        'get_request'
    ]): object
    {
        $Mock = $this->getMockBuilder($this->ClientClassName)
            ->setMethods($Methods)
            ->disableOriginalConstructor()
            ->getMock();

        return ($Mock);
    }

    /**
     * Method creates mock with setup
     *
     * @param string $DataFile
     *            File name with testing data
     * @return object Mock object
     */
    protected function get_service_client_mock(string $DataFile): object
    {
        $Mock = $this->get_service_client_raw_mock([
            'send_request'
        ]);

        $Mock->method('send_request')->will($this->returnValue(json_decode(file_get_contents(__DIR__ . '/conf/' . $DataFile . '.json'), true)));

        return ($Mock);
    }

    /**
     * Testing construction with login and password
     */
    public function test_construct_with_login(): void
    {
        // setup
        $Mock = $this->get_service_client_mock('construct-with-login');

        // test body
        $Mock->__construct('http://fast-auth.gdzone.ru/', 'login', 'password');

        // assertions
        $this->assertEquals('login', $Mock->get_stored_login(), 'Login was not set');
        $this->assertEquals('session id', $Mock->get_token(), 'SessionId was not set');
    }

    /**
     * Testing constructor
     */
    public function test_set_header(): void
    {
        // setup
        $Client = new $this->ClientClassName('http://fast-auth.gdzone.ru/');

        // test body and assertions
        $this->assertEquals('', $Client->Service, 'Field was init but it must not');
    }

    /**
     * Checking exception throwing if the service was not found
     */
    public function test_no_service_found(): void
    {
        try {
            $Client = new $this->ClientClassName('auth');

            $this->fail('Exception must be thrown ' . serialize($Client));
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing that service was found.
     */
    public function test_service_found(): void
    {
        $Client = new $this->ClientClassName('existing-service');

        $this->assertEquals('existing-service', $Client->Service, 'Field was init but it must not');
    }

    /**
     * Testing post_request
     */
    public function test_post_request(): void
    {
        $Mock = $this->get_service_client_mock('test-post-request');

        $Result = $Mock->post_request('http://ya.ru', []);

        $this->assertEquals(1, $Result->result, 'Invalid result was returned');
    }

    /**
     * Testing get_request
     */
    public function test_get_request(): void
    {
        $Mock = $this->get_service_client_mock('test-get-request');

        $Result = $Mock->get_request('http://ya.ru');

        $this->assertEquals(1, $Result->result, 'Invalid result was returned');
    }

    /**
     * Testing set_token method
     */
    public function test_set_token(): void
    {
        // setup
        $Mock = $this->get_service_client_raw_mock(); // we need this function, as we need mock without any extra setup

        // test body
        $Mock->set_token('token', 'login');

        // assertions
        $this->assertEquals('token', $Mock->get_token(), 'SessionId was not set');
        $this->assertEquals('login', $Mock->get_stored_login(), 'Login was not set');
    }

    /**
     * Testing get_token method
     */
    public function test_get_token(): void
    {
        // setup
        $Mock = $this->get_service_client_raw_mock(); // we need this function, as we need mock without any extra setup

        // test body
        $SessionId = $Mock->get_token();

        // assertions
        $this->assertEquals('', $SessionId, 'Invalid session id');
    }

    /**
     * Testing set_token method
     */
    public function test_set_token_exception(): void
    {
        // setup
        $Mock = $this->get_service_client_raw_mock(); // we need this function, as we need mock without any extra setup

        // test body and assertions
        try {
            $Mock->set_token('');
            $this->fail('Empty token must cause throwing of the exception');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing get_self_id method
     */
    public function test_get_self_id(): void
    {
        // setup
        $Mock = $this->get_service_client_mock('self-id');

        // test body
        $SelfId = $Mock->get_self_id();

        // assertions
        $this->assertEquals('123', $SelfId, 'Invalid self id');
    }

    /**
     * Testing get_self_login method
     */
    public function test_get_self_login(): void
    {
        // setup
        $Mock = $this->get_service_client_mock('self-login');

        // test body
        $SelfLogin = $Mock->get_self_login();

        // assertions
        $this->assertEquals('admin', $SelfLogin, 'Invalid self login');
    }

    /**
     * Testing login_as method
     */
    public function test_login_as_with_invalid_session_id(): void
    {
        // setup
        $Mock = $this->get_service_client_mock('login-with-invalid-session-id');

        // test body
        try {
            $Mock->login_as('registered', 'login');
            // assertions
            $this->fail();
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing login_as method
     */
    public function test_login_as_with_invalid_session_id_2(): void
    {
        // setup
        $Mock = $this->get_service_client_mock('login-with-invalid-session-id');

        // test body
        try {
            $Mock->login_as('registered', 'id');
            // assertions
            $this->fail();
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing login_as method
     */
    public function test_login_as(): void
    {
        // setup
        $Mock = $this->get_service_client_mock('login-as');

        // test body
        $Mock->login_as('registered', 'login');

        // assertions
        $this->assertEquals('session-id', $Mock->get_token(), 'Invalid self login');
    }

    /**
     * Testing construction with login and password and invalid session_id
     */
    public function test_construct_with_login_and_invalid_session_id(): void
    {
        // setup
        $Mock = $this->get_service_client_mock('login-with-invalid-session-id');

        // test body and assertions
        try {
            $Mock->__construct('http://fast-auth.gdzone.ru/', 'login', 'password');
            $this->fail('Exception must be thrown');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }
}

?>