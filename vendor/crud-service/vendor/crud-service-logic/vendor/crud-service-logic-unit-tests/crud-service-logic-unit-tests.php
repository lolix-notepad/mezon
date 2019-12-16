<?php
/**
 * Class CRUDServiceLogicUnitTests
 *
 * @package     CRUDServiceLogic
 * @subpackage  CRUDServiceLogicUnitTests
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/17)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../../../gui/vendor/fields-algorithms/vendor/filter/filter.php');
require_once (__DIR__ . '/../../../../../gui/vendor/fields-algorithms/fields-algorithms.php');
require_once (__DIR__ . '/../../../../../gui/vendor/form-builder/form-builder.php');

require_once (__DIR__ . '/../../../crud-service-model/crud-service-model.php');
require_once (__DIR__ . '/../../crud-service-logic.php');

/**
 * Fake securoity provider
 */
class FakeSecurityProvider
{
}

/**
 * Fake patrameters fetched
 */
class FakeParametersFetcher implements ServiceRequestParams
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
 * Fake service model
 */
class FakeServiceModel extends CRUDServiceModel
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct([
            'id' => [
                'type' => 'integer'
            ]
        ], 'record');
    }

    /**
     * Method returns amount of records in table, grouped by the specified field
     *
     * @param integer|bool $DomainId
     *            Domain id
     * @param string $FieldName
     *            Grouping field
     * @param array $Where
     *            Filtration conditions
     * @return array Records with stat
     */
    public function records_count_by_field($DomainId, string $FieldName, array $Where): array
    {
        return ([
            [
                'id' => 1,
                'records_count' => 1
            ],
            [
                'id' => 2,
                'records_count' => 2
            ]
        ]);
    }

    /**
     * Method returns last $Count records
     *
     * @param integer|boolean $DomainId
     *            Id of the domain
     * @param integer $Count
     *            Amount of records to be returned
     * @param array $Where
     *            Filter conditions
     * @return array List of the last $Count records
     */
    public function last_records($DomainId, $Count, $Where)
    {
        return ([
            []
        ]);
    }
}

/**
 * Common CRUDServiceLogic unit tests
 */
class CRUDServiceLogicUnitTests extends ServiceLogicUnitTests
{

    /**
     * Method returns service model
     *
     * @param array $Methods
     *            Methods to be mocked
     * @return object Service model
     */
    protected function get_service_model_mock(array $Methods = [])
    {
        $Model = $this->getMockBuilder('CRUDServiceModel')
            ->setConstructorArgs([
            [
                'id' => [
                    'type' => 'integer'
                ],
                'domain_id' => [
                    'type' => 'integer'
                ],
                'creation_date' => [
                    'type' => 'date'
                ]
            ],
            'record'
        ])
            ->setMethods($Methods)
            ->getMock();

        return ($Model);
    }

    /**
     * Returning json file content
     *
     * @param string $FileName
     *            File name
     * @return array json decoded countent of the file
     */
    protected function json_data(string $FileName): array
    {
        return (json_decode(file_get_contents(__DIR__ . '/conf/' . $FileName . '.json'), true));
    }

    /**
     * Method creates full functional CRUDServiceLogic object
     *
     * @param mixed $Model
     *            List of models or single model
     * @return CRUDServiceLogic object
     */
    protected function get_service_logic($Model): CRUDServiceLogic
    {
        $Transport = new ServiceConsoleTransport();

        $Logic = new CRUDServiceLogic($Transport->ParamsFetcher, new ServiceMockSecurityProvider(), $Model);

        return ($Logic);
    }

    /**
     * Method creates service logic for list methods testing
     */
    protected function setup_logic_for_list_methods_testing()
    {
        $Connection = $this->getMockBuilder('PDOCRUD')
            ->disableOriginalConstructor()
            ->setMethods([
            'select'
        ])
            ->getMock();
        $Connection->method('select')->willReturn([
            [
                'field_name' => 'balance',
                'field_value' => 100
            ]
        ]);

        $ServiceModel = $this->get_service_model_mock([
            'get_simple_records',
            'get_connection'
        ]);
        $ServiceModel->method('get_simple_records')->willReturn($this->json_data('get-simple-records'));
        $ServiceModel->method('get_connection')->willReturn($Connection);

        $ServiceLogic = $this->get_service_logic($ServiceModel);

        return ($ServiceLogic);
    }

    /**
     * Testing getting amount of records
     */
    public function test_records_count1()
    {
        // setup
        $ServiceModel = $this->get_service_model_mock();
        $ServiceModel->method('records_count')->willReturn(1);

        $ServiceLogic = $this->get_service_logic($ServiceModel);

        // test body
        $Count = $ServiceLogic->records_count();

        // assertions
        $this->assertEquals(1, $Count, 'Records count was not fetched');
    }

    /**
     * Testing getting amount of records
     */
    public function test_records_count0()
    {
        // setup
        $ServiceModel = $this->get_service_model_mock();
        $ServiceModel->method('records_count')->willReturn(0);

        $ServiceLogic = $this->get_service_logic($ServiceModel);

        // test body
        $Count = $ServiceLogic->records_count();

        // assertions
        $this->assertEquals(0, $Count, 'Records count was not fetched');
    }

    /**
     * Method tests last N records returning
     */
    public function test_last_records()
    {
        // setup
        $ServiceModel = new FakeServiceModel();

        $ServiceLogic = $this->get_service_logic($ServiceModel);

        // test body
        $Records = $ServiceLogic->last_records(1);

        // assertions
        $this->assertEquals(1, count($Records), 'Invalid amount of records was returned');
    }

