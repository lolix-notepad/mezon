<?php
require_once (__DIR__ . '/../../../autoloader.php');
require_once (__DIR__ . '/test-service.php');

class ServiceUnitTest extends \Mezon\Service\ServiceUnitTests
{

    /**
     * Method tests does custom routes were loaded.
     * Trying to read routes both from php and json file and call routes from them.
     */
    public function testCustomRoutesLoading()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $Service = new TestService(
            \Mezon\Service\ServiceConsoleTransport::class,
            $this->getSecurityProvider(AS_STRING),
            TestLogic::class);

        try {
            // route from routes.php
            $_GET['r'] = 'test';
            $Service->run();
            $this->addToAssertionCount(1);
        } catch (Exception $e) {
            $this->fail('Route "test" was not handled');
        }

        try {
            // route from routes.json
            $_GET['r'] = 'test2';
            $Service->run();
            $this->addToAssertionCount(1);
        } catch (Exception $e) {
            $this->fail('Route "test2" was not handled');
        }
    }
}
