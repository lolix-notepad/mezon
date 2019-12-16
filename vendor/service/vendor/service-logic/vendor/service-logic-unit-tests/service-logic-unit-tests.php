<?php

/**
 * Class ServiceLogicUnitTests
 *
 * @package     ServiceLogic
 * @subpackage  ServiceLogicUnitTests
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/17)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../service-base-logic/vendor/service-base-logic-unit-tests/service-base-logic-unit-tests.php');
require_once (__DIR__ . '/../../../service-mock-security-provider/service-mock-security-provider.php');
require_once (__DIR__ . '/../../../service-security-provider/service-security-provider.php');

/**
 * Base class for service logic unit tests
 *
 * @author Dodonov A.A.
 */
class ServiceLogicUnitTests extends ServiceBaseLogicUnitTests
{

    /**
     * Testing connect method
     */
    public function test_construct_1()
    {
        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(), new MockSecurityProvider());

        $Msg = 'Construction failed for default model';

        $this->assertInstanceOf('MockParamsFetcher', $Logic->ParamsFetcher, $Msg);
        $this->assertInstanceOf('MockSecurityProvider', $Logic->SecurityProvider, $Msg);
        $this->assertEquals(null, $Logic->Model, $Msg);
    }

    /**
     * Testing connect method
     */
    public function test_construct_2()
    {
        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(), new MockSecurityProvider(), new MockModel());

        $Msg = 'Construction failed for defined model object';

        $this->check_logic_parts($Logic, $Msg);
    }

    /**
     * Testing connect method
     */
    public function test_construct_3()
    {
        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(), new MockSecurityProvider(), 'MockModel');

        $Msg = 'Construction failed for defined model name';

        $this->check_logic_parts($Logic, $Msg);
    }

    /**
     * Method returns mock of the security provider
     */
    protected function get_security_provider_mock()
    {
        $Mock = $this->getMockBuilder('ServiceMockSecurityProvider')
            ->disableOriginalConstructor()
            ->setMethods([
            'connect',
            'set_token',
            'get_param',
            'validate_permit'
        ])
            ->getMock();

        $Mock->method('connect')->will($this->returnValue('valuevalue'));
        $Mock->method('set_token')->will($this->returnValue('token'));

        return ($Mock);
    }

    /**
     * Testing connection routine
     */
    public function test_connect()
    {
        $SecurityProviderMock = $this->get_security_provider_mock();

        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(), $SecurityProviderMock);

        $Result = $Logic->connect();

        $this->assertEquals('valuevalue', $Result['session_id'], 'Connection failed');
    }

    /**
     * Testing connection routine
     */
    public function test_connect_with_empty_params()
    {
        $SecurityProviderMock = $this->get_security_provider_mock();

        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(false), $SecurityProviderMock);

        try {
            $Logic->connect();

            $this->fail('Exception was not thrown');
        } catch (Exception $e) {
            $this->assertEquals('Fields login and/or password were not set', $e->getMessage(), 'Connection error processing failed');
        }
    }

    /**
     * Testing set_token method
     */
    public function test_set_token()
    {
        // setup
        $SecurityProviderMock = $this->get_security_provider_mock();

        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(), $SecurityProviderMock);

        // test body
        $Result = $Logic->set_token();

        // assertions
        $this->assertEquals('token', $Result['session_id'], 'Setting token failed');
    }

    /**
     * Testing get_self_id method
     */
    public function test_get_self_id()
    {
        // setup
        $SecurityProviderMock = $this->get_security_provider_mock();

        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(), $SecurityProviderMock);

        // test body
        $Result = $Logic->get_self_id();

        // assertions
        $this->assertEquals(1, $Result['id'], 'Getting self id failed');
    }

    /**
     * Testing get_self_login method
     */
    public function test_get_self_login()
    {
        // setup
        $SecurityProviderMock = $this->get_security_provider_mock();

        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(), $SecurityProviderMock);

        // test body
        $Result = $Logic->get_self_login();

        // assertions
        $this->assertEquals('admin@localhost', $Result['login'], 'Getting self login failed');
    }

    /**
     * Testing login_as method
     */
    public function test_login_as()
    {
        // setup
        $SecurityProviderMock = $this->get_security_provider_mock();

        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(), $SecurityProviderMock);

        // test body
        $Result = $Logic->login_as();

        // assertions
        $this->assertEquals('value', $Result['session_id'], 'Getting self login failed');
    }

    /**
     * Testing validate_permit method
     */
    public function test_validate_permit()
    {
        // setup
        $SecurityProviderMock = $this->get_security_provider_mock();
        $SecurityProviderMock->method('validate_permit')->with($this->equalTo('value'), $this->equalTo('admin'));

        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(), $SecurityProviderMock);

        // test body and assertions
        $Logic->validate_permit('admin');
        $this->addToAssertionCount(1);
    }
}

?>