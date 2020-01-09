<?php
namespace Mezon\Service\ServiceBaseLogic;

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
    protected $Value = false;

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
class MockSecurityProvider implements \Mezon\Service\ServiceSecurityProvider
{

    /**
     * 
     * {@inheritDoc}
     * @see \Mezon\Service\ServiceSecurityProvider::connect()
     */
    public function connect(string $Login, string $Password):string
    {
        return ($Login . $Password);
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Mezon\Service\ServiceSecurityProvider::getLoginFieldName()
     */
    public function getLoginFieldName(): string
    {
        return ('login');
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Mezon\Service\ServiceSecurityProvider::setToken()
     */
    public function setToken(string $Token): string
    {
        return ($Token);
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Mezon\Service\ServiceSecurityProvider::hasPermit()
     */
    public function hasPermit(string $Token, string $Permit): bool
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \Mezon\Service\ServiceSecurityProvider::validatePermit()
     */
    public function validatePermit(string $Token, string $Permit)
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \Mezon\Service\ServiceSecurityProvider::getSelfLogin()
     */
    public function getSelfLogin(string $Token): string
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \Mezon\Service\ServiceSecurityProvider::getSessionIdFieldName()
     */
    public function getSessionIdFieldName(): string
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \Mezon\Service\ServiceSecurityProvider::getSelfId()
     */
    public function getSelfId(string $Token): int
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \Mezon\Service\ServiceSecurityProvider::loginAs()
     */
    public function loginAs(string $Token, string $LoginOrId, string $Field): string
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \Mezon\Service\ServiceSecurityProvider::createSession()
     */
    public function createSession(string $Token = ''): string
    {}

}

/**
 * Mock model
 *
 * @author Dodonov A.A.
 */
class MockModel extends \Mezon\Service\ServiceModel
{
}

/**
 * Base class for service logic unit tests.
 *
 * @author Dodonov A.A.
 */
class ServiceBaseLogicUnitTests extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing class name.
     *
     * @var string
     */
    protected $ClassName;

    /**
     * Constructor
     */
    public function __construct(string $ClassName = \Mezon\Service\ServiceBaseLogic::class)
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
        $this->assertInstanceOf(MockParamsFetcher::class, $Logic->getParamsFetcher(), $Msg);
        $this->assertInstanceOf(MockSecurityProvider::class, $Logic->getSecurityProvider(), $Msg);
        $this->assertInstanceOf(MockModel::class, $Logic->getModel(), $Msg);
    }

    /**
     * Testing connect method
     */
    public function testConstruct1(): void
    {
        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(), new MockSecurityProvider());

        $Msg = 'Construction failed for default model';

        $this->assertInstanceOf(MockParamsFetcher::class, $Logic->getParamsFetcher(), $Msg);
        $this->assertInstanceOf(MockSecurityProvider::class, $Logic->getSecurityProvider(), $Msg);
        $this->assertEquals(null, $Logic->getModel(), $Msg);
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

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(), new MockSecurityProvider(), MockModel::class);

        $Msg = 'Construction failed for defined model name';

        $this->checkLogicParts($Logic, $Msg);
    }
}
