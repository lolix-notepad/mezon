<?php
require_once (__DIR__ . '/../crud-service-collection.php');

class CRUDServiceCollectionUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructorValid()
    {
        $Collection = new \Mezon\CRUDService\CRUDServiceCollection('http://auth', 'some token');

        $this->assertInstanceOf(\Mezon\CRUDService\CRUDServiceClient::class, $Collection->Connector, 'Connector was not setup');
    }

    /**
     * Method returns mock connector
     */
    protected function getConnector()
    {
        $Mock = $this->getMockBuilder('MyClass')
            ->setMethods([
            'newRecordsSince',
            'getList'
        ])
            ->disableOriginalConstructor()
            ->getMock();

        return ($Mock);
    }

    /**
     * Testing newRecordsSince method
     */
    public function testNewRecordsSince()
    {
        // setup
        $Connector = $this->getConnector();
        $Connector->method('newRecordsSince')->willReturn([
            [],
            []
        ]);

        $Collection = new \Mezon\CRUDService\CRUDServiceCollection();
        $Collection->setConnector($Connector);

        // test body
        $Collection->newRecordsSince('2019-01-01');

        // assertions
        $this->assertEquals(2, count($Collection->Collection), 'Invalid records count');
    }

    /**
     * Testing top_by_field method
     */
    public function testTopByField()
    {
        // setup
        $Connector = $this->getConnector();
        $Connector->method('getList')->willReturn([
            [],
            []
        ]);

        $Collection = new \Mezon\CRUDService\CRUDServiceCollection();
        $Collection->setConnector($Connector);

        // test body
        $Collection->topByField(2, 'id', 'DESC');

        // assertions
        $this->assertEquals(2, count($Collection->Collection), 'Invalid records count');
    }
}

?>