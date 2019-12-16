<?php
require_once (__DIR__ . '/../../service-logic/service-logic.php');
require_once (__DIR__ . '/../../service-security-provider/service-security-provider.php');
require_once (__DIR__ . '/../../service-mock-security-provider/service-mock-security-provider.php');
require_once (__DIR__ . '/../../service-console-transport/vendor/console-request-params/console-request-params.php');
require_once (__DIR__ . '/../service-console-transport.php');

class FakeSecurityProvider
{
}

class TestingServiceLogic extends ServiceLogic
{

    public function private_method()
    {
        return ('private');
    }

    public function public_method()
    {
        return ('public');
    }
}

class ServiceConsoleTransportUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     * Getting mock object.
     *
     * @return object ServiceConsoleTransport mocked object.
     */
    protected function get_transport_mock(): object
    {
        $Mock = $this->getMockBuilder('ServiceConsoleTransport')
            ->setMethods([
            'create_session'
        ])
            ->getMock();

        return ($Mock);
    }

    /**
     * Getting mock object.
     *
     * @return object ServiceLogic mocked object.
     */
    protected function get_service_logic_mock(): object
    {
        $Mock = $this->getMockBuilder('TestingServiceLogic')
            ->disableOriginalConstructor()
            ->setMethods([
            'connect'
        ])
            ->getMock();

        return ($Mock);
    }

    /**
     * Testing connect method.
     */
    public function test_constructor(): void
    {
        $Transport = new ServiceConsoleTransport();

        $this->assertNotEquals(null, $Transport->SecurityProvider, 'Security provide was not setup');
    }

    /**
     * Testing that security provider was set.
     */
    public function test_security_provider_init_default(): void
    {
        $Transport = new ServiceConsoleTransport();
        $this->assertInstanceOf('ServiceMockSecurityProvider', $Transport->SecurityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function test_security_provider_init_string(): void
    {
        $Transport = new ServiceConsoleTransport(FakeSecurityProvider::class);
        $this->assertInstanceOf(FakeSecurityProvider::class, $Transport->SecurityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function test_security_provider_init_object(): void
    {
        $Transport = new ServiceConsoleTransport(new FakeSecurityProvider());
        $this->assertInstanceOf(FakeSecurityProvider::class, $Transport->SecurityProvider);
    }

    /**
     * Testing that header function is called once for each header.
     */
    public function test_single_header_call(): void
    {
        $Mock = $this->get_transport_mock();

        $ServiceLogic = $this->get_service_logic_mock();

        $ServiceLogic->expects($this->once())
            ->method('connect');

        $Mock->call_logic($ServiceLogic, 'connect');
    }

    /**
     * Testing that header function is called once for each header.
     */
    public function test_single_header_call_public(): void
    {
        $Mock = $this->get_transport_mock();

        $ServiceLogic = $this->get_service_logic_mock();

        $ServiceLogic->expects($this->once())
            ->method('connect');

        $Mock->call_public_logic($ServiceLogic, 'connect');
    }

    /**
     * Testing public call without create_session method.
     */
    public function test_public_call(): void
    {
        // setup
        $Mock = $this->get_transport_mock();

        $Mock->ServiceLogic = $this->get_service_logic_mock();

        $Mock->expects($this->never())
            ->method('create_session');

        $Mock->add_route('public-method', 'public_method', 'GET', 'public_call');

        // test body and assertions
        $Mock->Router->call_route('/public-method/');
    }

    /**
     * Testing private call with create_session method.
     */
    public function test_private_call(): void
    {
        // setup
        $Mock = $this->get_transport_mock();

        $Mock->ServiceLogic = $this->get_service_logic_mock();

        $Mock->expects($this->once())
            ->method('create_session');

        $Mock->add_route('private-method', 'private_method', 'GET', 'private_call');

        // test body and assertions
        $Mock->Router->call_route('/private-method/');
    }

    /**
     * Testing 'run' method
     */
    public function test_run(): void
    {
        // setup
        $_GET['r'] = 'public-method';
        $Mock = $this->get_transport_mock();

        $Mock->ServiceLogic = $this->get_service_logic_mock();

        $Mock->expects($this->never())
            ->method('create_session');

        $Mock->add_route('public-method', 'public_method', 'GET', 'public_call');

        // test body
        $Mock->run();

        // assertions
        $this->assertEquals('public', $Mock->Result);
    }
}

?>