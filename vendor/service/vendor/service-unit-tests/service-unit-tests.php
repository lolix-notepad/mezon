<?php
namespace Mezon\Service;

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

class FakeRequestParams implements \Mezon\Service\ServiceRequestParams
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
        return (false);
    }
}

/**
 * Common service unit tests
 *
 * @author Dodonov A.A.
 */
class ServiceUnitTests extends \PHPUnit\Framework\TestCase
{

    /**
     * Service class name
     *
     * @var string
     */
    var $ClassName = 'Mezon\Service';

    /**
     * Constructor
     *
     * @param string $ClassName
     *            - Class name to be tested
     */
    public function __construct(string $ClassName = '\Mezon\Service')
    {
        parent::__construct();

        $this->ClassName = $ClassName;
    }

    /**
     * Testing initialization of the security provider
     */
    public function testInitSecurityProviderDefault()
    {
        $Service = new $this->ClassName($this->getTransport(AS_STRING));
        $this->assertInstanceOf($this->getSecurityProvider(AS_STRING), $Service->ServiceTransport->SecurityProvider);

        $Service = new $this->ClassName(new \Mezon\Service\ServiceRestTransport(), $this->getSecurityProvider(AS_STRING));
        $this->assertInstanceOf($this->getSecurityProvider(AS_STRING), $Service->ServiceTransport->SecurityProvider);

        $Service = new $this->ClassName(new \Mezon\Service\ServiceRestTransport(), $this->getSecurityProvider(AS_OBJECT));
        $this->assertInstanceOf($this->getSecurityProvider(AS_STRING), $Service->ServiceTransport->SecurityProvider);
    }

    /**
     * Testing initialization of the service model
     */
    public function testInitServiceModel()
    {
        $Service = new $this->ClassName(new \Mezon\Service\ServiceRestTransport(), new \Mezon\Service\ServiceMockSecurityProvider(), $this->getLogic(AS_STRING));
        $this->assertInstanceOf($this->getModel(AS_STRING), $Service->ServiceLogic->Model);

        $Service = new $this->ClassName(new \Mezon\Service\ServiceRestTransport(), new \Mezon\Service\ServiceMockSecurityProvider(), $this->getLogic(AS_OBJECT), 'ServiceModel');
        $this->assertInstanceOf($this->getModel(AS_STRING), $Service->ServiceLogic->Model);

        $Service = new $this->ClassName(new \Mezon\Service\ServiceRestTransport(), new \Mezon\Service\ServiceMockSecurityProvider(), $this->getLogic(AS_OBJECT), new \Mezon\Service\ServiceModel());
        $this->assertInstanceOf($this->getModel(AS_STRING), $Service->ServiceLogic->Model);
    }

    /**
     * Method returns mock
     */
    protected function getMock()
    {
        $Mock = $this->getMockBuilder($this->ClassName)
            ->disableOriginalConstructor()
            ->setMethods([
            'run'
        ])
            ->getMock();

        return ($Mock);
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
            return ('\Mezon\Service\ServiceLogic');
        }
        if ($Mode == AS_OBJECT) {
            return (new \Mezon\Service\ServiceLogic(new FakeRequestParams(), new \stdClass(), $this->getModel(AS_OBJECT)));
        }
        return (null);
    }

    /**
     * Method creates model
     *
     * @param int $Mode
     *            - Creation mode
     * @return \Mezon\Service\ServiceModel|string Service model object
     */
    protected function getModel(int $Mode)
    {
        if ($Mode == AS_STRING) {
            return ('\Mezon\Service\ServiceModel');
        }
        if ($Mode == AS_OBJECT) {
            return (new \Mezon\Service\ServiceModel());
        }
        return (null);
    }

    /**
     * Method creates transport
     *
     * @param int $Mode
     *            - Creation mode
     * @return \Mezon\Service\ServiceTransport|string Service transport object
     */
    protected function getTransport(int $Mode)
    {
        if ($Mode == AS_STRING) {
            return ('\Mezon\Service\ServiceRestTransport');
        }
        if ($Mode == AS_OBJECT) {
            return (new \Mezon\Service\ServiceRestTransport());
        }
        return (null);
    }

    /**
     * Method creates security provider
     *
     * @param int $Mode
     *            - Creation mode
     * @return \Mezon\Service\ServiceSecurityProvider|string Service security provider object
     */
    protected function getSecurityProvider(int $Mode)
    {
        if ($Mode == AS_STRING) {
            return ('\Mezon\Service\ServiceMockSecurityProvider');
        }
        if ($Mode == AS_OBJECT) {
            return (new \Mezon\Service\ServiceMockSecurityProvider());
        }
        return (null);
    }

    /**
     * Testing launcher with transport
     *
     * @see \Mezon\Service::launch
     */
    public function testLaunchWithRransport()
    {
        $LocalClassName = $this->ClassName;
        $Mock = $this->getMock();

        // implicit
        $Service = $LocalClassName::launch(get_class($Mock));
        $this->assertInstanceOf($this->getTransport(AS_STRING), $Service->ServiceTransport);

        // explicit string
        $Service = $LocalClassName::launch(get_class($Mock), $this->getTransport(AS_STRING));
        $this->assertInstanceOf($this->getTransport(AS_STRING), $Service->ServiceTransport);

        // explicit object
        $Service = $LocalClassName::launch(get_class($Mock), $this->getTransport(AS_OBJECT));
        $this->assertInstanceOf($this->getTransport(AS_STRING), $Service->ServiceTransport);
    }

    /**
     * Testing launcher with security provider
     *
     * @see \Mezon\Service::launch
     */
    public function testLaunchWithSecurityProvider()
    {
        $LocalClassName = $this->ClassName;

        $Service = $LocalClassName::launch($this->ClassName, $this->getTransport(AS_STRING), $this->getSecurityProvider(AS_STRING), $this->getLogic(AS_STRING), $this->getModel(AS_STRING), false);

        $this->assertInstanceOf($this->getSecurityProvider(AS_STRING), $Service->ServiceTransport->SecurityProvider);
    }

    /**
     * Trying to construct service from array of logics
     */
    public function testCreateServiceLogicFromArray()
    {
        $LocalClassName = $this->ClassName;

        $Service = $LocalClassName::launch($this->ClassName, $this->getTransport(AS_STRING), $this->getSecurityProvider(AS_STRING), [
            $this->getLogic(AS_STRING)
        ], $this->getModel(AS_STRING), false);

        $this->assertTrue(is_array($Service->ServiceLogic), 'Array of logic objects was not created');
    }

    /**
     * Trying to run logic method from array
     */
    public function testServiceLogicFromArrayCanBeExecuted()
    {
        $LocalClassName = $this->ClassName;

        $_GET['r'] = 'connect';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $Service = $LocalClassName::launch($this->ClassName, '\Mezon\Service\ServiceConsoleTransport', $this->getSecurityProvider(AS_STRING), [
            $this->getLogic(AS_STRING)
        ], $this->getModel(AS_STRING), false);

        $Service->run();
        $this->addToAssertionCount(1);
    }

    /**
     * Testing launcher with transport
     *
     * @see \Mezon\Service::start
     */
    public function testStartWithTransport()
    {
        $LocalClassName = $this->ClassName;
        $Mock = $this->getMock();

        // implicit
        $Service = $LocalClassName::start(get_class($Mock));
        $this->assertInstanceOf($this->getTransport(AS_STRING), $Service->ServiceTransport);
    }
}

?>