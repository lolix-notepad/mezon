<?php
require_once ('autoload.php');

/**
 * Test application
 *
 * @author Dodonov A.A.
 */
class TestExtendingApplication extends \Mezon\CommonApplication\CommonApplication
{

    public function __construct()
    {
        parent::__construct(new \Mezon\HtmlTemplate\HtmlTemplate(__DIR__));
    }

    public function redirectTo($URL): void
    {}
}

class TestApplicationActions extends \Mezon\CrudService\ApplicationActions
{

    public function getSelfId(): string
    {
        return 1;
    }
}

class ApplicationActionsUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Creating mock of the application actions
     *
     * @return object Application actions
     */
    protected function getApplicationActions(): object
    {
        $Object = new TestApplicationActions('entity');

        $CrudServiceClient = $this->getMockBuilder(\Mezon\CrudService\CrudServiceClient::class)
            ->setMethods([
            'getList',
            'delete',
            'getRemoteCreationFormFields'
        ])
            ->disableOriginalConstructor()
            ->getMock();

        $CrudServiceClient->method('getList')->willReturn([
            [
                'id' => 1
            ]
        ]);

        $CrudServiceClient->method('delete')->willReturn('');

        $CrudServiceClient->method('getRemoteCreationFormFields')->willReturn(
            [
                'fields' => [
                    'id' => [
                        'type' => 'integer',
                        'title' => 'id'
                    ]
                ],
                'layout' => []
            ]);

        $Object->setServiceClient($CrudServiceClient);

        return $Object;
    }
    
    /**
     * Common setup for all tests
     */
    public function setUp(): void
    {
        \Mezon\DnsClient\DnsClient::clear();
        \Mezon\DnsClient\DnsClient::setService('entity', 'http://entity.local/');
    }

    /**
     * Testing attaching list method
     */
    public function testAttachListPageMthodInvalid(): void
    {
        // setup
        $Object = $this->getApplicationActions();

        $Application = new TestExtendingApplication();

        // test body and assertions
        $this->expectException(Exception::class);

        $Object->attachListPage($Application, []);
        $Application->entityListingPage();

    }

    /**
     * Testing attaching list method
     */
    public function testAttachListPageMethod(): void
    {
        // setup
        $Object = $this->getApplicationActions();

        $Application = new TestExtendingApplication();

        // test body
        $Object->attachListPage($Application, [
            'default-fields' => 'id'
        ]);

        $Result = $Application->entityListingPage();

        // assertions
        $this->assertTrue(isset($Application->entityListingPage), 'Method "entityListingPage" does not exist');
        $this->assertStringContainsString('>1<', $Result['main']);
        $this->assertStringContainsString('>id<', $Result['main']);
    }

    /**
     * Testing attaching simple list method
     */
    public function testAttachSimpleListPageMethod(): void
    {
        // setup
        $Object = $this->getApplicationActions();
        $Application = new TestExtendingApplication();

        // test body
        $Object->attachSimpleListPage($Application, [
            'default-fields' => 'id'
        ]);
        $Application->entitySimpleListingPage();

        // assertions
        $this->assertTrue(
            isset($Application->entitySimpleListingPage),
            'Method "entitySimpleListingPage" does not exist');
    }

    /**
     * Testing attaching delete method
     */
    public function testAttachDeleteMethod(): void
    {
        // setup
        $Object = $this->getApplicationActions();
        $Application = new TestExtendingApplication();

        // test body
        $Object->attachDeleteRecord($Application, []);

        $Application->entityDeleteRecord('/route/', [
            'id' => 1
        ]);

        // assertions
        $this->assertTrue(isset($Application->entityDeleteRecord), 'Method "entityDeleteRecord" does not exist');
    }

    /**
     * Testing attaching create method
     */
    public function testAttachCreateMethod(): void
    {
        // setup
        $Object = $this->getApplicationActions();
        $Application = new TestExtendingApplication();

        // test body
        $Object->attachCreateRecord($Application, []);
        $Result = $Application->entityCreateRecord();

        // assertions
        $this->assertStringContainsString('x_title', $Result['main'], 'Method "entityCreateRecord" does not exist');
    }

    /**
     * Testing attaching update method
     */
    public function testAttachUpdateMethod(): void
    {
        // setup
        $Object = $this->getApplicationActions();
        $Application = new TestExtendingApplication();

        // test body
        $Object->attachUpdateRecord($Application, []);

        // assertions
        $this->assertTrue(isset($Application->entityUpdateRecord), 'Method "entityUpdateRecord" does not exist');
    }
}
