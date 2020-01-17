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

        $this->assertEquals(32, strlen($token), 'Invalid token was returned');
    }
}
