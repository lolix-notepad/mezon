<?php
namespace Mezon\Service\Tests;

require_once ('autoload.php');
/**
 * Class ServiceUnitTests
 *
 * @package Service
 * @subpackage ServiceUnitTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */
define('AS_STRING', 1);
define('AS_OBJECT', 2);

class FakeRequestParams implements \Mezon\Service\ServiceRequestParamsInterface
{

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
        return false;
    }
}

/**
 * Common service unit tests
 *
 * @author Dodonov A.A.
 * @group baseTests
 */
class ServiceUnitTests extends \PHPUnit\Framework\TestCase
{

    /**
     * Service class name
     *
     * @var string
     */
    protected $ClassName = \Mezon\Service\Service::class;

    /**
     * Constructor
     *
     * @param string $ClassName
     *            - Class name to be tested
     */
    public function __construct(string $ClassName = \Mezon\Service\Service::class)
    {
        parent::__construct();

        $this->ClassName = $ClassName;
    }

    /**
     * Testing initialization of the security provider
     */
    public function testInitSecurityProviderDefault()
    {
        $Service = new $this->ClassName(\Mezon\Service\ServiceRestTransport\ServiceRestTransport::class);
        $this->assertInstanceOf($this->getSecurityProvider(AS_STRING), $Service->getTransport()->SecurityProvider);

        $Service = new $this->ClassName(
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport(),
            $this->getSecurityProvider(AS_STRING));
        $this->assertInstanceOf($this->getSecurityProvider(AS_STRING), $Service->getTransport()->SecurityProvider);

        $Service = new $this->ClassName(
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport(),
            $this->getSecurityProvider(AS_OBJECT));
        $this->assertInstanceOf($this->getSecurityProvider(AS_STRING), $Service->getTransport()->SecurityProvider);
    }

    /**
     * Testing initialization of the service model
     */
    public function testInitServiceModel()
    {
        $Service = new $this->ClassName(
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport(),
            new \Mezon\Service\ServiceMockSecurityProvider(),
            $this->getLogic(AS_STRING));
        $this->assertInstanceOf(\Mezon\Service\ServiceModel::class, $Service->getLogic()
            ->getModel());

        $Service = new $this->ClassName(
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport(),
            new \Mezon\Service\ServiceMockSecurityProvider(),
            $this->getLogic(AS_OBJECT),
            'ServiceModel');
        $this->assertInstanceOf(\Mezon\Service\ServiceModel::class, $Service->getLogic()
            ->getModel());

        $Service = new $this->ClassName(
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport(),
            new \Mezon\Service\ServiceMockSecurityProvider(),
            $this->getLogic(AS_OBJECT),
            new \Mezon\Service\ServiceModel());
        $this->assertInstanceOf(\Mezon\Service\ServiceModel::class, $Service->getLogic()
            ->getModel());
    }

    /**
     * Method returns mock
     *
     * @return object Mock of the testing class
     */
    protected function getMock(): object
    {
        $Mock = $this->getMockBuilder($this->ClassName)
            ->disableOriginalConstructor()
            ->setMethods([
            'run'
        ])
            ->getMock();

        return $Mock;
    }

    /**
     * Method creates logic
     *
     * @param int $Mode
     *            - Creation mode
     * @return \Mezon\Service\ServiceLogic|string Service logic object
     */
    protected function getLogic(int $Mode)
    {
        if ($Mode == AS_STRING) {
            return \Mezon\Service\ServiceLogic::class;
        }
        if ($Mode == AS_OBJECT) {
            return new \Mezon\Service\ServiceLogic(
                new FakeRequestParams(),
                new \stdClass(),
                new \Mezon\Service\ServiceModel());
        }
        return null;
    }

    /**
     * Method creates security provider
     *
     * @param int $Mode
     *            - Creation mode
     * @return \Mezon\Service\ServiceSecurityProviderInterface|string Service security provider object
     */
    protected function getSecurityProvider(int $Mode)
    {
        if ($Mode == AS_STRING) {
            return \Mezon\Service\ServiceMockSecurityProvider::class;
        }
        if ($Mode == AS_OBJECT) {
            return new \Mezon\Service\ServiceMockSecurityProvider();
        }
        return null;
    }

    /**
     * Testing launcher with transport
     *
     * @see \Mezon\Service\Service::launch
     */
    public function testLaunchWithRransport()
    {
        $LocalClassName = $this->ClassName;
        $Mock = $this->getMock();

        // implicit
        $Service = $LocalClassName::launch(get_class($Mock));
        $this->assertInstanceOf(
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $Service->getTransport());

        // explicit string
        $Service = $LocalClassName::launch(
            get_class($Mock),
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class);
        $this->assertInstanceOf(
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $Service->getTransport());

        // explicit object
        $Service = $LocalClassName::launch(
            get_class($Mock),
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport());
        $this->assertInstanceOf(
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $Service->getTransport());
    }

    /**
     * Testing launcher with security provider
     *
     * @see \Mezon\Service\Service::launch
     */
    public function testLaunchWithSecurityProvider()
    {
        $LocalClassName = $this->ClassName;

        $Service = $LocalClassName::launch(
            $this->ClassName,
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $this->getSecurityProvider(AS_STRING),
            $this->getLogic(AS_STRING),
            \Mezon\Service\ServiceModel::class,
            false);

        $this->assertInstanceOf($this->getSecurityProvider(AS_STRING), $Service->getTransport()->SecurityProvider);
    }

    /**
     * Trying to construct service from array of logics
     */
    public function testCreateServiceLogicFromArray()
    {
        $LocalClassName = $this->ClassName;

        $Service = $LocalClassName::launch(
            $this->ClassName,
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $this->getSecurityProvider(AS_STRING),
            [
                $this->getLogic(AS_STRING)
            ],
            \Mezon\Service\ServiceModel::class,
            false);

        $this->assertTrue(is_array($Service->getLogic()), 'Array of logic objects was not created');
    }

    /**
     * Trying to run logic method from array
     */
    public function testServiceLogicFromArrayCanBeExecuted()
    {
        $LocalClassName = $this->ClassName;

        $_GET['r'] = 'connect';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $Service = $LocalClassName::launch(
            $this->ClassName,
            \Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport::class,
            $this->getSecurityProvider(AS_STRING),
            [
                $this->getLogic(AS_STRING)
            ],
            \Mezon\Service\ServiceModel::class,
            false);

        $Service->run();
        $this->addToAssertionCount(1);
    }

    /**
     * Testing launcher with transport
     *
     * @see \Mezon\Service\Service::start
     */
    public function testStartWithTransport()
    {
        $LocalClassName = $this->ClassName;
        $Mock = $this->getMock();

        // implicit
        $Service = $LocalClassName::start(get_class($Mock));
        $this->assertInstanceOf(
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $Service->getTransport());
    }
}
