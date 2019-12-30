<?php
require_once (__DIR__ . '/../../../../../../../autoloader.php');

/**
 * Unit tests for the class HttpRequestParams.
 */

define('SESSION_ID_FIELD_NAME', 'session_id');

$TestHeaders = [];

function getallheaders()
{
    global $TestHeaders;

    return ($TestHeaders);
}

/**
 *
 * @author Dodonov A.A.
 */
class HttpRequestParamsUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     * Method constructs object to be tested.
     */
    protected function getRequestParamsMock()
    {
        $Router = new \Mezon\Router();

        return (new \Mezon\Service\ServiceHttpTransport\HttpRequestParams($Router));
    }

    /**
     * Testing empty result of the get_http_request_headers method.
     */
    public function testGetHttpRequestHeaders()
    {
        $RequestParams = $this->getRequestParamsMock();

        $Param = $RequestParams->getParam('unexisting-param', 'default-value');

        $this->assertEquals('default-value', $Param, 'Default value must be returned but it was not');
    }

    /**
     * Testing getting parameter.
     */
    public function testGetSessionIdFromAuthorization()
    {
        global $TestHeaders;
        $TestHeaders = [
            'Authorization' => 'Basic author session id'
        ];

        $RequestParams = $this->getRequestParamsMock();

        $Param = $RequestParams->getParam(SESSION_ID_FIELD_NAME);

        $this->assertEquals('author session id', $Param, 'Session id must be fetched but it was not');
    }

    /**
     * Testing getting parameter.
     */
    public function testGetSessionIdFromCgiAuthorization()
    {
        global $TestHeaders;
        $TestHeaders = [
            'Cgi-Authorization' => 'Basic cgi author session id'
        ];

        $RequestParams = $this->getRequestParamsMock();

        $Param = $RequestParams->getParam(SESSION_ID_FIELD_NAME);

        $this->assertEquals('cgi author session id', $Param, 'Session id must be fetched but it was not');
    }

    /**
     * Testing getting parameter.
     */
    public function testGetUnexistingSessionId()
    {
        global $TestHeaders;
        $TestHeaders = [];

        $RequestParams = $this->getRequestParamsMock();

        try {
            $RequestParams->getParam(SESSION_ID_FIELD_NAME);

            $this->fail('Exception must be thrown');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing getting parameter from custom header.
     */
    public function testGetParameterFromHeader()
    {
        global $TestHeaders;
        $TestHeaders = [
            'Custom-Header' => 'header value'
        ];

        $RequestParams = $this->getRequestParamsMock();

        $Param = $RequestParams->getParam('Custom-Header');

        $this->assertEquals('header value', $Param, 'Header value must be fetched but it was not');
    }

    /**
     * Testing getting parameter from $_POST.
     */
    public function testGetParameterFromPost()
    {
        $_POST['post-parameter'] = 'post value';

        $RequestParams = $this->getRequestParamsMock();

        $Param = $RequestParams->getParam('post-parameter');

        $this->assertEquals('post value', $Param, 'Value from $_POST must be fetched but it was not');
    }

    /**
     * Testing getting parameter from $_GET.
     */
    public function testGetParameterFromGet()
    {
        $_GET['get-parameter'] = 'get value';

        $RequestParams = $this->getRequestParamsMock();

        $Param = $RequestParams->getParam('get-parameter');

        $this->assertEquals('get value', $Param, 'Value from $_GET must be fetched but it was not');
    }
}

?>