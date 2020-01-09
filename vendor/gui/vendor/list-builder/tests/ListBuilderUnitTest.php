<?php
require_once (__DIR__ . '/../../../../../autoloader.php');

class FakeAdapter implements \Mezon\Gui\ListBuilder\ListBuilderAdapter
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
                'id' => 1,
            ],
            [
                'id' => 2,
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
    public function getRecords(array $Order, int $From, int $Limit): array
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
    public function preprocessListItem(array $Record): array
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
    public function getParam($Param, $Default = false)
    {
        return (false);
    }
}

class ListBuilderUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Method returns list of fields
     *
     * @return array Fields algorithms object
     */
    protected function getFields(): array
    {
        return ([
            'id'
        ]);
    }

    /**
     * Method creates service logic
     *
     * @return \Mezon\CrudService\CrudServiceLogic Crud service logic object
     */
    protected function getServiceLogic()
    {
        return (new \Mezon\CrudService\CrudServiceLogic(new FakeRequestParams(), new stdClass()));
    }

    /**
     * Testing constructor
     */
    public function testConstructorValid()
    {
        // setup and test body
        $ListBuilder = new \Mezon\Gui\ListBuilder($this->getFields(), new FakeAdapter($this->getServiceLogic()));

        // assertions
        $this->assertIsArray($ListBuilder->getFields(), 'Invalid fields list type');
    }

    /**
     * Testing listing form
     */
    public function testListingForm()
    {
        // setup
        $ListBuilder = new \Mezon\Gui\ListBuilder($this->getFields(), new FakeAdapter($this->getServiceLogic()));

        // test body
        $Content = $ListBuilder->listingForm();

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
        $ListBuilder = new \Mezon\Gui\ListBuilder($this->getFields(), new FakeAdapter($this->getServiceLogic()));

        // test body
        $Content = $ListBuilder->simpleListingForm();

        // assertions
        $this->assertContains('>id<', $Content, 'Invalid header content');
        $this->assertContains('>1<', $Content, 'Invalid cell content');
        $this->assertContains('>2<', $Content, 'Invalid cell content');
    }
}
