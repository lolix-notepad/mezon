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

    public function __construct()
    {
        parent::__construct([
            'fields' => '*',
            'table-name' => 'table',
            'entity-name' => 'entity'
        ]);
    }

    protected function initCommonRoutes(): void
    {
        throw (new \Exception('Testing exception'));
    }
}

/**
 * Basic service's unit tests
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
    public function __construct(string $ServiceClassName = \Mezon\CrudService::class)
    {
        parent::__construct();

        $this->ServiceClassName = $ServiceClassName;
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
        $Service = new \Mezon\CrudService($this->getServiceSettings(), $this->getTransport(), new FakeSecurityProviderForCrudService());

        // assertions
        $this->assertInstanceOf(FakeSecurityProviderForCrudService::class, $Service->getTransport()->SecurityProvider);
    }

    /**
     * Testing CrudService constructor with exception
     */
    public function testServiceConstructorWithException()
    {
        // setup, test body and assertions
        $this->expectException(\Exception::class);

        new CrudServiceExceptionConstructorMock();
    }

    /**
     * Testing CrudService route processor
     */
    public function testRoutes()
    {
        // test body and assertions
        $this->checkRoute('/list/', 'listRecord');

        $this->checkRoute('/all/', 'all');

        $this->checkRoute('/exact/list/[il:ids]/', 'exactList');

        $this->checkRoute('/exact/[i:id]/', 'exact');

        $this->checkRoute('/fields/', 'fields');

        $this->checkRoute('/delete/1/', 'deleteRecord');

        $this->checkRoute('/delete/', 'deleteFiltered', 'POST');

        $this->checkRoute('/create/', 'createRecord', 'POST');

        $this->checkRoute('/update/1/', 'updateRecord', 'POST');

        $this->checkRoute('/new/from/2019-01-01/', 'newRecordsSince');

        $this->checkRoute('/records/count/', 'recordsCount');

        $this->checkRoute('/last/10/', 'lastRecords');

        $this->checkRoute('/records/count/id/', 'recordsCountByField');
    }

    /**
     * Testing CrudService constructor
     */
    public function testMultipleModels()
    {
        // setup
        $Model = new \Mezon\CrudService\CrudServiceModel();

        $Transport = $this->getTransport(GET_OBJECT);

        $Logic1 = new \Mezon\CrudService\CrudServiceLogic($Transport->ParamsFetcher, new FakeSecurityProviderForCrudService(), $Model);
        $Logic2 = new \Mezon\CrudService\CrudServiceLogic($Transport->ParamsFetcher, new FakeSecurityProviderForCrudService(), $Model);

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
