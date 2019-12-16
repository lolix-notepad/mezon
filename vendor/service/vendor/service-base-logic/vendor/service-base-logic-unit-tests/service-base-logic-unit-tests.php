<?php
/**
 * 	Service logic utin tests
 *
 * @package     ServiceLogic
 * @subpackage  ServiceBaseLogicUnitTests
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/17)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../service-security-provider/service-security-provider.php');
require_once (__DIR__ . '/../../../service-logic/service-logic.php');
require_once (__DIR__ . '/../../../service-mock-security-provider/service-mock-security-provider.php');

/**
 * Mock parameter fetcher
 *
 * @author Dodonov A.A.
 */
class MockParamsFetcher implements ServiceRequestParams
{

    /**
     * Some testing value
     *
     * @var string
     */
    var $Value = false;

    /**
     * Constructor
     *
     * @param string $Value
     *            Value to be set
     */
    public function __construct($Value = 'value')
    {
        $this->Value = $Value;
    }

    /**
     * Method returns request parameter
     *
     * @param string $Param
     *            parameter name
     * @param mixed $Default
     *            default value
     * @return mixed Parameter value
     */
    public function get_param($Param, $Default = false)
    {
        return ($this->Value);
    }
}

/**
 * Mock security provider
 *
 * @author Dodonov A.A.
 */
class MockSecurityProvider
{

    /**
     * Connection function
     *
     * @param string $Login
     *            Login
     * @param string $Password
     *            Password
     * @return string Result of the connection
     */
    public function connect(string $Login, string $Password)
    {
        return ($Login . $Password);
    }

    /**
     * Getter for login field name
     *
     * @return string Login field name
     */
    public function get_login_field_name(): string
    {
        return ('login');
    }

    /**
     * Setting token
     *
     * @param string $Token
     *            Token
     * @return string Token
     */
    public function set_token(string $Token): string
    {
        return ($Token);
    }
}

/**
 * Mock model
 *
 * @author Dodonov A.A.
 */
class MockModel
{
}

/**
 * Base class for service logic unit tests.
 *
 * @author Dodonov A.A.
 */
class ServiceBaseLogicUnitTests extends PHPUnit\Framework\TestCase
{

    /**
     * Testing class name.
     *
     * @var string
     */
    var $ClassName = 'ServiceLogic';

    /**
     * Method tests creation of the logis's parts
     *
     * @param object $Logic
     *            ServiceLogic object
     * @param string $Msg
     *            Error message
     */
    protected function check_logic_parts(object $Logic, string $Msg)
    {
        $this->assertInstanceOf('MockParamsFetcher', $Logic->ParamsFetcher, $Msg);
        $this->assertInstanceOf('MockSecurityProvider', $Logic->SecurityProvider, $Msg);
        $this->assertInstanceOf('MockModel', $Logic->Model, $Msg);
    }

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
}

?>