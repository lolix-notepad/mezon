<?php
require_once (__DIR__ . '/../crud-service-collection.php');

class CRUDServiceCollectionUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function test_constructor_valid()
    {
        $Collection = new \Mezon\CRUDService\CRUDServiceCollection('http://auth', 'some token');

        $this->assertInstanceOf(\Mezon\CRUDService\CRUDServiceClient::class, $Collection->Connector, 'Connector was not setup');
    }

    /**
     * Method returns mock connector
     */
    protected function get_connector()
    {
        $Mock = $this->getMockBuilder('MyClass')
            ->setMethods([
            'new_records_since',
            'get_list'
        ])
            ->disableOriginalConstructor()
            ->getMock();

        return ($Mock);
    }

    /**
     * Testing new_records_since method
     */
    public function test_new_records_since()
    {
        // setup
        $Connector = $this->get_connector();
        $Connector->method('new_records_since')->willReturn([
            [],
            []
        ]);

        $Collection = new \Mezon\CRUDService\CRUDServiceCollection();
        $Collection->set_connector($Connector);

        // test body
        $Collection->new_records_since('2019-01-01');

        // assertions
        $this->assertEquals(2, count($Collection->Collection), 'Invalid records count');
    }

    /**
     * Testing top_by_field method
     */
    public function test_top_by_field()
    {
        // setup
        $Connector = $this->get_connector();
        $Connector->method('get_list')->willReturn([
            [],
            []
        ]);

        $Collection = new \Mezon\CRUDService\CRUDServiceCollection();
        $Collection->set_connector($Connector);

        // test body
        $Collection->top_by_field(2, 'id', 'DESC');

        // assertions
        $this->assertEquals(2, count($Collection->Collection), 'Invalid records count');
    }
}

?>