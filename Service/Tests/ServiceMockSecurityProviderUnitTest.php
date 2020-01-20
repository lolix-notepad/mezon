<?php

class ServiceMockSecurityProviderUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing session creation.
     */
    public function testCreateSession1()
    {
        $provider = new \Mezon\Service\ServiceMockSecurityProvider();

        $token = $provider->createSession();

        $this->assertEquals(32, strlen($token));
    }

    /**
     * Testing session creation with already created token
     */
    public function testCreateSession2()
    {
        $provider = new \Mezon\Service\ServiceMockSecurityProvider();

        $token = $provider->createSession('token');

        $this->assertEquals('token', $token);
    }

    /**
     * Testing setting token
     */
    public function testSetToken()
    {
        $provider = new \Mezon\Service\ServiceMockSecurityProvider();

        $token = $provider->setToken('token');

        $this->assertEquals('token', $token);
    }
}
