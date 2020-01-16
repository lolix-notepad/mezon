<?php
namespace Mezon\Service\ServiceBaseLogic;

require_once ('autoload.php');

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
 * @group baseTests
 */
class MockParamsFetcher implements \Mezon\Service\ServiceRequestParamsInterface
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
        $this->assertInstanceOf(\Mezon\Service\ServiceMockSecurityProvider::class, $Logic->getSecurityProvider(), $Msg);
        $this->assertInstanceOf(MockModel::class, $Logic->getModel(), $Msg);
    }

    /**
     * Testing connect method
     */
    public function testConstruct1(): void
    {
        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(new MockParamsFetcher(), new \Mezon\Service\ServiceMockSecurityProvider());

        $Msg = 'Construction failed for default model';

        $this->assertInstanceOf(MockParamsFetcher::class, $Logic->getParamsFetcher(), $Msg);
        $this->assertInstanceOf(\Mezon\Service\ServiceMockSecurityProvider::class, $Logic->getSecurityProvider(), $Msg);
        $this->assertEquals(null, $Logic->getModel(), $Msg);
    }

    /**
     * Testing connect method
     */
    public function testConstruct2(): void
    {
        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(
            new MockParamsFetcher(),
            new \Mezon\Service\ServiceMockSecurityProvider(),
            new MockModel());

        $Msg = 'Construction failed for defined model object';

        $this->checkLogicParts($Logic, $Msg);
    }

    /**
     * Testing connect method
     */
    public function testConstruct3(): void
    {
        $ServiceLogicClassName = $this->ClassName;

        $Logic = new $ServiceLogicClassName(
            new MockParamsFetcher(),
            new \Mezon\Service\ServiceMockSecurityProvider(),
            MockModel::class);

        $Msg = 'Construction failed for defined model name';

        $this->checkLogicParts($Logic, $Msg);
    }
}
