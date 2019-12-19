<?php
require_once (__DIR__ . '/../../service-logic/service-logic.php');
require_once (__DIR__ . '/../../service-security-provider/service-security-provider.php');
require_once (__DIR__ . '/../../service-mock-security-provider/service-mock-security-provider.php');
require_once (__DIR__ . '/../../service-http-transport/vendor/http-request-params/http-request-params.php');
require_once (__DIR__ . '/../service-rest-transport.php');

class FakeSecurityProvider
{
}

class TestingServiceLogic extends \Mezon\Service\ServiceLogic
{

    public function private_method()
    {}

    public function public_method()
    {}

    public function method_exception()
    {
        throw (new Exception('Msg'));
    }

    public function method_rest_exception()
    {
        throw (new \Mezon\Service\ServiceRESTTransport\RESTException('Msg', 0, 1, 1));
    }
}

class ServiceRESTTransportTest extends PHPUnit\Framework\TestCase
{

    /**
     * Getting mock object.
     *
     * @return object ServiceRESTTransport mocked object.
     */
    protected function get_transport_mock()
    {
        $Mock = $this->getMockBuilder('\Mezon\Service\ServiceRESTTransport')
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
     * Testing connect method.
     */
    public function test_constructor()
    {
        $Transport = new \Mezon\Service\ServiceRESTTransport();

        $this->assertNotEquals(null, $Transport->SecurityProvider, 'Security provide was not setup');
    }

    /**
     * Testing that security provider was set.
     */
    public function test_security_provider_init_default()
    {
        $Transport = new \Mezon\Service\ServiceRESTTransport();
        $this->assertInstanceOf('\Mezon\Service\ServiceMockSecurityProvider', $Transport->SecurityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function test_security_provider_init_string()
    {
        $Transport = new \Mezon\Service\ServiceRESTTransport(FakeSecurityProvider::class);
        $this->assertInstanceOf(FakeSecurityProvider::class, $Transport->SecurityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function test_security_provider_init_object()
    {
        $Transport = new \Mezon\Service\ServiceRESTTransport(new FakeSecurityProvider());
        $this->assertInstanceOf(FakeSecurityProvider::class, $Transport->SecurityProvider);
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
     * Setup method call
     *
     * @param string $MethodName
     *            Method name
     * @return object Mock object
     */
    protected function setup_method(string $MethodName): object
    {
        $Mock = $this->get_transport_mock();

        $Mock->ServiceLogic = $this->get_service_logic_mock();

        $Mock->expects($this->never())
            ->method('create_session');

        $Mock->add_route('public-method', $MethodName, 'GET', 'public_call');

        return ($Mock);
    }

    /**
     * Testing public call without create_session method.
     */
    public function test_public_call()
    {
        // setup
        $Mock = $this->setup_method('public_method');

        // test body and assertions
        $Mock->Router->call_route('/public-method/');
    }

    /**
     * Setup method call
     *
     * @param string $MethodName
     *            Method name
     * @return object Mock object
     */
    protected function setup_private_method(string $MethodName): object
    {
        $Mock = $this->get_transport_mock();

        $Mock->ServiceLogic = $this->get_service_logic_mock();

        $Mock->expects($this->once())
            ->method('create_session');

        $Mock->add_route('private-method', $MethodName, 'GET', 'private_call');

        return ($Mock);
    }

    /**
     * Testing private call with create_session method.
     */
    public function test_private_call()
    {
        // setup
        $Mock = $this->setup_private_method('private_method');

        // test body and assertions
        $Mock->Router->call_route('/private-method/');
    }

    /**
     * Testing public call with exception throwing
     */
    public function test_public_call_exception()
    {
        // setup
        $Mock = $this->setup_method('method_exception');

        try {
            // test body and assertions
            $Mock->Router->call_route('/public-method/');
            $this->fail();
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing public call with exception throwing
     */
    public function test_public_call_rest_exception()
    {
        // setup
        $Mock = $this->setup_method('method_rest_exception');

        try {
            // test body and assertions
            $Mock->Router->call_route('/public-method/');
            $this->fail();
        } catch (\Mezon\Service\ServiceRESTTransport\RESTException $e) {
            $this->addToAssertionCount(1);
        } catch (Exception $e) {
            $this->addToAssertionCount(0);
        }
    }
    
    /**
     * Testing private call with exception throwing
     */
    public function test_private_call_exception()
    {
        // setup
        $Mock = $this->setup_private_method('method_exception');
        
        try {
            // test body and assertions
            $Mock->Router->call_route('/private-method/');
            $this->fail();
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }
    
    /**
     * Testing private call with exception throwing
     */
    public function test_private_call_rest_exception()
    {
        // setup
        $Mock = $this->setup_private_method('method_rest_exception');
        
        try {
            // test body and assertions
            $Mock->Router->call_route('/private-method/');
            $this->fail();
        } catch (\Mezon\Service\ServiceRESTTransport\RESTException $e) {
            $this->addToAssertionCount(1);
        } catch (Exception $e) {
            $this->addToAssertionCount(0);
        }
    }
}

?>