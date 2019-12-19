<?php
/**
 * Class ServiceUnitTests
 *
 * @package     Service
 * @subpackage  ServiceUnitTests
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/17)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../custom-client/custom-client.php');
require_once (__DIR__ . '/../../../router/router.php');

require_once (__DIR__ . '/../service-client/service-client.php');
require_once (__DIR__ . '/../service-console-transport/service-console-transport.php');
require_once (__DIR__ . '/../service-console-transport/vendor/console-request-params/console-request-params.php');
require_once (__DIR__ . '/../service-rest-transport/service-rest-transport.php');
require_once (__DIR__ . '/../service-http-transport/vendor/http-request-params/http-request-params.php');
require_once (__DIR__ . '/../service-security-provider/service-security-provider.php');
require_once (__DIR__ . '/../service-mock-security-provider/service-mock-security-provider.php');
require_once (__DIR__ . '/../service-model/service-model.php');

require_once (__DIR__ . '/../../service.php');

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
    public function get_param($Param, $Default = false)
    {
        return (false);
    }
}

/**
 * Common service unit tests
 *
 * @author Dodonov A.A.
 */
class ServiceUnitTests extends PHPUnit\Framework\TestCase
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
    public function test_init_security_provider_default()
    {
        $Service = new $this->ClassName($this->get_transport(AS_STRING));
        $this->assertInstanceOf($this->get_security_provider(AS_STRING), $Service->ServiceTransport->SecurityProvider);

        $Service = new $this->ClassName(new \Mezon\Service\ServiceRESTTransport(), $this->get_security_provider(AS_STRING));
        $this->assertInstanceOf($this->get_security_provider(AS_STRING), $Service->ServiceTransport->SecurityProvider);

        $Service = new $this->ClassName(new \Mezon\Service\ServiceRESTTransport(), $this->get_security_provider(AS_OBJECT));
        $this->assertInstanceOf($this->get_security_provider(AS_STRING), $Service->ServiceTransport->SecurityProvider);
    }

    /**
     * Testing initialization of the service model
     */
    public function test_init_service_model()
    {
        $Service = new $this->ClassName(new \Mezon\Service\ServiceRESTTransport(), new \Mezon\Service\ServiceMockSecurityProvider(), $this->get_logic(AS_STRING));
        $this->assertInstanceOf($this->get_model(AS_STRING), $Service->ServiceLogic->Model);

        $Service = new $this->ClassName(new \Mezon\Service\ServiceRESTTransport(), new \Mezon\Service\ServiceMockSecurityProvider(), $this->get_logic(AS_OBJECT), 'ServiceModel');
        $this->assertInstanceOf($this->get_model(AS_STRING), $Service->ServiceLogic->Model);

        $Service = new $this->ClassName(new \Mezon\Service\ServiceRESTTransport(), new \Mezon\Service\ServiceMockSecurityProvider(), $this->get_logic(AS_OBJECT), new \Mezon\Service\ServiceModel());
        $this->assertInstanceOf($this->get_model(AS_STRING), $Service->ServiceLogic->Model);
    }

    /**
     * Method returns mock
     */
    protected function get_mock()
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
    protected function get_logic(int $Mode)
    {
        if ($Mode == AS_STRING) {
            return ('\Mezon\Service\ServiceLogic');
        }
        if ($Mode == AS_OBJECT) {
            return (new \Mezon\Service\ServiceLogic(new FakeRequestParams(), new stdClass(), $this->get_model(AS_OBJECT)));
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
    protected function get_model(int $Mode)
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
    protected function get_transport(int $Mode)
    {
        if ($Mode == AS_STRING) {
            return ('\Mezon\Service\ServiceRESTTransport');
        }
        if ($Mode == AS_OBJECT) {
            return (new \Mezon\Service\ServiceRESTTransport());
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
    protected function get_security_provider(int $Mode)
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
     * @see Mezon\Service::launch
     */
    public function test_launch_with_transport()
    {
        $LocalClassName = $this->ClassName;
        $Mock = $this->get_mock();

        // implicit
        $Service = $LocalClassName::launch(get_class($Mock));
        $this->assertInstanceOf($this->get_transport(AS_STRING), $Service->ServiceTransport);

        // explicit string
        $Service = $LocalClassName::launch(get_class($Mock), $this->get_transport(AS_STRING));
        $this->assertInstanceOf($this->get_transport(AS_STRING), $Service->ServiceTransport);

        // explicit object
        $Service = $LocalClassName::launch(get_class($Mock), $this->get_transport(AS_OBJECT));
        $this->assertInstanceOf($this->get_transport(AS_STRING), $Service->ServiceTransport);
    }

    /**
     * Testing launcher with security provider
     *
     * @see Mezon\Service::launch
     */
    public function test_launch_with_security_provider()
    {
        $LocalClassName = $this->ClassName;

        $Service = $LocalClassName::launch($this->ClassName, $this->get_transport(AS_STRING), $this->get_security_provider(AS_STRING), $this->get_logic(AS_STRING), $this->get_model(AS_STRING), false);

        $this->assertInstanceOf($this->get_security_provider(AS_STRING), $Service->ServiceTransport->SecurityProvider);
    }

    /**
     * Trying to construct service from array of logics
     */
    public function test_create_service_logic_from_array()
    {
        $LocalClassName = $this->ClassName;

        $Service = $LocalClassName::launch($this->ClassName, $this->get_transport(AS_STRING), $this->get_security_provider(AS_STRING), [
            $this->get_logic(AS_STRING)
        ], $this->get_model(AS_STRING), false);

        $this->assertTrue(is_array($Service->ServiceLogic), 'Array of logic objects was not created');
    }

    /**
     * Trying to run logic method from array
     */
    public function test_service_logic_from_array_can_be_executed()
    {
        $LocalClassName = $this->ClassName;

        $_GET['r'] = 'connect';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $Service = $LocalClassName::launch($this->ClassName, '\Mezon\Service\ServiceConsoleTransport', $this->get_security_provider(AS_STRING), [
            $this->get_logic(AS_STRING)
        ], $this->get_model(AS_STRING), false);

        $Service->run();
        $this->addToAssertionCount(1);
    }

    /**
     * Testing launcher with transport
     *
     * @see Mezon\Service::start
     */
    public function test_start_with_transport()
    {
        $LocalClassName = $this->ClassName;
        $Mock = $this->get_mock();

        // implicit
        $Service = $LocalClassName::start(get_class($Mock));
        $this->assertInstanceOf($this->get_transport(AS_STRING), $Service->ServiceTransport);
    }
}

?>