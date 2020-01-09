<?php
namespace Mezon\Service\ServiceLogic;

/**
 * Class ServiceLogicUnitTests
 *
 * @package ServiceLogic
 * @subpackage ServiceLogicUnitTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Base class for service logic unit tests
 *
 * @author Dodonov A.A.
 */
class ServiceLogicUnitTests extends \Mezon\Service\ServiceBaseLogic\ServiceBaseLogicUnitTests
{

    /**
     * Constructor
     */
    public function __construct(string $ClassName = \Mezon\Service\ServiceLogic::class)
    {
        parent::__construct($ClassName);
    }

    /**
     * Method returns mock of the security provider
     */
    protected function getSecurityProviderMock()
    {
        $Mock = $this->getMockBuilder(\Mezon\Service\ServiceMockSecurityProvider::class)
            ->disableOriginalConstructor()
            ->setMethods([
            'connect',
            'setToken',
            'getParam',
            'validatePermit'
        ])
            ->getMock();

        $Mock->method('connect')->will($this->returnValue('valuevalue'));
        $Mock->method('setToken')->will($this->returnValue('token'));

        return ($Mock);
    }

    /**
     * Testing connection routine
     */
    public function testConnect()
    {
        $SecurityProviderMock = $this->getSecurityProviderMock();

        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(
            new \Mezon\Service\ServiceBaseLogic\MockParamsFetcher(),
            $SecurityProviderMock);

        $Result = $Logic->connect();

        $this->assertEquals('valuevalue', $Result['session_id'], 'Connection failed');
    }

    /**
     * Testing connection routine
     */
    public function testConnectWithEmptyParams()
    {
        $SecurityProviderMock = $this->getSecurityProviderMock();

        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(
            new \Mezon\Service\ServiceBaseLogic\MockParamsFetcher(false),
            $SecurityProviderMock);

        try {
            $Logic->connect();

            $this->fail('Exception was not thrown');
        } catch (\Exception $e) {
            $this->assertEquals(
                'Fields login and/or password were not set',
                $e->getMessage(),
                'Connection error processing failed');
        }
    }

    /**
     * Testing setToken method
     */
    public function testSetToken()
    {
        // setup
        $SecurityProviderMock = $this->getSecurityProviderMock();

        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(
            new \Mezon\Service\ServiceBaseLogic\MockParamsFetcher(),
            $SecurityProviderMock);

        // test body
        $Result = $Logic->setToken();

        // assertions
        $this->assertEquals('token', $Result['session_id'], 'Setting token failed');
    }

    /**
     * Testing getSelfId method
     */
    public function testGetSelfId()
    {
        // setup
        $SecurityProviderMock = $this->getSecurityProviderMock();

        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(
            new \Mezon\Service\ServiceBaseLogic\MockParamsFetcher(),
            $SecurityProviderMock);

        // test body
        $Result = $Logic->getSelfId();

        // assertions
        $this->assertEquals(1, $Result['id'], 'Getting self id failed');
    }

    /**
     * Testing getSelfLogin method
     */
    public function testGetSelfLogin()
    {
        // setup
        $SecurityProviderMock = $this->getSecurityProviderMock();

        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(
            new \Mezon\Service\ServiceBaseLogic\MockParamsFetcher(),
            $SecurityProviderMock);

        // test body
        $Result = $Logic->getSelfLogin();

        // assertions
        $this->assertEquals('admin@localhost', $Result['login'], 'Getting self login failed');
    }

    /**
     * Testing loginAs method
     */
    public function testLoginAs()
    {
        // setup
        $SecurityProviderMock = $this->getSecurityProviderMock();

        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(
            new \Mezon\Service\ServiceBaseLogic\MockParamsFetcher(),
            $SecurityProviderMock);

        // test body
        $Result = $Logic->loginAs();

        // assertions
        $this->assertEquals('value', $Result['session_id'], 'Getting self login failed');
    }

    /**
     * Testing validatePermit method
     */
    public function testValidatePermit()
    {
        // setup
        $SecurityProviderMock = $this->getSecurityProviderMock();
        $SecurityProviderMock->method('validatePermit')->with($this->equalTo('value'), $this->equalTo('admin'));

        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(
            new \Mezon\Service\ServiceBaseLogic\MockParamsFetcher(),
            $SecurityProviderMock);

        // test body and assertions
        $Logic->validatePermit('admin');
        $this->addToAssertionCount(1);
    }
}
