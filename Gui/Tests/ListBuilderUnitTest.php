<?php

class FakeAdapter implements \Mezon\Gui\ListBuilder\ListBuilderAdapter
{

    /**
     * Method returns all vailable records
     *
     * @return array all vailable records
     */
    public function all(): array
    {
        return [
            [
                'id' => 1,
            ],
            [
                'id' => 2,
            ]
        ];
    }

    /**
     * Method returns a subset from vailable records
     *
     * @param array $order
     *            order settings
     * @param int $from
     *            the beginning of the bunch
     * @param int $limit
     *            the size of the batch
     * @return array subset from vailable records
     */
    public function getRecords(array $order, int $from, int $limit): array
    {
        return $this->all();
    }

    /**
     * Record preprocessor
     *
     * @param array $record
     *            record to be preprocessed
     * @return array preprocessed record
     */
    public function preprocessListItem(array $record): array
    {
        return $record;
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
        return [
            'id'
        ];
    }

    /**
     * Method creates service logic
     *
     * @return \Mezon\CrudService\CrudServiceLogic Crud service logic object
     */
    protected function getServiceLogic()
    {
        return new \Mezon\CrudService\CrudServiceLogic(
            new \Mezon\Service\ServiceConsoleTransport\ConsoleRequestParams(),
            new stdClass());
    }

    /**
     * Testing constructor
     */
    public function testConstructorValid()
    {
        // setup and test body
        $listBuilder = new \Mezon\Gui\ListBuilder($this->getFields(), new FakeAdapter($this->getServiceLogic()));

        // assertions
        $this->assertIsArray($listBuilder->getFields(), 'Invalid fields list type');
    }

    /**
     * Testing listing form
     */
    public function testListingForm()
    {
        // setup
        $listBuilder = new \Mezon\Gui\ListBuilder($this->getFields(), new FakeAdapter($this->getServiceLogic()));

        // test body
        $content = $listBuilder->listingForm();

        // assertions
        $this->assertStringContainsString('>id<', $content, 'Invalid header content');
        $this->assertStringContainsString('>1<', $content, 'Invalid cell content');
        $this->assertStringContainsString('>2<', $content, 'Invalid cell content');
    }

    /**
     * Testing listing form
     */
    public function testSimpleListingForm()
    {
        // setup
        $listBuilder = new \Mezon\Gui\ListBuilder($this->getFields(), new FakeAdapter($this->getServiceLogic()));

        // test body
        $content = $listBuilder->simpleListingForm();

        // assertions
        $this->assertStringContainsString('>id<', $content, 'Invalid header content');
        $this->assertStringContainsString('>1<', $content, 'Invalid cell content');
        $this->assertStringContainsString('>2<', $content, 'Invalid cell content');
    }
}
