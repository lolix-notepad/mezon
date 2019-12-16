<?php
/**
 * Class CRUDServiceClientUnitTests
 *
 * @package     CRUDServiceClient
 * @subpackage  CRUDServiceClientUnitTests
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/09/18)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../../../service/vendor/service-client/vendor/service-client-unit-tests/service-client-unit-tests.php');

require_once (__DIR__ . '/../../crud-service-client.php');

/**
 * Common unit tests for CRUDServiceClient and all derived client classes
 *
 * @author Dodonov A.A.
 */
class CRUDServiceClientUnitTests extends ServiceClientUnitTests
{

    /**
     * Getting mock object for CRUD service client
     *
     * @return object Mock object
     */
    protected function get_crud_service_client_mock()
    {
        $Mock = $this->getMockBuilder('CRUDServiceClient')
            ->setMethods([
            'get_request',
            'post_request'
        ])
            ->disableOriginalConstructor()
            ->getMock();

        return ($Mock);
    }

    /**
     * Method make full setup of the mock object
     *
     * @param string $ConfigName
     * @return object Mock object
     */
    protected function get_setup_mock_with_get_method(string $ConfigName)
    {
        $Mock = $this->get_crud_service_client_mock();

        $Mock->method('get_request')->willReturn(json_decode(file_get_contents(__DIR__ . '/conf/' . $ConfigName . '.json')));
        $Mock->method('post_request')->willReturn(json_decode(file_get_contents(__DIR__ . '/conf/' . $ConfigName . '.json')));

        return ($Mock);
    }

    /**
     * Testing 'get_compiled_filter' method
     */
    public function test_get_compiled_filter_1()
    {
        // setup
        $Client = $this->get_crud_service_client_mock();

        // test body
        $Result = $Client->get_compiled_filter(false);

        // assertions
        $this->assertEquals('', $Result, 'Empty string must be returned');
    }

    /**
     * Testing 'get_compiled_filter' method
     */
    public function test_get_compiled_filter_2()
    {
        // setup
        $Client = $this->get_crud_service_client_mock();

        // test body
        $Result = $Client->get_compiled_filter([
            'field1' => 1,
            'field2' => 2
        ], true);

        // assertions
        $this->assertStringContainsString('filter[field1]=1', $Result);
        $this->assertStringContainsString('filter[field2]=2', $Result);
    }

    /**
     * Testing 'get_compiled_filter' method
     */
    public function test_get_compiled_filter_3()
    {
        // setup
        $Client = $this->get_crud_service_client_mock();

        // test body
        $Result = $Client->get_compiled_filter([
            [
                'arg1' => '$id',
                'op' => '=',
                'arg2' => '1'
            ]
        ], true);

        // assertions
        $this->assertStringContainsString('&filter%5B0%5D%5Barg1%5D=%24id', $Result);
        $this->assertStringContainsString('&filter%5B0%5D%5Bop%5D=%3D', $Result);
        $this->assertStringContainsString('&filter%5B0%5D%5Barg2%5D=1', $Result);
    }

    /**
     * Testing 'get_by_ids_array' method
     */
    public function test_get_by_ids_array()
    {
        // setup
        $Client = $this->get_setup_mock_with_get_method('get-by-ids-array');

        // test body
        $ids = [
            1,
            2
        ];
        $Result = $Client->get_by_ids_array($ids); // compile
        $Result2 = $Client->get_by_ids_array($ids); // cache

        // assertions
        $this->assertEquals(2, count($Result));
        $this->assertEquals(2, count($Result2));
    }

    /**
     * Testing 'get_by_ids_array' method
     */
    public function test_get_by_ids_array_null()
    {
        // setup
        $Client = $this->get_setup_mock_with_get_method('get-by-ids-array');

        // test body
        $Result = $Client->get_by_ids_array([]);

        // assertions
        $this->assertEquals(0, count($Result));
    }

    /**
     * Testing 'records_count_by_field' method
     */
    public function test_records_count_by_field()
    {
        // setup
        $Client = $this->get_setup_mock_with_get_method('records-count-by-field');

        // test body
        $Result = $Client->records_count_by_field('id');
        $Result2 = $Client->records_count_by_field('id');

        // assertions
        $this->assertEquals(3, count($Result));
        $this->assertEquals(3, count($Result2));
    }

    /**
     * Testing instance method
     */
    public function test_instance()
    {
        // setup and test body
        $Client = CRUDServiceClient::instance('http://auth', 'token');

        // assertions
        $this->assertEquals('token', $Client->get_token());
    }

    /**
     * Testing 'get_list' method
     */
    public function test_get_list()
    {
        // setup
        $Client = $this->get_setup_mock_with_get_method('get-list');

        // test body
        $Result = $Client->get_list(0, 1, false, false, false);

        // assertions
        $this->assertEquals(2, count($Result));
    }

    /**
     * Testing 'get_list' method
     */
    public function test_get_list_order()
    {
        // setup
        $Client = $this->get_setup_mock_with_get_method('get-list');

        // test body
        $Result = $Client->get_list(0, 1, false, false, [
            'field' => 'id',
            'order' => 'ASC'
        ]);

        // assertions
        $this->assertEquals(2, count($Result));
    }

    /**
     * Testing 'create' method
     */
    public function test_create()
    {
        // setup
        $Client = $this->get_setup_mock_with_get_method('create');

        // test body
        $Result = $Client->create([
            'avatar' => [
                'name' => 'n',
                'size' => 's',
                'type' => 't',
                'tmp_name' => __FILE__
            ]
        ]);

        // assertions
        $this->assertEquals(1, $Result->id);
    }
}

?>