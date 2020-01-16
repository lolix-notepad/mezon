<?php
require_once ('autoload.php');

/**
 * Tests for the class ServiceTransport.
 */
class FakeService implements \Mezon\Service\ServiceBaseLogicInterface
{

    public function action_hello_world()
    {
        return 1;
    }
}

class ConcreteFetcher implements \Mezon\Service\ServiceRequestParamsInterface
{

    public function getParam($Param, $Default = false)
    {
        return 1;
    }
}

class ConcreteServiceTransport extends \Mezon\Service\ServiceTransport
{

    public function createFetcher(): \Mezon\Service\ServiceRequestParamsInterface
    {
        return new ConcreteFetcher();
    }
}

/**
 * Fake service logic.
 *
 * @author Dodonov A.A.
 */
class FakeServiceLogic extends \Mezon\Service\ServiceLogic
{

    public function __construct(\Mezon\Router\Router &$Router)
    {
        parent::__construct(
            new \Mezon\Service\ServiceHttpTransport\HttpRequestParams($Router),
            new \Mezon\Service\ServiceMockSecurityProvider());
    }

    public function test()
    {
        return 'test';
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

        $this->assertInstanceOf(\Mezon\Router\Router::class, $ServiceTransport->getRouter(), 'Router was not created');
    }

    /**
     * Testing simple calling of the logic's method.
     */
    public function testGetServiceLogic(): void
    {
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->getRouter());
        $ServiceTransport->addRoute('test', 'test', 'GET');

        $Result = $ServiceTransport->getRouter()->callRoute('test');

        $this->assertEquals('test', $Result, 'Invalid route execution result');
    }

    /**
     * Testing simple calling of the logic's method.
     */
    public function testGetServiceLogicPublic(): void
    {
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->getRouter());
        $ServiceTransport->addRoute('test', 'test', 'GET', 'public_call');

        $Result = $ServiceTransport->getRouter()->callRoute('test');

        $this->assertEquals('test', $Result, 'Invalid public route execution result');
    }

    /**
     * Testing calling of the logic's method from array.
     */
    public function testGetServiceLogicFromArray(): void
    {
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = [
            new FakeServiceLogic($ServiceTransport->getRouter())
        ];
        $ServiceTransport->addRoute('test', 'test', 'GET');

        $_GET['r']='test';
        $_REQUEST['HTTP_METHOD']='GET';
        ob_start();
        $ServiceTransport->run();
        $Output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('test', $Output, 'Invalid route execution result for multyple logics');
    }

    /**
     * Testing calling of the logic's method from array.
     */
    public function testGetServiceLogicWithUnexistingMethod(): void
    {
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->getRouter());

        $this->expectException(Exception::class);
        $ServiceTransport->addRoute('unexisting', 'unexisting', 'GET');
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
        return [
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
        ];
    }

    /**
     * Testing 'load_route' method
     */
    public function testLadRoute(): void
    {
        // setup
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->getRouter());

        // test body
        $ServiceTransport->loadRoute([
            'route' => '/route/',
            'callback' => 'test'
        ]);

        // assertions
        $this->assertTrue($ServiceTransport->routeExists('/route/'), 'Route does not exists');
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
        $this->expectException(Exception::class);
        $ServiceTransport->loadRoute($Route);
    }

    /**
     * Testing load_routes method
     */
    public function testLoadRoutes(): void
    {
        // setup
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->getRouter());

        // test body
        $ServiceTransport->loadRoutes([
            [
                'route' => '/route/',
                'callback' => 'test'
            ]
        ]);

        // assertions
        $this->assertTrue($ServiceTransport->routeExists('/route/'), 'Route does not exists');
    }

    /**
     * Testing fetchActions method
     */
    public function testFetchActions(): void
    {
        // setup
        $ServiceTransport = new ConcreteServiceTransport();
        $ServiceTransport->ServiceLogic = new FakeServiceLogic($ServiceTransport->getRouter());

        // test body
        $ServiceTransport->fetchActions(new FakeService());

        // assertions
        $this->assertTrue($ServiceTransport->routeExists('/hello-world/'), 'Route does not exists');
    }
}
