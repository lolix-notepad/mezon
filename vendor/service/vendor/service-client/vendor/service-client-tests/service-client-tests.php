<?php
namespace Mezon\Service\ServiceClient\ServiceClientTests;

/**
 * Class ServiceClientTests
 *
 * @package ServiceClient
 * @subpackage ServiceClientTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Common unit tests for ServiceClient and all derived client classes
 *
 * @author Dodonov A.A.
 * @group baseTests
 */
class ServiceClientTests extends \PHPUnit\Framework\TestCase
{

    /**
     * Client class name
     */
    protected $ClientClassName = '';

    /**
     * Existing user's login
     *
     * @var string
     */
    protected $ExistingLogin = '';

    /**
     * Constructor
     *
     * @param string $ExistingLogin
     */
    public function __construct(string $ExistingLogin)
    {
        parent::__construct();

        $this->ExistingLogin = $ExistingLogin;
    }

    /**
     * Method creates client object
     *
     * @param string $Password
     */
    protected function constructClient(string $Password = 'root')
    {
        $Client = new $this->ClientClassName($this->ExistingLogin, $Password);

        return ($Client);
    }

    /**
     * Testing API connection
     */
    public function testValidConnect()
    {
        $Client = $this->construct_client();

        $this->assertNotEquals($Client->getSessionId(), false, 'Connection failed');
        $this->assertEquals($Client->Login, $this->ExistingLogin, 'Login was not saved');
    }

    /**
     * Testing invalid API connection
     */
    public function testInValidConnect()
    {
        $this->expectException(\Exception::class);
        $this->construct_client('1234567');
    }

    /**
     * Testing setting valid token
     */
    public function testSetValidToken()
    {
        $Client = $this->construct_client();

        $NewClient = new $this->ClientClassName();
        $NewClient->setToken($Client->getSessionId());

        $this->assertNotEquals($NewClient->getSessionId(), false, 'Token was not set(1)');
    }

    /**
     * Testing setting valid token and login
     */
    public function testSetValidTokenAndLogin()
    {
        $Client = $this->construct_client();

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

        $this->expectException(\Exception::class);
        $Client->setToken('unexistingtoken');
    }

    /**
     * Testing loginAs method
     */
    public function testLoginAs()
    {
        $Client = $this->construct_client();

        try {
            $Client->loginAs($this->ExistingLogin);
        } catch (\Exception $e) {
            $this->assertEquals(0, 1, 'Login was was not called properly');
        }
    }

    /**
     * Testing loginAs method with failed call
     */
    public function testFailedLoginAs()
    {
        $Client = $this->construct_client();

        $this->expectException(\Exception::class);
        $Client->loginAs('alexey@dodonov.none');
    }

    /**
     * Testing situation that loginAs will not be called after the connect() call with the same login
     */
    public function testSingleLoginAs()
    {
        $this->assertEquals(0, 1, 'Test was not created');
    }
}
