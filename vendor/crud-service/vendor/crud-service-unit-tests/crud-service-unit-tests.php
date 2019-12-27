<?php
/**
 * Class CRUDServiceUnitTests
 *
 * @package     CRUDService
 * @subpackage  CRUDServiceUnitTests
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/13)
 * @copyright   Copyright (c) 2019, aeon.org
 */

define('GET_STRING', 1);
define('GET_OBJECT', 2);

/**
 * Fake security provider
 */
class FakeSecurityProvider
{
}

class CRUDServiceExceptionConstructorMock extends \Mezon\CRUDService
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
        throw (new Exception('Testing exception'));
    }
}

/**
 * Basic service's unit tests
 */
class CRUDServiceUnitTests extends PHPUnit\Framework\TestCase
{

    /**
     * Service class name
     *
     * @var string
     */
    var $ServiceClassName = \Mezon\CRUDService::class;

    /**
     * Constructor
     *
     * @param string $ServiceClassName
     *            - Class name to be tested
     */
    public function __construct(string $ServiceClassName = \Mezon\CRUDService::class)
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

        $Mock = $this->getMockBuilder(\Mezon\CRUDService\CRUDServiceLogic::class)
            ->setConstructorArgs([
                (new \Mezon\Service\ServiceConsoleTransport())->getParamsFetcher(),
            new FakeSecurityProvider(),
            new \Mezon\CRUDService\CRUDServiceModel()
        ])
            ->setMethods([
            $Method
        ])
            ->getMock();

        $Mock->expects($this->once())
            ->method($Method);

        $Service = new $this->ServiceClassName($this->getServiceSettings(), \Mezon\Service\ServiceConsoleTransport::class, \Mezon\Service\ServiceMockSecurityProvider::class, $Mock);

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
     * Testing CRUDService constructor
     */
    public function testServiceConstructor()
    {
        $Service = new \Mezon\CRUDService($this->getServiceSettings(), $this->getTransport());

        $this->assertInstanceOf(\Mezon\Service\ServiceMockSecurityProvider::class, $Service->ServiceTransport->SecurityProvider);
    }

    /**
     * Testing CRUDService constructor
     */
    public function testServiceConstructorWithSecurityProviderString()
    {
        $Service = new \Mezon\CRUDService($this->getServiceSettings(), $this->getTransport(), $TransportName = FakeSecurityProvider::class);

        $this->assertInstanceOf($TransportName, $Service->ServiceTransport->SecurityProvider);
    }

    /**
     * Testing CRUDService constructor
     */
    public function testServiceConstructorWithSecurityProviderObject()
    {
        // setup and test body
        $Service = new \Mezon\CRUDService($this->getServiceSettings(), $this->getTransport(), new FakeSecurityProvider());

        // assertions
        $this->assertInstanceOf('FakeSecurityProvider', $Service->ServiceTransport->SecurityProvider);
    }

    /**
     * Testing CRUDService constructor with exception
     */
    public function testServiceConstructorWithException()
    {
        // setup, test body and assertions
        try {
            new CRUDServiceExceptionConstructorMock();
            $this->fail();
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing CRUDService route processor
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
     * Testing CRUDService constructor
     */
    public function testMultipleModels()
    {
        // setup
        $Model = new \Mezon\CRUDService\CRUDServiceModel();

        $Transport = $this->getTransport(GET_OBJECT);

        $Logic1 = new \Mezon\CRUDService\CRUDServiceLogic($Transport->ParamsFetcher, new FakeSecurityProvider(), $Model);
        $Logic2 = new \Mezon\CRUDService\CRUDServiceLogic($Transport->ParamsFetcher, new FakeSecurityProvider(), $Model);

        // test body
        $Service = new \Mezon\CRUDService($this->getServiceSettings(), $this->getTransport(), new FakeSecurityProvider(), [
            $Logic1,
            $Logic2
        ]);

        // assertions
        $this->assertInstanceOf(\Mezon\CRUDService\CRUDServiceModel::class, $Service->ServiceLogic[0]->Model, 'Logic was not stored properly');
        $this->assertInstanceOf(\Mezon\CRUDService\CRUDServiceModel::class, $Service->ServiceLogic[1]->Model, 'Logic was not stored properly');
    }
}

?>