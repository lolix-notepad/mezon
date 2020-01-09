<?php
require_once (__DIR__ . '/../../../../../autoloader.php');

/**
 * Tests for the class ServiceTransport.
 */
class FakeService implements \Mezon\Service\ServiceBaseLogicInterface
{

    public function action_hello_world()
    {
        return (1);
    }
}

class ConcreteFetcher implements \Mezon\Service\ServiceRequestParams
{

    public function getParam($Param, $Default = false)
    {
        return (1);
    }
}

class ConcreteServiceTransport extends \Mezon\Service\ServiceTransport
{

    public function createFetcher(): \Mezon\Service\ServiceRequestParams
    {
        return (new ConcreteFetcher());
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
        parent::__construct(
            new \Mezon\Service\ServiceHttpTransport\HttpRequestParams($Router),
            new \Mezon\Service\ServiceMockSecurityProvider());
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
class ServiceTransportUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor.
     */
    public function testConstructor(): void
    {
        $ServiceTransport = new ConcreteServiceTransport();

        $this->assertInstanceOf(\Mezon\Router::class, $ServiceTransport->Router, 'Router was not created');
    }

    /**
     * Testing simple calling of the logic's method.
     */
    public function testGetServiceLogic(): void
    {
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->Router);
        $ServiceTransport->addRoute('test', 'test', 'GET');

        $Result = $ServiceTransport->Router->callRoute('test');

        $this->assertEquals('test', $Result, 'Invalid route execution result');
    }

    /**
     * Testing simple calling of the logic's method.
     */
    public function testGetServiceLogicPublic(): void
    {
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->Router);
        $ServiceTransport->addRoute('test', 'test', 'GET', 'public_call');

        $Result = $ServiceTransport->Router->callRoute('test');

        $this->assertEquals('test', $Result, 'Invalid public route execution result');
    }

    /**
     * Testing calling of the logic's method from array.
     */
    public function testGetServiceLogicFromArray(): void
    {
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = [
            new FakeServiceLogic($ServiceTransport->Router)
        ];
        $ServiceTransport->addRoute('test', 'test', 'GET');

        $Result = $ServiceTransport->Router->callRoute('test');

        $this->assertEquals('test', $Result, 'Invalid route execution result for multyple logics');
    }

    /**
     * Testing calling of the logic's method from array.
     */
    public function testGetServiceLogicWithUnexistingMethod(): void
    {
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->Router);

        try {
            $ServiceTransport->addRoute('unexisting', 'unexisting', 'GET');
            $this->fail('Exception must be thrown');
        } catch (Exception $e) {
            $this->assertEquals(
                - 1,
                $e->getCode(),
                'Illeagal error code was returned. Probably invalid exception was thrown.');
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing call stack formatter.
     */
    public function testFormatCallStack(): void
    {
        // setup
        $ServiceTransport = new ConcreteServiceTransport();
        $Exception = new Exception('Error message', - 1);

        // test body
        $Format = $ServiceTransport->errorResponse($Exception);

        // assertions
        $this->assertEquals(5, count($Format), 'Invalid formatter');
    }

    /**
     * Data provider
     *
     * @return string[][][] Data set
     */
    public function dataProviderForTestInvalidLoadRoute()
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
    public function testLadRoute(): void
    {
        // setup
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->Router);

        // test body
        $ServiceTransport->loadRoute([
            'route' => '/route/',
            'callback' => 'test'
        ]);

        // assertions
        $this->assertTrue(is_object($ServiceTransport->Router->getRoute('/route/')), 'Route does not exists');
    }

    /**
     * Testing 'loadRoute' method with unexisting logic
     *
     * @dataProvider dataProviderForTestInvalidLoadRoute
     */
    public function testInvalidLoadRoute(array $Route): void
    {
        // setup
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = null;

        // test body
        try {
            $ServiceTransport->loadRoute($Route);

            // assertions
            $this->fail('Exception must be thrown');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing load_routes method
     */
    public function testLoadRoutes(): void
    {
        // setup
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->Router);

        // test body
        $ServiceTransport->loadRoutes([
            [
                'route' => '/route/',
                'callback' => 'test'
            ]
        ]);

        // assertions
        $this->assertTrue(is_object($ServiceTransport->Router->getRoute('/route/')), 'Route does not exists');
    }

    /**
     * Testing fetchActions method
     */
    public function testFetchActions(): void
    {
        // setup
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->Router);

        // test body
        $ServiceTransport->fetchActions(new FakeService());

        // assertions
        $this->assertTrue(is_object($ServiceTransport->Router->getRoute('/hello-world/')), 'Route does not exists');
    }
}
