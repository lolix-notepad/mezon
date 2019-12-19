<?php
/**
 * Tests for the class ServiceTransport.
 */
require_once (__DIR__ . '/../service-transport.php');
require_once (__DIR__ . '/../../service-base-logic-interface/service-base-logic-interface.php');
require_once (__DIR__ . '/../../service-http-transport/vendor/http-request-params/http-request-params.php');
require_once (__DIR__ . '/../../service-mock-security-provider/service-mock-security-provider.php');

class FakeService implements \Mezon\Service\ServiceBaseLogicInterface
{

    public function action_hello_world()
    {
        return (1);
    }
}

/**
 * Fake service logic.
 *
 * @author Dodonov A.A.
 */
class FakeServiceLogic extends \Mezon\Service\ServiceLogic
{

    public function __construct(\Mezon\Router &$Router)
    {
        parent::__construct(new \Mezon\Service\ServiceHTTPTransport\HTTPRequestParams($Router), new \Mezon\Service\ServiceMockSecurityProvider());
    }

    public function test()
    {
        return ('test');
    }
}

/**
 *
 * @author Dodonov A.A.
 */
class ServiceTransportUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor.
     */
    public function test_constructor(): void
    {
        $ServiceTransport = new \Mezon\Service\ServiceTransport();

        $this->assertInstanceOf('\Mezon\Router', $ServiceTransport->Router, 'Router was not created');
    }

    /**
     * Testing simple calling of the logic's method.
     */
    public function test_get_service_logic(): void
    {
        $ServiceTransport = new \Mezon\Service\ServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->Router);
        $ServiceTransport->add_route('test', 'test', 'GET');

        $Result = $ServiceTransport->Router->call_route('test');

        $this->assertEquals('test', $Result, 'Invalid route execution result');
    }

    /**
     * Testing simple calling of the logic's method.
     */
    public function test_get_service_logic_public(): void
    {
        $ServiceTransport = new \Mezon\Service\ServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->Router);
        $ServiceTransport->add_route('test', 'test', 'GET', 'public_call');

        $Result = $ServiceTransport->Router->call_route('test');

        $this->assertEquals('test', $Result, 'Invalid public route execution result');
    }

    /**
     * Testing calling of the logic's method from array.
     */
    public function test_get_service_logic_from_array(): void
    {
        $ServiceTransport = new \Mezon\Service\ServiceTransport();
        $ServiceTransport->ServiceLogic = [
            new FakeServiceLogic($ServiceTransport->Router)
        ];
        $ServiceTransport->add_route('test', 'test', 'GET');

        $Result = $ServiceTransport->Router->call_route('test');

        $this->assertEquals('test', $Result, 'Invalid route execution result for multyple logics');
    }

    /**
     * Testing calling of the logic's method from array.
     */
    public function test_get_service_logic_with_unexisting_method(): void
    {
        $ServiceTransport = new \Mezon\Service\ServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->Router);

        try {
            $ServiceTransport->add_route('unexisting', 'unexisting', 'GET');
            $this->fail('Exception must be thrown');
        } catch (Exception $e) {
            $this->assertEquals(- 1, $e->getCode(), 'Illeagal error code was returned. Probably invalid exception was thrown.');
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing call stack formatter.
     */
    public function test_format_call_stack(): void
    {
        // setup
        $ServiceTransport = new \Mezon\Service\ServiceTransport();
        $Exception = new Exception('Error message', - 1);

        // test body
        $Format = $ServiceTransport->error_response($Exception);

        // assertions
        $this->assertEquals(5, count($Format), 'Invalid formatter');
    }

    /**
     * Data provider
     *
     * @return string[][][] Data set
     */
    public function data_provider_for_test_invalid_load_route()
    {
        return ([
            [
                [
                    'route' => '/route/',
                    'callback' => 'test'
                ]
            ],
            [
                [
                    'route' => '/route/'
                ]
            ],
            [
                [
                    'callback' => 'test'
                ]
            ]
        ]);
    }

    /**
     * Testing 'load_route' method
     */
    public function test_load_route(): void
    {
        // setup
        $ServiceTransport = new \Mezon\Service\ServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->Router);

        // test body
        $ServiceTransport->load_route([
            'route' => '/route/',
            'callback' => 'test'
        ]);

        // assertions
        $this->assertTrue(is_object($ServiceTransport->Router->get_route('/route/')), 'Route does not exists');
    }

    /**
     * Testing 'load_route' method with unexisting logic
     *
     * @dataProvider data_provider_for_test_invalid_load_route
     */
    public function test_invalid_load_route(array $Route): void
    {
        // setup
        $ServiceTransport = new \Mezon\Service\ServiceTransport();
        $ServiceTransport->ServiceLogic = null;

        // test body
        try {
            $ServiceTransport->load_route($Route);

            // assertions
            $this->fail('Exception must be thrown');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing load_routes method
     */
    public function test_load_routes(): void
    {
        // setup
        $ServiceTransport = new \Mezon\Service\ServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->Router);

        // test body
        $ServiceTransport->load_routes([
            [
                'route' => '/route/',
                'callback' => 'test'
            ]
        ]);

        // assertions
        $this->assertTrue(is_object($ServiceTransport->Router->get_route('/route/')), 'Route does not exists');
    }

    /**
     * Testing fetch_actions method
     */
    public function test_fetch_actions(): void
    {
        // setup
        $ServiceTransport = new \Mezon\Service\ServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->Router);

        // test body
        $ServiceTransport->fetch_actions(new FakeService());

        // assertions
        $this->assertTrue(is_object($ServiceTransport->Router->get_route('/hello-world/')), 'Route does not exists');
    }
}

?>