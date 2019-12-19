<?php
require_once (__DIR__ . '/../../../../crud-service/vendor/crud-service-logic/crud-service-logic.php');
require_once (__DIR__ . '/../../fields-algorithms/fields-algorithms.php');
require_once (__DIR__ . '/../../form-builder/form-builder.php');

require_once (__DIR__ . '/../vendor/list-builder-adapter/list-builder-adapter.php');
require_once (__DIR__ . '/../list-builder.php');

class FakeAdapter implements \Mezon\GUI\ListBuilder\ListBuilderAdapter
{

    /**
     * Method returns all vailable records
     *
     * @return array all vailable records
     */
    public function all(): array
    {
        return ([
            [
                'id' => 1
            ],
            [
                'id' => 2
            ]
        ]);
    }

    /**
     * Method returns a subset from vailable records
     *
     * @param array $Order
     *            order settings
     * @param int $From
     *            the beginning of the bunch
     * @param int $Limit
     *            the size of the batch
     * @return array subset from vailable records
     */
    public function get_records(array $Order, int $From, int $Limit): array
    {
        return ($this->all());
    }

    /**
     * Record preprocessor
     *
     * @param array $Record
     *            record to be preprocessed
     * @return array preprocessed record
     */
    public function preprocess_list_item(array $Record): array
    {
        return ($Record);
    }
}

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

class ListBuilderUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     * Method returns list of fields
     *
     * @return array Fields algorithms object
     */
    protected function get_fields(): array
    {
        return ([
            'id'
        ]);
    }

    /**
     * Method creates service logic
     *
     * @return \Mezon\CRUDService\CRUDServiceLogic CRUD service logic object
     */
    protected function get_service_logic()
    {
        return (new \Mezon\CRUDService\CRUDServiceLogic(new FakeRequestParams(), new stdClass()));
    }

    /**
     * Testing constructor
     */
    public function test_constructor_valid()
    {
        // setup and test body
        $ListBuilder = new \Mezon\GUI\ListBuilder($this->get_fields(), new FakeAdapter($this->get_service_logic()));

        // assertions
        $this->assertIsArray($ListBuilder->Fields, 'Invalid fields list type');
    }

    /**
     * Testing listing form
     */
    public function test_listing_form()
    {
        // setup
        $ListBuilder = new \Mezon\GUI\ListBuilder($this->get_fields(), new FakeAdapter($this->get_service_logic()));

        // test body
        $Content = $ListBuilder->listing_form();

        // assertions
        $this->assertContains('>id<', $Content, 'Invalid header content');
        $this->assertContains('>1<', $Content, 'Invalid cell content');
        $this->assertContains('>2<', $Content, 'Invalid cell content');
    }

    /**
     * Testing listing form
     */
    public function test_simple_listing_form()
    {
        // setup
        $ListBuilder = new \Mezon\GUI\ListBuilder($this->get_fields(), new FakeAdapter($this->get_service_logic()));

        // test body
        $Content = $ListBuilder->simple_listing_form();

        // assertions
        $this->assertContains('>id<', $Content, 'Invalid header content');
        $this->assertContains('>1<', $Content, 'Invalid cell content');
        $this->assertContains('>2<', $Content, 'Invalid cell content');
    }
}

?>