<?php

/**
 * Unit tests for the class HTTPRequestParams.
 */
require_once (__DIR__ . '/../../../../../../router/router.php');

require_once (__DIR__ . '/../http-request-params.php');

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
class HTTPRequestParamsUnitTest extends PHPUnit\Framework\TestCase
{

	/**
	 * Method constructs object to be tested.
	 */
	protected function get_request_params_mock()
	{
		$Router = new \Mezon\Router();

		return (new \Mezon\Service\ServiceHTTPTransport\HTTPRequestParams($Router));
	}

	/**
	 * Testing empty result of the get_http_request_headers method.
	 */
	public function test_get_http_request_headers()
	{
		$RequestParams = $this->get_request_params_mock();

		$Param = $RequestParams->get_param('unexisting-param', 'default-value');

		$this->assertEquals('default-value', $Param, 'Default value must be returned but it was not');
	}

	/**
	 * Testing getting parameter.
	 */
	public function test_get_session_id_from_authorization()
	{
		global $TestHeaders;
		$TestHeaders = [
			'Authorization' => 'Basic author session id'
		];

		$RequestParams = $this->get_request_params_mock();

		$Param = $RequestParams->get_param(SESSION_ID_FIELD_NAME);

		$this->assertEquals('author session id', $Param, 'Session id must be fetched but it was not');
	}

	/**
	 * Testing getting parameter.
	 */
	public function test_get_session_id_from_cgi_authorization()
	{
		global $TestHeaders;
		$TestHeaders = [
			'Cgi-Authorization' => 'Basic cgi author session id'
		];

		$RequestParams = $this->get_request_params_mock();

		$Param = $RequestParams->get_param(SESSION_ID_FIELD_NAME);

		$this->assertEquals('cgi author session id', $Param, 'Session id must be fetched but it was not');
	}

	/**
	 * Testing getting parameter.
	 */
	public function test_get_unexisting_session_id()
	{
		global $TestHeaders;
		$TestHeaders = [];

		$RequestParams = $this->get_request_params_mock();

		try {
			$RequestParams->get_param(SESSION_ID_FIELD_NAME);

			$this->fail('Exception must be thrown');
		} catch (Exception $e) {
			$this->addToAssertionCount(1);
		}
	}

	/**
	 * Testing getting parameter from custom header.
	 */
	public function test_get_parameter_from_header()
	{
		global $TestHeaders;
		$TestHeaders = [
			'Custom-Header' => 'header value'
		];

		$RequestParams = $this->get_request_params_mock();

		$Param = $RequestParams->get_param('Custom-Header');

		$this->assertEquals('header value', $Param, 'Header value must be fetched but it was not');
	}

	/**
	 * Testing getting parameter from $_POST.
	 */
	public function test_get_parameter_from_post()
	{
		$_POST['post-parameter'] = 'post value';

		$RequestParams = $this->get_request_params_mock();

		$Param = $RequestParams->get_param('post-parameter');

		$this->assertEquals('post value', $Param, 'Value from $_POST must be fetched but it was not');
	}

	/**
	 * Testing getting parameter from $_GET.
	 */
	public function test_get_parameter_from_get()
	{
		$_GET['get-parameter'] = 'get value';

		$RequestParams = $this->get_request_params_mock();

		$Param = $RequestParams->get_param('get-parameter');

		$this->assertEquals('get value', $Param, 'Value from $_GET must be fetched but it was not');
	}
}

?>