    /**
     * Testing getting amount of records
     */
    public function test_records_count_by_existing_field()
    {
        // setup
        $ServiceModel = new FakeServiceModel();

        $ServiceLogic = $this->get_service_logic($ServiceModel);

        global $argv;
        $argv['field'] = 'id';

        // test body
        $Counters = $ServiceLogic->records_count_by_field();

        // assertions
        $this->assertEquals(2, count($Counters), 'Records were not fetched. Params:  ' . serialize($argv));
        $this->assertEquals(1, $Counters[0]['records_count'], 'Records were not counted');
        $this->assertEquals(2, $Counters[1]['records_count'], 'Records were not counted');
    }

    /**
     * Testing getting amount of records.
     */
    public function test_records_count_by_not_existing_field()
    {
        // setup
        $ServiceModel = new FakeServiceModel();

        $ServiceLogic = $this->get_service_logic($ServiceModel);

        global $argv;
        $argv['field'] = 'unexisting';

        // test body and assertions
        try {
            $ServiceLogic->records_count_by_field();

            $this->fail('Exception must be thrown, but it was not ' . serialize($argv));
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing constructor.
     */
    public function test_construct()
    {
        $ServiceLogic = new CRUDServiceLogic(new FakeParametersFetcher(), new FakeSecurityProvider());

        $this->assertInstanceOf('FakeParametersFetcher', $ServiceLogic->ParamsFetcher);
        $this->assertInstanceOf('FakeSecurityProvider', $ServiceLogic->SecurityProvider);
    }

    /**
     * Testing records list generation
     */
    public function test_list_record()
    {
        // setup
        $ServiceLogic = $this->setup_logic_for_list_methods_testing();

        // test body
        $RecordsList = $ServiceLogic->list_record();

        // assertions
        $this->assertEquals(2, count($RecordsList), 'Invalid records list was fetched');
    }

    /**
     * Testing domain_id fetching
     */
    public function test_get_domain_id_cross_domain_disabled()
    {
        // setup
        $ServiceModel = $this->get_service_model_mock([
            'get_connection'
        ]);

        $ServiceLogic = $this->get_service_logic($ServiceModel);

        unset($_GET['cross_domain']);

        // test body
        $Result = $ServiceLogic->get_domain_id();

        // assertions
        $this->assertEquals(1, $Result, 'Invalid get_domain_id result. Must be 1');
    }

    /**
     * Testing domain_id fetching
     */
    public function test_get_domain_id_cross_domain_enabled()
    {
        // setup
        $ServiceModel = $this->get_service_model_mock();

        $ServiceLogic = $this->get_service_logic($ServiceModel);

        $_GET['cross_domain'] = 1;

        // test
        $Result = $ServiceLogic->get_domain_id();

        $this->assertEquals(false, $Result, 'Invalid get_domain_id result. Must be false');
    }

    /**
     * Testing new_records_since method for invalid
     */
    public function test_new_records_since_invalid()
    {
        // setup
        $ServiceModel = $this->get_service_model_mock();

        $ServiceLogic = $this->get_service_logic($ServiceModel);

        // test body
        try {
            $ServiceLogic->new_records_since();
            $this->fail('Exception must be thrown');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing new_records_since method
     */
    public function test_new_records_since()
    {
        // setup
        $ServiceModel = $this->get_service_model_mock([
            'new_records_since'
        ]);
        $ServiceModel->method('new_records_since')->willReturn([
            []
        ]);

        $ServiceLogic = $this->get_service_logic($ServiceModel);

        // test body
        $Result = $ServiceLogic->new_records_since();

        // assertions
        $this->assertCount(1, $Result);
    }

    /**
     * Testing 'update_record' method
     */
    public function test_update_record()
    {
        // setup
        $FieldName = 'record-title';
        $ServiceModel = $this->get_service_model_mock([
            'update_basic_fields',
            'set_field_for_object'
        ]);
        $ServiceModel->method('update_basic_fields')->willReturn([
            $FieldName => 'Record title'
        ]);

        $ServiceLogic = $this->get_service_logic($ServiceModel);

        global $argv;
        $argv[$FieldName] = 'Some title';
        $argv['custom_fields']['record-balance'] = 123;

        // test body
        $Record = $ServiceLogic->update_record();

        // assertions
        $this->assertEquals('Record title', $Record[$FieldName], 'Invalid update result' . serialize($argv));
        $this->assertEquals(123, $Record['custom_fields']['record-balance'], 'Invalid update result' . serialize($argv));
        $this->assertTrue(isset($Record['id']), 'Id was not returned' . serialize($argv));
    }

    /**
     * Method tests filtered deletion
     */
    public function test_delete_filtered()
    {
        // setup
        $ServiceModel = $this->get_service_model_mock();
        $ServiceModel->expects($this->once())
            ->method('delete_filtered');

        $Mock = $this->get_service_logic($ServiceModel);

        // test body and assertions
        $Mock->delete_filtered();
    }

    /**
     * Method tests deletion
     */
    public function test_delete_record()
    {
        // setup
        $ServiceModel = $this->get_service_model_mock();
        $ServiceModel->expects($this->once())
            ->method('delete_filtered');

        $Mock = $this->get_service_logic($ServiceModel);

        // test body and assertions
        $Mock->delete_record();
    }

    /**
     * Testing all records generation
     */
    public function test_all()
    {
        // setup
        $ServiceLogic = $this->setup_logic_for_list_methods_testing();

        // test body
        $RecordsList = $ServiceLogic->all();

        // assertions
        $this->assertEquals(2, count($RecordsList), 'Invalid records list was fetched');
    }

    /**
     * Method tests creation
     */
    public function test_create_record()
    {
        // setup
        $ServiceModel = $this->get_service_model_mock();
        $ServiceModel->expects($this->once())
            ->method('insert_basic_fields');

        $Mock = $this->get_service_logic($ServiceModel);

        // test body and assertions
        $Mock->create_record();
    }
}

?>