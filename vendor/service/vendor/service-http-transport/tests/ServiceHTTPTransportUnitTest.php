<?php

require_once (__DIR__ . '/../../service-logic/service-logic.php');
require_once (__DIR__ . '/../../service-security-provider/service-security-provider.php');
require_once (__DIR__ . '/../../service-mock-security-provider/service-mock-security-provider.php');
require_once (__DIR__ . '/../vendor/http-request-params/http-request-params.php');
require_once (__DIR__ . '/../service-http-transport.php');

class FakeSecurityProvider
{
}

class TestingServiceLogic extends \Mezon\Service\ServiceLogic
{

    public function privateMethod()
    {}

    public function publicMethod()
    {}
}

class ServiceHTTPTransportTest extends PHPUnit\Framework\TestCase
{

    /**
     * Getting mock object.
     *
     * @return object ServiceLogic mocked object.
     */
    protected function getServiceLogicMock()
    {
        $Mock = $this->getMockBuilder(TestingServiceLogic::class)
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
    protected function getTransportMock()
    {
        $Mock = $this->getMockBuilder(\Mezon\Service\ServiceHTTPTransport::class)
            ->setMethods([
            'header',
            'createSession'
        ])
            ->getMock();

        $Mock->expects($this->once())
            ->method('header');

        return ($Mock);
    }

    /**
     * Testing connect method.
     */
    public function testConstructor()
    {
        new \Mezon\Service\ServiceHTTPTransport();

        $this->addToAssertionCount(1);
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitDefault()
    {
        $Transport = new \Mezon\Service\ServiceHTTPTransport();
        $this->assertInstanceOf(\Mezon\Service\ServiceMockSecurityProvider::class, $Transport->SecurityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitString()
    {
        $Transport = new \Mezon\Service\ServiceHTTPTransport('FakeSecurityProvider');
        $this->assertInstanceOf(FakeSecurityProvider::class, $Transport->SecurityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitObject()
    {
        $Transport = new \Mezon\Service\ServiceHTTPTransport(new FakeSecurityProvider());
        $this->assertInstanceOf(FakeSecurityProvider::class, $Transport->SecurityProvider);
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
     * Testing expected header values.
     */
    public function testExpectedHeaderValues()
    {
        $Mock = $this->getTransportMock();

        $Mock->method('header')->with($this->equalTo('Content-type'), $this->equalTo('text/html; charset=utf-8'));

        $ServiceLogic = $this->getServiceLogicMock();

        $Mock->callLogic($ServiceLogic, 'connect');
    }

    /**
     * Testing expected header values.
     */
    public function testExpectedHeaderValuesPublic()
    {
        $Mock = $this->getTransportMock();

        $Mock->method('header')->with($this->equalTo('Content-type'), $this->equalTo('text/html; charset=utf-8'));

        $ServiceLogic = $this->getServiceLogicMock();

        $Mock->callPublicLogic($ServiceLogic, 'connect');
    }

    /**
     * Getting tricky mock object.
     */
    protected function getMockEx(string $Mode)
    {
        $Mock = $this->getTransportMock();

        $Mock->ServiceLogic = $this->getServiceLogicMock();

        $Mock->method('header')->with($this->equalTo('Content-type'), $this->equalTo('text/html; charset=utf-8'));

        $Mock->addRoute('connect', 'connect', 'GET', $Mode, [
            'content_type' => 'text/html; charset=utf-8'
        ]);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['r'] = 'connect';

        return ($Mock);
    }

    /**
     * Testing expected header values.
     */
    public function testExpectedHeaderValuesEx()
    {
        $Mock = $this->getMockEx('callLogic');

        $Mock->Router->callRoute($_GET['r']);
    }

    /**
     * Testing expected header values.
     */
    public function testExpectedHeaderValuesPublicEx()
    {
        $Mock = $this->getMockEx('publicCall');

        $Mock->Router->callRoute($_GET['r']);
    }

    /**
     * Testing public call without createSession method.
     */
    public function testPublicCall()
    {
        $Mock = $this->getTransportMock();

        $Mock->ServiceLogic = $this->getServiceLogicMock();

        $Mock->expects($this->never())
            ->method('createSession');

            $Mock->addRoute('public-method', 'publicMethod', 'GET', 'public_call');

        $Mock->Router->callRoute('/public-method/');
    }

    /**
     * Testing private call with createSession method.
     */
    public function test_private_call()
    {
        $Mock = $this->getTransportMock();

        $Mock->ServiceLogic = $this->getServiceLogicMock();

        $Mock->expects($this->once())
            ->method('createSession');

            $Mock->addRoute('private-method', 'privateMethod', 'GET', 'private_call');

        $Mock->Router->callRoute('/private-method/');
    }
}

?>