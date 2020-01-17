<?php
namespace Mezon\CrudService\Tests;

/**
 * Class CrudServiceClientUnitTests
 *
 * @package CrudServiceClient
 * @subpackage CrudServiceClientUnitTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/09/18)
 * @copyright Copyright (c) 2019, aeon.org
 */
class HackedCrudServiceClient extends \Mezon\CrudService\CrudServiceClient
{

    public function publicGetCompiledFilter($Filter, $Amp = true): string
    {
        return (parent::getCompiledFilter($Filter, $Amp));
    }
}

/**
 * Common unit tests for CrudServiceClient and all derived client classes
 *
 * @author Dodonov A.A.
 * @group baseTests
 */
class CrudServiceClientUnitTests extends \Mezon\Service\Tests\ServiceClientUnitTests
{

    /**
     * Getting mock object for Crud service client
     *
     * @return object Mock object
     */
    protected function getCrudServiceClientMock()
    {
        $Mock = $this->getMockBuilder(\Mezon\CrudService\CrudServiceClient::class)
            ->setMethods([
            'getRequest',
            'postRequest'
        ])
            ->disableOriginalConstructor()
            ->getMock();

        return $Mock;
    }

    /**
     * Method make full setup of the mock object
     *
     * @param string $ConfigName
     * @return object Mock object
     */
    protected function getSetupMockWithGetMethod(string $ConfigName)
    {
        $Mock = $this->getCrudServiceClientMock();

        $Mock->method('getRequest')->willReturn(
            json_decode(file_get_contents(__DIR__ . '/conf/' . $ConfigName . '.json')));
        $Mock->method('postRequest')->willReturn(
            json_decode(file_get_contents(__DIR__ . '/conf/' . $ConfigName . '.json')));

        return $Mock;
    }

    /**
     * Testing 'getCompiledFilter' method
     */
    public function testGetCompiledFilter1()
    {
        // setup
        $Client = new HackedCrudServiceClient('https://ya.ru');

        // test body
        $Result = $Client->publicGetCompiledFilter(false);

        // assertions
        $this->assertEquals('', $Result, 'Empty string must be returned');
    }

    /**
     * Testing 'getCompiledFilter' method
     */
    public function testGetCompiledFilter2()
    {
        // setup
        $Client = new HackedCrudServiceClient('https://ya.ru');

        // test body
        $Result = $Client->publicGetCompiledFilter([
            'field1' => 1,
            'field2' => 2
        ], true);

        // assertions
        $this->assertStringContainsString('filter[field1]=1', $Result);
        $this->assertStringContainsString('filter[field2]=2', $Result);
    }

    /**
     * Testing 'getCompiledFilter' method
     */
    public function testGetCompiledFilter3()
    {
        // setup
        $Client = new HackedCrudServiceClient('https://ya.ru');

        // test body
        $Result = $Client->publicGetCompiledFilter([
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
     * Testing 'getByIdsArray' method
     */
    public function testGetByIdsArray()
    {
        // setup
        $Client = $this->getSetupMockWithGetMethod('GetByIdsArray');

        // test body
        $ids = [
            1,
            2
        ];
        $Result = $Client->getByIdsArray($ids); // compile
        $Result2 = $Client->getByIdsArray($ids); // cache

        // assertions
        $this->assertEquals(2, count($Result));
        $this->assertEquals(2, count($Result2));
    }

    /**
     * Testing 'getByIdsArray' method
     */
    public function testGetByIdsArrayNull()
    {
        // setup
        $Client = $this->getSetupMockWithGetMethod('GetByIdsArray');

        // test body
        $Result = $Client->getByIdsArray([]);

        // assertions
        $this->assertEquals(0, count($Result));
    }

    /**
     * Testing 'recordsCountByField' method
     */
    public function testRecordsCountByField()
    {
        // setup
        $Client = $this->getSetupMockWithGetMethod('RecordsCountByField');

        // test body
        $Result = $Client->recordsCountByField('id');
        $Result2 = $Client->recordsCountByField('id');

        // assertions
        $this->assertEquals(3, count($Result));
        $this->assertEquals(3, count($Result2));
    }

    /**
     * Testing instance method
     */
    public function testInstance()
    {
        // setup and test body
        $Client = \Mezon\CrudService\CrudServiceClient::instance('http://auth', 'token');

        // assertions
        $this->assertEquals('token', $Client->getToken());
    }

    /**
     * Testing 'getList' method
     */
    public function testGetList()
    {
        // setup
        $Client = $this->getSetupMockWithGetMethod('GetList');

        // test body
        $Result = $Client->getList(0, 1, false, false, false);

        // assertions
        $this->assertEquals(2, count($Result));
    }

    /**
     * Testing 'getList' method
     */
    public function testGetListOrder()
    {
        // setup
        $Client = $this->getSetupMockWithGetMethod('GetList');

        // test body
        $Result = $Client->getList(0, 1, false, false, [
            'field' => 'id',
            'order' => 'ASC'
        ]);

        // assertions
        $this->assertEquals(2, count($Result));
    }

    /**
     * Testing 'create' method
     */
    public function testCreate()
    {
        // setup
        $Client = $this->getSetupMockWithGetMethod('Create');

        // test body
        $Result = $Client->create(
            [
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
