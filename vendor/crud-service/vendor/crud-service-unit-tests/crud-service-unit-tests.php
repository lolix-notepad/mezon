<?php
namespace Mezon\CrudService;

/**
 * Class CrudServiceUnitTests
 *
 * @package CrudService
 * @subpackage CrudServiceUnitTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/13)
 * @copyright Copyright (c) 2019, aeon.org
 */
define('GET_STRING', 1);
define('GET_OBJECT', 2);

/**
 * Fake security provider
 */
class FakeSecurityProviderForCrudService
{
}

class CrudServiceExceptionConstructorMock extends \Mezon\CrudService
{

    public function __construct(\Mezon\Service\ServiceTransport $Transport)
    {
        parent::__construct([
            'fields' => '*',
            'table-name' => 'table',
            'entity-name' => 'entity',
        ], $Transport);
    }

    protected function initCommonRoutes(): void
    {
        throw (new \Exception('Testing exception'));
    }
}

/**
 * Basic service's unit tests
 * 
 * @group baseTests
 */
class CrudServiceUnitTests extends \PHPUnit\Framework\TestCase
{

    /**
     * Service class name
     *
     * @var string
     */
    protected $ServiceClassName = \Mezon\CrudService::class;

    /**
     * Constructor
     *
     * @param string $ServiceClassName
     *            - Class name to be tested
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /**
     * Method returns service settings
     *
     * @return \Mezon\Service settings
     */
    protected function getServiceSettings(): array
    {
        return (json_decode(file_get_contents(__DIR__ . '/conf/setup.json'), true));
    }

    /**
     * Method checks route and method bindings
     *
     * @param string $Route
     *            - Route to be checked
     * @param string $Method
     *            - Method to be bound with route
     * @param string $RequestMethod
     *            - HTTP request method
     */
    protected function checkRoute(string $Route, string $Method, string $RequestMethod = 'GET')
    {
        $_GET['r'] = $Route;

        $Mock = $this->getMockBuilder(\Mezon\CrudService\CrudServiceLogic::class)
            ->setConstructorArgs(
            [
                (new \Mezon\Service\ServiceConsoleTransport())->getParamsFetcher(),
                new FakeSecurityProviderForCrudService(),
                new \Mezon\CrudService\CrudServiceModel()
            ])
            ->setMethods([
            $Method
        ])
            ->getMock();

        $Mock->expects($this->once())
            ->method($Method);

        $Service = new $this->ServiceClassName(
            $this->getServiceSettings(),
            \Mezon\Service\ServiceConsoleTransport::class,
            \Mezon\Service\ServiceMockSecurityProvider::class,
            $Mock);

        $_SERVER['REQUEST_METHOD'] = $RequestMethod;

        $Service->run();

        $this->addToAssertionCount(1);
    }

    /**
     * Method returns transport
     *
     * @param string $Type
     *            - Type of return value
     * @return string Transport
     */
    protected function getTransport(string $Type = GET_STRING)
    {
        if ($Type == GET_STRING) {
            return (\Mezon\Service\ServiceConsoleTransport::class);
        } else {
            return (new \Mezon\Service\ServiceConsoleTransport());
        }
    }

    /**
     * Testing CrudService constructor
     */
    public function testServiceConstructor()
    {
        $Service = new \Mezon\CrudService($this->getServiceSettings(), $this->getTransport());

        $this->assertInstanceOf(
            \Mezon\Service\ServiceMockSecurityProvider::class,
            $Service->getTransport()->SecurityProvider);
    }

    /**
     * Testing CrudService constructor
     */
    public function testServiceConstructorWithSecurityProviderString()
    {
        $Service = new \Mezon\CrudService(
            $this->getServiceSettings(),
            $this->getTransport(),
            FakeSecurityProviderForCrudService::class);

        $this->assertInstanceOf(FakeSecurityProviderForCrudService::class, $Service->getTransport()->SecurityProvider);
    }

    /**
     * Testing CrudService constructor
     */
    public function testServiceConstructorWithSecurityProviderObject()
    {
        // setup and test body
        $Service = new \Mezon\CrudService(
            $this->getServiceSettings(),
            $this->getTransport(),
            new FakeSecurityProviderForCrudService());

        // assertions
        $this->assertInstanceOf(FakeSecurityProviderForCrudService::class, $Service->getTransport()->SecurityProvider);
    }

    /**
     * Testing CrudService constructor with exception
     */
    public function testServiceConstructorWithException()
    {
        // setup, test body and assertions
        $Transport = $this->getMockBuilder(\Mezon\Service\ServiceConsoleTransport::class)
            ->setMethods([
            'handleException',
        ])
            ->getMock();

        $Transport->expects($this->once())
            ->method('handleException');

        new CrudServiceExceptionConstructorMock($Transport);
    }

    /**
     * Testing CrudService route processor
     *
     * @param string $Route
     *            Route
     * @param string $Handler
     *            Route handler
     * @param string $Method
     *            GET|POST
     * @dataProvider routesDataProvider
     */
    public function testRoutes(string $Route, string $Handler, string $Method)
    {
        // test body and assertions
        $this->checkRoute($Route, $Handler, $Method);
    }
    
    /**
     * Data provider for the test testRoutes
     *
     * @return array
     */
    public static function routesDataProvider(): array
    {
        return ([
            [
                '/list/',
                'listRecord',
                'GET',
            ],
            [
                '/all/',
                'all',
                'GET',
            ],
            [
                '/exact/list/[il:ids]/',
                'exactList',
                'GET',
            ],
            [
                '/exact/[i:id]/',
                'exact',
                'GET',
            ],
            [
                '/fields/',
                'fields',
                'GET',
            ],
            [
                '/delete/1/',
                'deleteRecord',
                'POST',
            ],
            [
                '/delete/',
                'deleteFiltered',
                'POST',
            ],
            [
                '/create/',
                'createRecord',
                'POST',
            ],
            [
                '/update/1/',
                'updateRecord',
                'POST',
            ],
            [
                '/new/from/2019-01-01/',
                'newRecordsSince',
                'GET',
            ],
            [
                '/records/count/',
                'recordsCount',
                'GET',
            ],
            [
                '/last/10/',
                'lastRecords',
                'GET',
            ],
            [
                '/records/count/id/',
                'recordsCountByField',
                'GET',
            ],
        ]);
    }

    /**
     * Testing CrudService constructor
     */
    public function testMultipleModels()
    {
        // setup
        $Model = new \Mezon\CrudService\CrudServiceModel();

        $Transport = $this->getTransport(GET_OBJECT);

        $Logic1 = new \Mezon\CrudService\CrudServiceLogic(
            $Transport->ParamsFetcher,
            new FakeSecurityProviderForCrudService(),
            $Model);
        $Logic2 = new \Mezon\CrudService\CrudServiceLogic(
            $Transport->ParamsFetcher,
            new FakeSecurityProviderForCrudService(),
            $Model);

        // test body
        $Service = new \Mezon\CrudService(
            $this->getServiceSettings(),
            $this->getTransport(),
            new FakeSecurityProviderForCrudService(),
            [
                $Logic1,
                $Logic2
            ]);

        // assertions
        $this->assertInstanceOf(
            \Mezon\CrudService\CrudServiceModel::class,
            $Service->getLogic()[0]->getModel(),
            'Logic was not stored properly');
        $this->assertInstanceOf(
            \Mezon\CrudService\CrudServiceModel::class,
            $Service->getLogic()[1]->getModel(),
            'Logic was not stored properly');
    }
}
