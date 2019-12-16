<?php

require_once (__DIR__ . '/../../service-logic/service-logic.php');
require_once (__DIR__ . '/../../service-security-provider/service-security-provider.php');
require_once (__DIR__ . '/../../service-mock-security-provider/service-mock-security-provider.php');
require_once (__DIR__ . '/../vendor/http-request-params/http-request-params.php');
require_once (__DIR__ . '/../service-http-transport.php');

class FakeSecurityProvider
{
}

class TestingServiceLogic extends ServiceLogic
{

    public function private_method()
    {}

    public function public_method()
    {}
}

class ServiceHTTPTransportTest extends PHPUnit\Framework\TestCase
{

    /**
     * Getting mock object.
     *
     * @return object ServiceLogic mocked object.
     */
    protected function get_service_logic_mock()
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
     * Getting mock object.
     *
     * @return object ServiceRESTTransport mocked object.
     */
    protected function get_transport_mock()
    {
        $Mock = $this->getMockBuilder('ServiceHTTPTransport')
            ->setMethods([
            'header',
            'create_session'
        ])
            ->getMock();

        $Mock->expects($this->once())
            ->method('header');

        return ($Mock);
    }

    /**
     * Testing connect method.
     */
    public function test_constructor()
    {
        new ServiceHTTPTransport();

        $this->addToAssertionCount(1);
    }

    /**
     * Testing that security provider was set.
     */
    public function test_security_provider_init_default()
    {
        $Transport = new ServiceHTTPTransport();
        $this->assertInstanceOf('ServiceMockSecurityProvider', $Transport->SecurityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function test_security_provider_init_string()
    {
        $Transport = new ServiceHTTPTransport('FakeSecurityProvider');
        $this->assertInstanceOf('FakeSecurityProvider', $Transport->SecurityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function test_security_provider_init_object()
    {
        $Transport = new ServiceHTTPTransport(new FakeSecurityProvider());
        $this->assertInstanceOf('FakeSecurityProvider', $Transport->SecurityProvider);
    }

    /**
     * Testing that header function is called once for each header.
     */
    public function test_single_header_call()
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
    public function test_single_header_call_public()
    {
        $Mock = $this->get_transport_mock();

        $ServiceLogic = $this->get_service_logic_mock();

        $ServiceLogic->expects($this->once())
            ->method('connect');

        $Mock->call_public_logic($ServiceLogic, 'connect');
    }

    /**
     * Testing expected header values.
     */
    public function test_expected_header_values()
    {
        $Mock = $this->get_transport_mock();

        $Mock->method('header')->with($this->equalTo('Content-type'), $this->equalTo('text/html; charset=utf-8'));

        $ServiceLogic = $this->get_service_logic_mock();

        $Mock->call_logic($ServiceLogic, 'connect');
    }

    /**
     * Testing expected header values.
     */
    public function test_expected_header_values_public()
    {
        $Mock = $this->get_transport_mock();

        $Mock->method('header')->with($this->equalTo('Content-type'), $this->equalTo('text/html; charset=utf-8'));

        $ServiceLogic = $this->get_service_logic_mock();

        $Mock->call_public_logic($ServiceLogic, 'connect');
    }

    /**
     * Getting tricky mock object.
     */
    protected function get_mock_ex(string $Mode)
    {
        $Mock = $this->get_transport_mock();

        $Mock->ServiceLogic = $this->get_service_logic_mock();

        $Mock->method('header')->with($this->equalTo('Content-type'), $this->equalTo('text/html; charset=utf-8'));

        $Mock->add_route('connect', 'connect', 'GET', $Mode, [
            'content_type' => 'text/html; charset=utf-8'
        ]);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['r'] = 'connect';

        return ($Mock);
    }

    /**
     * Testing expected header values.
     */
    public function test_expected_header_values_ex()
    {
        $Mock = $this->get_mock_ex('call_logic');

        $Mock->Router->call_route($_GET['r']);
    }

    /**
     * Testing expected header values.
     */
    public function test_expected_header_values_public_ex()
    {
        $Mock = $this->get_mock_ex('public_call');

        $Mock->Router->call_route($_GET['r']);
    }

    /**
     * Testing public call without create_session method.
     */
    public function test_public_call()
    {
        $Mock = $this->get_transport_mock();

        $Mock->ServiceLogic = $this->get_service_logic_mock();

        $Mock->expects($this->never())
            ->method('create_session');

        $Mock->add_route('public-method', 'public_method', 'GET', 'public_call');

        $Mock->Router->call_route('/public-method/');
    }

    /**
     * Testing private call with create_session method.
     */
    public function test_private_call()
    {
        $Mock = $this->get_transport_mock();

        $Mock->ServiceLogic = $this->get_service_logic_mock();

        $Mock->expects($this->once())
            ->method('create_session');

        $Mock->add_route('private-method', 'private_method', 'GET', 'private_call');

        $Mock->Router->call_route('/private-method/');
    }
}

?>