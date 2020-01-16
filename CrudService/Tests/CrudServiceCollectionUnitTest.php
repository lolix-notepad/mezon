<?php
require_once ('autoload.php');

class CrudServiceCollectionUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructorValid()
    {
        $Collection = new \Mezon\CrudService\CrudServiceCollection('http://auth', 'some token');

        $this->assertInstanceOf(
            \Mezon\CrudService\CrudServiceClient::class,
            $Collection->getConnector(),
            'Connector was not setup');
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

        $Collection = new \Mezon\CrudService\CrudServiceCollection();
        $Collection->setConnector($Connector);

        // test body
        $Collection->newRecordsSince('2019-01-01');

        // assertions
        $this->assertEquals(2, count($Collection->getCollection()), 'Invalid records count');
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

        $Collection = new \Mezon\CrudService\CrudServiceCollection();
        $Collection->setConnector($Connector);

        // test body
        $Collection->topByField(2, 'id', 'DESC');

        // assertions
        $this->assertEquals(2, count($Collection->getCollection()), 'Invalid records count');
    }
}
