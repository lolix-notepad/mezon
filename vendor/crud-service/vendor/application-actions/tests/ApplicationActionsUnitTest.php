<?php
require_once (__DIR__ . '/../application-actions.php');
require_once (__DIR__ . '/../../../../common-application/common-application.php');

define('ENTITY_NAME', 'entity');

$DNSRecords = [
    ENTITY_NAME => 'http://entity.local/'
];

/**
 * Method returns service setting.
 */
function get_dns_str($Service, $Key1 = false)
{
    global $DNSRecords;

    if (isset($DNSRecords[$Service])) {
        if (is_string($DNSRecords[$Service])) {
            return ($DNSRecords[$Service]);
        } else {
            if ($Key1 !== false) {
                return ($DNSRecords[$Service][$Key1]);
            } else {
                return ($DNSRecords[$Service]);
            }
        }
    } else {
        throw (new Exception('Field "' . $Key1 . '" for "' . $Service . '" service was not set in the DNS'));
    }
}

/**
 * Test application
 *
 * @author Dodonov A.A.
 */
class TestApplication extends \Mezon\CommonApplication
{
    public function __construct(){
        parent::__construct( new \Mezon\HTMLTemplate(__DIR__) );
    }

    public function redirect_to($URL)
    {}
}

class TestApplicationActions extends \Mezon\CRUDService\ApplicationActions
{

    public function get_self_id(): string
    {
        return (1);
    }
}

class ApplicationActionsUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     * Creating mock of the application actions
     *
     * @return object Application actions
     */
    protected function get_application_actions(): object
    {
        $Object = new TestApplicationActions(ENTITY_NAME);

        $Object->CRUDServiceClient = $this->getMockBuilder('\Mezon\CRUDService\CRUDServiceClient')
            ->setMethods([
            'get_list',
            'delete',
            'get_remote_creation_form_fields'
        ])
            ->disableOriginalConstructor()
            ->getMock();

        $Object->CRUDServiceClient->method('get_list')->willReturn([
            [
                'id' => 1
            ]
        ]);

        $Object->CRUDServiceClient->method('delete')->willReturn('');

        $Object->CRUDServiceClient->method('get_remote_creation_form_fields')->willReturn([
            'fields' => [
                'id' => [
                    'type' => 'integer',
                    'title' => 'id'
                ]
            ],
            'layout' => []
        ]);

        return ($Object);
    }

    /**
     * Testing invalid construction
     */
    public function test_constructor_valid()
    {
        // setup and test body
        $Object = $this->get_application_actions();

        // assertions
        $this->assertEquals(ENTITY_NAME, $Object->EntityName, 'EntityName was not initialized');
    }

    /**
     * Testing attaching list method
     */
    public function test_attach_list_page_method_invalid(): void
    {
        // setup
        $Object = $this->get_application_actions();

        $Application = new TestApplication();

        // test body
        try {
            $Object->attach_list_page($Application, []);
            $Application->entity_listing_page();
            // assertions
            $this->fail();
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing attaching list method
     */
    public function test_attach_list_page_method(): void
    {
        // setup
        $Object = $this->get_application_actions();

        $Application = new TestApplication();

        // test body
        $Object->attach_list_page($Application, [
            'default-fields' => 'id'
        ]);

        $Result = $Application->entity_listing_page();

        // assertions
        $this->assertTrue(isset($Application->entity_listing_page), 'Method "entity_listing_page" does not exist');
        $this->assertContains('>1<', $Result['main']);
        $this->assertContains('>id<', $Result['main']);
    }

    /**
     * Testing attaching simple list method
     */
    public function test_attach_simple_list_page_method(): void
    {
        // setup
        $Object = $this->get_application_actions();
        $Application = new TestApplication();

        // test body
        $Object->attach_simple_list_page($Application, [
            'default-fields' => 'id'
        ]);
        $Application->entity_simple_listing_page();

        // assertions
        $this->assertTrue(isset($Application->entity_simple_listing_page), 'Method "entity_simple_listing_page" does not exist');
    }

    /**
     * Testing attaching delete method
     */
    public function test_attach_delete_method(): void
    {
        // setup
        $Object = $this->get_application_actions();
        $Application = new TestApplication();

        // test body
        $Object->attach_delete_record($Application, []);

        $Application->entity_delete_record('/route/', [
            'id' => 1
        ]);

        // assertions
        $this->assertTrue(isset($Application->entity_delete_record), 'Method "entity_delete_record" does not exist');
    }

    /**
     * Testing attaching create method
     */
    public function test_attach_create_method(): void
    {
        // setup
        $Object = $this->get_application_actions();
        $Application = new TestApplication();

        // test body
        $Object->attach_create_record($Application, []);
        $Result = $Application->entity_create_record();

        // assertions
        $this->assertStringContainsString('x_title', $Result['main'], 'Method "entity_create_record" does not exist');
    }

    /**
     * Testing attaching update method
     */
    public function test_attach_update_method(): void
    {
        // setup
        $Object = $this->get_application_actions();
        $Application = new TestApplication();

        // test body
        $Object->attach_update_record($Application, []);

        // assertions
        $this->assertTrue(isset($Application->entity_update_record), 'Method "entity_update_record" does not exist');
    }
}

?>