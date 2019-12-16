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
require_once (__DIR__ . '/../../../custom-client/custom-client.php');
require_once (__DIR__ . '/../../../router/router.php');
require_once (__DIR__ . '/../../../service/vendor/service-client/service-client.php');
require_once (__DIR__ . '/../../../service/vendor/service-rest-transport/service-rest-transport.php');
require_once (__DIR__ . '/../../../service/vendor/service-console-transport/service-console-transport.php');
require_once (__DIR__ . '/../../../service/vendor/service-console-transport/vendor/console-request-params/console-request-params.php');
require_once (__DIR__ . '/../../../service/vendor/service-security-provider/service-security-provider.php');
require_once (__DIR__ . '/../../../service/vendor/service-logic/service-logic.php');
require_once (__DIR__ . '/../../../service/vendor/service-mock-security-provider/service-mock-security-provider.php');
require_once (__DIR__ . '/../../../service/service.php');

require_once (__DIR__ . '/../crud-service-model/crud-service-model.php');
require_once (__DIR__ . '/../crud-service-logic/crud-service-logic.php');
require_once (__DIR__ . '/../../crud-service.php');

define('GET_STRING', 1);
define('GET_OBJECT', 2);

/**
 * Fake security provider
 */
class FakeSecurityProvider
{
}

class CRUDServiceExceptionConstructorMock extends CRUDService
{

    public function __construct()
    {
        parent::__construct([
            'fields' => '*',
            'table-name' => 'table',
            'entity-name' => 'entity'
        ]);
    }

    protected function init_common_routes(): void
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
    var $ServiceClassName = 'CRUDService';

    /**
     * Constructor
     *
     * @param string $ServiceClassName
     *            - Class name to be tested
     */
    public function __construct(string $ServiceClassName = 'CRUDService')
    {
        parent::__construct();

        $this->ServiceClassName = $ServiceClassName;
    }

    /**
     * Method returns service settings
     *
     * @return Service settings
     */
    protected function get_service_settings(): array
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
    protected function check_route(string $Route, string $Method, string $RequestMethod = 'GET')
    {
        $_GET['r'] = $Route;

        $Mock = $this->getMockBuilder('CRUDServiceLogic')
            ->setConstructorArgs([
            (new ServiceConsoleTransport())->get_params_fetcher(),
            new FakeSecurityProvider(),
            new CRUDServiceModel()
        ])
            ->setMethods([
            $Method
        ])
            ->getMock();

        $Mock->expects($this->once())
            ->method($Method);

        $Service = new $this->ServiceClassName($this->get_service_settings(), 'ServiceConsoleTransport', 'ServiceMockSecurityProvider', $Mock);

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
    protected function get_transport(string $Type = GET_STRING)
    {
        if ($Type == GET_STRING) {
            return ('ServiceConsoleTransport');
        } else {
            return (new ServiceConsoleTransport());
        }
    }

    /**
     * Testing CRUDService constructor
     */
    public function test_service_constructor()
    {
        $Service = new CRUDService($this->get_service_settings(), $this->get_transport());

        $this->assertInstanceOf('ServiceMockSecurityProvider', $Service->ServiceTransport->SecurityProvider);
    }

    /**
     * Testing CRUDService constructor
     */
    public function test_service_constructor_with_security_provider_string()
    {
        $Service = new CRUDService($this->get_service_settings(), $this->get_transport(), $TransportName = 'FakeSecurityProvider');

        $this->assertInstanceOf($TransportName, $Service->ServiceTransport->SecurityProvider);
    }

    /**
     * Testing CRUDService constructor
     */
    public function test_service_constructor_with_security_provider_object()
    {
        // setup and test body
        $Service = new CRUDService($this->get_service_settings(), $this->get_transport(), new FakeSecurityProvider());

        // assertions
        $this->assertInstanceOf('FakeSecurityProvider', $Service->ServiceTransport->SecurityProvider);
    }

    /**
     * Testing CRUDService constructor with exception
     */
    public function test_service_constructor_with_exception()
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
    public function test_routes()
    {
        // test body and assertions
        $this->check_route('/list/', 'list_record');

        $this->check_route('/all/', 'all');

        $this->check_route('/exact/list/[il:ids]/', 'exact_list');

        $this->check_route('/exact/[i:id]/', 'exact');

        $this->check_route('/fields/', 'fields');

        $this->check_route('/delete/1/', 'delete_record');

        $this->check_route('/delete/', 'delete_filtered', 'POST');

        $this->check_route('/create/', 'create_record', 'POST');

        $this->check_route('/update/1/', 'update_record', 'POST');

        $this->check_route('/new/from/2019-01-01/', 'new_records_since');

        $this->check_route('/records/count/', 'records_count');

        $this->check_route('/last/10/', 'last_records');

        $this->check_route('/records/count/id/', 'records_count_by_field');
    }

    /**
     * Testing CRUDService constructor
     */
    public function test_multiple_models()
    {
        // setup
        $Model = new CRUDServiceModel();

        $Transport = $this->get_transport(GET_OBJECT);

        $Logic1 = new CRUDServiceLogic($Transport->ParamsFetcher, new FakeSecurityProvider(), $Model);
        $Logic2 = new CRUDServiceLogic($Transport->ParamsFetcher, new FakeSecurityProvider(), $Model);

        // test body
        $Service = new CRUDService($this->get_service_settings(), $this->get_transport(), new FakeSecurityProvider(), [
            $Logic1,
            $Logic2
        ]);

        // assertions
        $this->assertInstanceOf('CRUDServiceModel', $Service->ServiceLogic[0]->Model, 'Logic was not stored properly');
        $this->assertInstanceOf('CRUDServiceModel', $Service->ServiceLogic[1]->Model, 'Logic was not stored properly');
    }
}

?>