<?php
namespace Mezon\Service\ServiceClient;

/**
 * Class ServiceClientUnitTests
 *
 * @package ServiceClient
 * @subpackage ServiceClientUnitTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/09/20)
 * @copyright Copyright (c) 2019, aeon.org
 */

// TODO replace fast-auth service
require_once (__DIR__ . '/dns.php');

/**
 * Basic tests for service client
 */
class ServiceClientUnitTests extends \PHPUnit\Framework\TestCase
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
    protected function getServiceClientRawMock(array $Methods = [
        'postRequest',
        'getRequest'
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
    protected function getServiceClientMock(string $DataFile): object
    {
        $Mock = $this->getServiceClientRawMock([
            'sendRequest'
        ]);

        $Mock->method('sendRequest')->will($this->returnValue(json_decode(file_get_contents(__DIR__ . '/conf/' . $DataFile . '.json'), true)));

        return ($Mock);
    }

    /**
     * Testing construction with login and password
     */
    public function testConstructWithLogin(): void
    {
        // setup
        $Mock = $this->getServiceClientMock('construct-with-login');

        // test body
        $Mock->__construct('http://fast-auth.gdzone.ru/', 'login', 'password');

        // assertions
        $this->assertEquals('login', $Mock->getStoredLogin(), 'Login was not set');
        $this->assertEquals('session id', $Mock->getToken(), 'SessionId was not set');
    }

    /**
     * Testing constructor
     */
    public function testSetHeader(): void
    {
        // setup
        $Client = new $this->ClientClassName('http://fast-auth.gdzone.ru/');

        // test body and assertions
        $this->assertEquals('', $Client->Service, 'Field was init but it must not');
    }

    /**
     * Checking exception throwing if the service was not found
     */
    public function testNoServiceFound(): void
    {
        try {
            $Client = new $this->ClientClassName('auth');

            $this->fail('Exception must be thrown ' . serialize($Client));
        } catch (\Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing that service was found.
     */
    public function testServiceFound(): void
    {
        $Client = new $this->ClientClassName('existing-service');

        $this->assertEquals('existing-service', $Client->Service, 'Field was init but it must not');
    }

    /**
     * Testing postRequest
     */
    public function testPostRequest(): void
    {
        $Mock = $this->getServiceClientMock('test-post-request');

        $Result = $Mock->postRequest('http://ya.ru', []);

        $this->assertEquals(1, $Result->result, 'Invalid result was returned');
    }

    /**
     * Testing getRequest
     */
    public function testGetRequest(): void
    {
        $Mock = $this->getServiceClientMock('test-get-request');

        $Result = $Mock->getRequest('http://ya.ru');

        $this->assertEquals(1, $Result->result, 'Invalid result was returned');
    }

    /**
     * Testing setToken method
     */
    public function testSetToken(): void
    {
        // setup
        $Mock = $this->getServiceClientRawMock(); // we need this function, as we need mock without any extra setup

        // test body
        $Mock->setToken('token', 'login');

        // assertions
        $this->assertEquals('token', $Mock->getToken(), 'SessionId was not set');
        $this->assertEquals('login', $Mock->getStoredLogin(), 'Login was not set');
    }

    /**
     * Testing getToken method
     */
    public function testGetToken(): void
    {
        // setup
        $Mock = $this->getServiceClientRawMock(); // we need this function, as we need mock without any extra setup

        // test body
        $SessionId = $Mock->getToken();

        // assertions
        $this->assertEquals('', $SessionId, 'Invalid session id');
    }

    /**
     * Testing setToken method
     */
    public function testSetTokenException(): void
    {
        // setup
        $Mock = $this->getServiceClientRawMock(); // we need this function, as we need mock without any extra setup

        // test body and assertions
        try {
            $Mock->setToken('');
            $this->fail('Empty token must cause throwing of the exception');
        } catch (\Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing getSelfId method
     */
    public function testGetSelfId(): void
    {
        // setup
        $Mock = $this->getServiceClientMock('self-id');

        // test body
        $SelfId = $Mock->getSelfId();

        // assertions
        $this->assertEquals('123', $SelfId, 'Invalid self id');
    }

    /**
     * Testing getSelfLogin method
     */
    public function testGetSelfLogin(): void
    {
        // setup
        $Mock = $this->getServiceClientMock('self-login');

        // test body
        $SelfLogin = $Mock->getSelfLogin();

        // assertions
        $this->assertEquals('admin', $SelfLogin, 'Invalid self login');
    }

    /**
     * Testing loginAs method
     */
    public function testLoginAsWithInvalidSessionId(): void
    {
        // setup
        $Mock = $this->getServiceClientMock('login-with-invalid-session-id');

        // test body
        try {
            $Mock->loginAs('registered', 'login');
            // assertions
            $this->fail();
        } catch (\Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing loginAs method
     */
    public function testLoginAsWithInvalidSessionId2(): void
    {
        // setup
        $Mock = $this->getServiceClientMock('login-with-invalid-session-id');

        // test body
        try {
            $Mock->loginAs('registered', 'id');
            // assertions
            $this->fail();
        } catch (\Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing loginAs method
     */
    public function testLoginAs(): void
    {
        // setup
        $Mock = $this->getServiceClientMock('login-as');

        // test body
        $Mock->loginAs('registered', 'login');

        // assertions
        $this->assertEquals('session-id', $Mock->getToken(), 'Invalid self login');
    }

    /**
     * Testing construction with login and password and invalid session_id
     */
    public function testConstructWithLoginAndInvalidSessionId(): void
    {
        // setup
        $Mock = $this->getServiceClientMock('login-with-invalid-session-id');

        // test body and assertions
        try {
            $Mock->__construct('http://fast-auth.gdzone.ru/', 'login', 'password');
            $this->fail('Exception must be thrown');
        } catch (\Exception $e) {
            $this->addToAssertionCount(1);
        }
    }
}

?>