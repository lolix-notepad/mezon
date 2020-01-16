<?php
namespace Mezon\Service\Tests;

require_once ('autoload.php');

/**
 * Class ServiceTests
 *
 * @package Service
 * @subpackage ServiceTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Predefined set of tests for service
 *
 * @author Dodonov A.A.
 * @group baseTests
 */
class ServiceTests extends \PHPUnit\Framework\TestCase
{

    /**
     * Session id.
     */
    protected $SessionId = false;

    /**
     * Server path.
     */
    protected $ServerPath = false;

    /**
     * Headers.
     *
     * @var string
     */
    protected $Headers = false;

    /**
     * Constructor.
     *
     * @param string $Service
     *            - Service name.
     */
    public function __construct(string $Service)
    {
        parent::__construct();

        $this->ServerPath = \Mezon\DnsClient\DnsClient::resolveHost($Service);
    }

    /**
     * Method asserts for errors and warnings in the html code.
     *
     * @param string $Content
     *            - Asserting content.
     * @param string $Message
     *            - Message to be displayed in case of error.
     */
    protected function assertErrors($Content, $Message)
    {
        if (strpos($Content, 'Warning') !== false || strpos($Content, 'Error') !== false ||
            strpos($Content, 'Fatal error') !== false || strpos($Content, 'Access denied') !== false ||
            strpos($Content, "doesn't exist in statement") !== false) {
            throw (new \Exception($Message . "\r\n" . $Content));
        }

        $this->addToAssertionCount(1);
    }

    /**
     * Method asserts JSON.
     *
     * @param mixed $JSONResult
     *            - Result of the call;
     * @param string $Result
     *            - Raw result of the call.
     */
    protected function assertJson($JSONResult, string $Result)
    {
        if ($JSONResult === null && $Result !== '') {
            throw (new \Exception("JSON result is invalid because of:\r\n$Result"));
        }

        if (isset($JSONResult->message)) {
            throw (new \Exception($JSONResult->message, $JSONResult->code));
        }
    }

    /**
     * Method sends post request.
     *
     * @param array $Data
     *            - Request data;
     * @param string $URL
     *            - Requesting endpoint.
     * @return mixed Request result.
     */
    protected function postHttpRequest(array $Data, string $URL)
    {
        $Options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\r\n" .
                ($this->SessionId !== false ? "Cgi-Authorization: Basic " . $this->SessionId . "\r\n" : '') .
                ($this->Headers !== false ? implode("\r\n", $this->Headers) . "\r\n" : ''),
                'method' => 'POST',
                'content' => http_build_query($Data)
            ]
        ];

        $Context = stream_context_create($Options);
        $Result = file_get_contents($URL, false, $Context);

        $this->assertErrors($Result, 'Request have returned warnings/errors');

        $JSONResult = json_decode($Result);

        $this->assertJson($JSONResult, $Result);

        return ($JSONResult);
    }

    /**
     * Method prepares GET request options.
     */
    protected function prepareGetOptions()
    {
        $Options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\r\n" .
                ($this->SessionId !== false ? "Cgi-Authorization: Basic " . $this->SessionId . "\r\n" : '') .
                ($this->Headers !== false ? implode("\r\n", $this->Headers) . "\r\n" : ''),
                'method' => 'GET'
            ]
        ];

        return ($Options);
    }

    /**
     * Method sends GET request
     *
     * @param string $URL
     *            Requesting URL
     * @return mixed Result off the request
     */
    protected function getHtmlRequest(string $URL)
    {
        $Options = $this->prepareGetOptions();

        $Context = stream_context_create($Options);
        $Result = file_get_contents($URL, false, $Context);

        $this->assertErrors($Result, 'Request have returned warnings/errors');

        $JSONResult = json_decode($Result);

        $this->assertJson($JSONResult, $Result);

        return ($JSONResult);
    }

    /**
     * Method returns test data
     *
     * @return array Test data
     */
    protected function getUserData(): array
    {
        return ([
            'login' => 'alexey@dodonov.pro',
            'password' => 'root'
        ]);
    }

    /**
     * Method performs valid connect.
     *
     * @return mixed Result of the connection.
     */
    protected function validConnect()
    {
        $Data = $this->getUserData();

        $URL = $this->ServerPath . '/connect/';

        $Result = $this->postHttpRequest($Data, $URL);

        if (isset($Result->session_id) !== false) {
            $this->SessionId = $Result->session_id;
        }

        return ($Result);
    }

    /**
     * Testing API connection.
     */
    public function testValidConnect()
    {
        // authorization
        $Result = $this->validConnect();

        $this->assertNotEquals($Result, null, 'Connection failed');

        if (isset($Result->session_id) === false) {
            $this->assertEquals(true, false, 'Field "session_id" was not set');
        }

        $this->SessionId = $Result->session_id;
    }

    /**
     * Testing API invalid connection.
     */
    public function testInvalidConnect()
    {
        // authorization
        $Data = $this->getUserData();
        $Data['password'] = '1234';

        $URL = $this->ServerPath . '/connect/';

        $this->expectException(\Exception::class);
        $this->postHttpRequest($Data, $URL);
    }

    /**
     * Testing setting valid token.
     */
    public function testSetValidToken()
    {
        $this->testValidConnect();

        $Data = [
            'token' => $this->SessionId
        ];

        $URL = $this->ServerPath . '/token/' . $this->SessionId . '/';

        $Result = $this->postHttpRequest($Data, $URL);

        $this->assertEquals(isset($Result->session_id), true, 'Connection failed');
    }

    /**
     * Testing setting invalid token.
     */
    public function testSetInvalidToken()
    {
        try {
            $this->testValidConnect();

            $Data = [
                'token' => ''
            ];

            $URL = $this->ServerPath . '/token/unexisting/';

            $this->postHttpRequest($Data, $URL);
        } catch (\Exception $e) {
            // set token method either throws exception or not
            // both is correct behaviour
            $this->assertEquals($e->getMessage(), 'Invalid session token', 'Invalid error message');
            $this->assertEquals($e->getCode(), 2, 'Invalid error code');
        }
    }

    /**
     * Testing login under another user
     */
    public function testLoginAs()
    {
        // setup
        $this->testValidConnect();

        // test body
        $Data = [
            'login' => 'alexey@dodonov.none'
        ];

        $URL = $this->ServerPath . '/login-as/';

        $this->postHttpRequest($Data, $URL);

        // assertions
        $URL = $this->ServerPath . '/self/login/';

        $Result = $this->get_html_request($URL);

        $this->assertEquals(
            'alexey@dodonov.none',
            \Mezon\Functional\Functional::getField($Result, 'login'),
            'Session user must be alexey@dodonov.none');
    }
}
