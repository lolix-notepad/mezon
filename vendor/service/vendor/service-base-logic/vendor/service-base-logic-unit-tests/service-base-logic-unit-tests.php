<?php

/**
 * Service logic utin tests
 *
 * @package ServiceLogic
 * @subpackage ServiceBaseLogicUnitTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Mock parameter fetcher
 *
 * @author Dodonov A.A.
 */
class MockParamsFetcher implements \Mezon\Service\ServiceRequestParams
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
    public function getParam($Param, $Default = false)
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
    public function setToken(string $Token): string
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
    var $ClassName;

    /**
     * Constructor
     */
    public function __construct(string $ClassName = '\Mezon\Service\ServiceBaseLogic')
    {
        parent::__construct();

        $this->ClassName = $ClassName;
    }

    /**
     * Method tests creation of the logis's parts
     *
     * @param object $Logic
     *            ServiceLogic object
     * @param string $Msg
     *            Error message
     */
    protected function checkLogicParts(object $Logic, string $Msg): void
    {
        $this->assertInstanceOf('MockParamsFetcher', $Logic->ParamsFetcher, $Msg);
        $this->assertInstanceOf('MockSecurityProvider', $Logic->SecurityProvider, $Msg);
        $this->assertInstanceOf('MockModel', $Logic->Model, $Msg);
    }

    /**
     * Testing connect method
     */
    public function testConstruct1(): void
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
    public function testConstruct2(): void
    {
        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(), new MockSecurityProvider(), new MockModel());

        $Msg = 'Construction failed for defined model object';

        $this->checkLogicParts($Logic, $Msg);
    }

    /**
     * Testing connect method
     */
    public function testConstruct3(): void
    {
        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(), new MockSecurityProvider(), 'MockModel');

        $Msg = 'Construction failed for defined model name';

        $this->checkLogicParts($Logic, $Msg);
    }
}

?>