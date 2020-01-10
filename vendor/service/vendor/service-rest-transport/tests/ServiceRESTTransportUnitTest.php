<?php
require_once (__DIR__ . '/../../../../../autoloader.php');

class FakeSecurityProviderForRestTransport
{
}

class TestingServiceLogicForRestTransport extends \Mezon\Service\ServiceLogic
{

    public function privateMethod()
    {}

    public function publicMethod()
    {}

    public function methodException()
    {
        throw (new \Exception('Msg'));
    }

    public function methodRestException()
    {
        throw (new \Mezon\Service\ServiceRestTransport\RestException('Msg', 0, 1, 1));
    }
}

class ServiceRestTransportTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Getting mock object.
     *
     * @return object ServiceRestTransport mocked object.
     */
    protected function getTransportMock()
    {
        $Mock = $this->getMockBuilder(\Mezon\Service\ServiceRestTransport::class)
            ->setMethods([
            'header',
            'createSession',
            'errorResponse',
            'parentErrorResponse'
        ])
            ->getMock();

        $Mock->expects($this->once())
            ->method('header');
        $Mock->method('errorResponse')->willThrowException(
            new \Mezon\Service\ServiceRestTransport\RestException('Msg', 0, 1, 1));
        $Mock->method('parentErrorResponse')->willThrowException(new \Exception('Msg', 0));

        return ($Mock);
    }

    /**
     * Getting mock object.
     *
     * @return object ServiceLogic mocked object.
     */
    protected function getServiceLogicMock()
    {
        $Mock = $this->getMockBuilder(TestingServiceLogicForRestTransport::class)
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
    public function testConstructor()
    {
        $Transport = new \Mezon\Service\ServiceRestTransport();

        $this->assertNotEquals(null, $Transport->SecurityProvider, 'Security provide was not setup');
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitDefault()
    {
        $Transport = new \Mezon\Service\ServiceRestTransport();
        $this->assertInstanceOf(\Mezon\Service\ServiceMockSecurityProvider::class, $Transport->SecurityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitString()
    {
        $Transport = new \Mezon\Service\ServiceRestTransport(FakeSecurityProviderForRestTransport::class);
        $this->assertInstanceOf(FakeSecurityProviderForRestTransport::class, $Transport->SecurityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitObject()
    {
        $Transport = new \Mezon\Service\ServiceRestTransport(new FakeSecurityProviderForRestTransport());
        $this->assertInstanceOf(FakeSecurityProviderForRestTransport::class, $Transport->SecurityProvider);
    }

    /**
     * Testing that header function is called once for each header.
     */
    public function testSingleHeaderCall()
    {
        $Mock = $this->getTransportMock();

        $ServiceLogic = $this->getServiceLogicMock();

        $ServiceLogic->expects($this->once())
            ->method('connect');

        $Mock->callLogic($ServiceLogic, 'connect');
    }

    /**
     * Testing that header function is called once for each header.
     */
    public function testSingleHeaderCallPublic()
    {
        $Mock = $this->getTransportMock();

        $ServiceLogic = $this->getServiceLogicMock();

        $ServiceLogic->expects($this->once())
            ->method('connect');

        $Mock->callPublicLogic($ServiceLogic, 'connect');
    }

    /**
     * Setup method call
     *
     * @param string $MethodName
     *            Method name
     * @return object Mock object
     */
    protected function setupMethod(string $MethodName): object
    {
        $Mock = $this->getTransportMock();

        $Mock->ServiceLogic = $this->getServiceLogicMock();

        $Mock->expects($this->never())
            ->method('createSession');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Mock->addRoute('public-method', $MethodName, 'GET', 'public_call');

        return ($Mock);
    }

    /**
     * Testing public call without createSession method.
     */
    public function testPublicCall()
    {
        // setup
        $Mock = $this->setupMethod('publicMethod');

        // test body and assertions
        $Mock->Router->callRoute('/public-method/');
    }

    /**
     * Setup method call
     *
     * @param string $MethodName
     *            Method name
     * @return object Mock object
     */
    protected function setupPrivateMethod(string $MethodName): object
    {
        $Mock = $this->getTransportMock();

        $Mock->ServiceLogic = $this->getServiceLogicMock();

        $Mock->expects($this->once())
            ->method('createSession');

        $Mock->addRoute('private-method', $MethodName, 'GET', 'private_call');

        return ($Mock);
    }

    /**
     * Testing private call with createSession method.
     */
    public function testPrivateCall()
    {
        // setup
        $Mock = $this->setupPrivateMethod('privateMethod');

        // test body and assertions
        $Mock->Router->callRoute('/private-method/');
    }

    /**
     * Testing public call with exception throwing
     */
    public function testPublicCallException()
    {
        // setup
        $Mock = $this->setupMethod('methodException');

        $this->expectException(Exception::class);

        // test body and assertions
        $Mock->Router->callRoute('/public-method/');
    }

    /**
     * Testing public call with exception throwing
     */
    public function testPublicCallRestException()
    {
        // setup
        $Mock = $this->setupMethod('methodRestException');

        $this->expectException(Exception::class);
        // test body and assertions
        $Mock->Router->callRoute('/public-method/');
    }

    /**
     * Testing private call with exception throwing
     */
    public function testPrivateCallException()
    {
        // setup
        $Mock = $this->setupPrivateMethod('methodException');

        $this->expectException(Exception::class);

        // test body and assertions
        $Mock->Router->callRoute('/private-method/');
    }

    /**
     * Testing private call with exception throwing
     */
    public function testPrivateCallRestException()
    {
        // setup
        $Mock = $this->setupPrivateMethod('methodRestException');

        $this->expectException(Exception::class);

        // test body and assertions
        $Mock->Router->callRoute('/private-method/');
    }
}
