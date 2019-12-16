<?php
require_once (__DIR__ . '/../auth.php');

class AuthUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     * Method returns fake settings
     *
     * @return array fake settings
     */
    protected function get_settings(): array
    {
        return ([
            'client_id' => 1,
            'client_secret' => 2,
            'redirect_uri' => 3
        ]);
    }

    /**
     * Testing constructor
     */
    public function test_constructor()
    {
        // setup and test body
        $Auth = new SocialNetworkAuth($this->get_settings());

        // assertions
        $this->assertEquals(3, count($Auth->Settings), 'Setting were not set');
    }

    /**
     * Testing get_link
     */
    public function test_get_link()
    {
        // setup
        $Auth = new SocialNetworkAuth($this->get_settings());

        // test body
        $Link = $Auth->get_link();

        // assertions
        $this->assertContains('http://oauth-uriclient_id=1&redirect_uri=3&response_type=code', $Link, 'Invalid link was generated');
    }

    /**
     * Testing get_link exception
     */
    public function test_get_link_exception()
    {
        // setup
        $Auth = new SocialNetworkAuth([]);

        try {
            // test body and assertions
            $Auth->get_link();
            $this->fail('Exception must be thrown');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing get_user_info_uri
     */
    public function test_get_user_info_uri()
    {
        // setup
        $Auth = new SocialNetworkAuth($this->get_settings());

        // test body
        $Link = $Auth->get_user_info_uri();

        // assertions
        $this->assertContains('://user-info-uri/?', $Link, 'Invalid user info URI');
    }

    /**
     * Testing get_token_params method
     */
    public function test_get_token_params()
    {
        // setup
        $Auth = new SocialNetworkAuth($this->get_settings());

        // test body
        $Params = $Auth->get_token_params(123);

        // assertions
        $this->assertEquals(1, $Params['client_id'], 'Invalid "client_id"');
        $this->assertEquals(2, $Params['client_secret'], 'Invalid "client_secret"');
        $this->assertEquals(3, $Params['redirect_uri'], 'Invalid "redirect_uri"');
        $this->assertEquals(123, $Params['code'], 'Invalid "code"');
    }

    /**
     * Testing get_token_uri
     */
    public function test_get_token_uri()
    {
        // setup
        $Auth = new SocialNetworkAuth($this->get_settings());

        // test body
        $Link = $Auth->get_token_uri();

        // assertions
        $this->assertContains('://token-uri', $Link, 'Invalid token URI');
    }

    /**
     * Testing get_desired_fields
     */
    public function test_get_desired_fields()
    {
        // setup
        $Auth = new SocialNetworkAuth($this->get_settings());

        // test body
        $Fields = $Auth->get_desired_fields();

        // assertions
        $this->assertContains('desired,fields', $Fields, 'Invalid token URI');
    }

    /**
     * Testing 'dispatch_user_info' method
     */
    public function test_dispatch_user_info()
    {
        // setup
        $Auth = new SocialNetworkAuth($this->get_settings());
        $UserInfo = [
            'picture' => [
                'data' => [
                    'url' => 'image url'
                ]
            ]
        ];

        // test body
        $UserInfo = $Auth->dispatch_user_info($UserInfo);

        // assertions
        $this->assertIsString($UserInfo['picture'], 'Record was not transformed');
    }

    /**
     * Testing 'auth' method
     */
    public function test_auth()
    {
        // setup
        $Auth = $this->getMockBuilder('SocialNetworkAuth')
            ->setMethods([
            'get_request',
            'request_token'
        ])
            ->setConstructorArgs([
            $this->get_settings()
        ])
            ->getMock();
        $Auth->method('get_request')->willReturn(json_encode([
            'id' => 1,
            'picture' => [
                'data' => [
                    'url' => 'http://'
                ]
            ]
        ]));

        $Auth->method('request_token')->willReturn([
            'access_token' => 'some-token'
        ]);

        // test body
        $Result = $Auth->auth('some-code');

        // assertions
        $this->assertTrue($Result, 'Auth was not performed');
    }
}

?>