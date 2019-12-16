<?php

/**
 * Class CustomClient
 *
 * @package     Mezon
 * @subpackage  CustomClient
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/07)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/vendor/curl-wrapper/curl-wrapper.php');

/**
 * Custom API client class
 */
class CustomClient
{

	/**
	 * Server host
	 *
	 * @var string
	 */
	protected $URL = false;

	/**
	 * Headers
	 *
	 * @var array
	 */
	protected $Headers = false;

	/**
	 * Idempotence key
	 *
	 * @var string
	 */
	protected $IdempotencyKey = '';

	/**
	 * Constructor
	 *
	 * @param string $URL
	 *        	Service URL
	 * @param array $Headers
	 *        	HTTP headers
	 */
	public function __construct(string $URL, array $Headers = [])
	{
		if ($URL === false || $URL === '') {
			throw (new Exception('Service URL must be set in class ' . __CLASS__ . ' extended in ' . get_called_class() . ' and called from ' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], - 23));
		}

		$this->URL = rtrim($URL, '/');

		$this->Headers = $Headers;
	}

	/**
	 * Method send request to the URL
	 *
	 * @param string $URL URL
	 * @param array $Headers Headers
	 * @param string $Method Request HTTP Method
	 * @param array $Data Request data
	 * @return array Response body and HTTP code
	 * @codeCoverageIgnore
	 */
	protected function send_request(string $URL, array $Headers, string $Method, array $Data = []): array
	{
		return (CurlWrapper::send_request($URL, $Headers, $Method, $Data));
	}

	/**
	 * Method gets result and validates it.
	 *
	 * @param string $URL
	 *        	Request URL
	 * @param integer $Code
	 *        	Response HTTP code
	 * @return mixed Request result
	 */
	protected function dispatch_result(string $URL, int $Code)
	{
		if ($Code == 404) {
			throw (new Exception("URL: $URL not found"));
		} elseif ($Code == 400) {
			throw (new Exception("Bad request on URL $URL"));
		} elseif ($Code == 403) {
			throw (new Exception("Auth error"));
		}
	}

	/**
	 * Method returns common headers
	 *
	 * @return array Headers
	 */
	protected function get_common_headers(): array
	{
		$Result = [];

		if ($this->Headers !== false) {
			$Result = $this->Headers;
		}

		if ($this->IdempotencyKey !== '') {
			$Result[] = 'Idempotency-Key: ' . $this->IdempotencyKey;
		}

		$Result[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0';

		return ($Result);
	}

	/**
	 * Method compiles post headers
	 *
	 * @return array Header
	 */
	protected function get_post_headers(): array
	{
		$FullHeaders = $this->get_common_headers();

		$FullHeaders[] = 'Content-type: application/x-www-form-urlencoded';

		return ($FullHeaders);
	}

	/**
	 * Method sends POST request to REST server
	 *
	 * @param string $Endpoint
	 *        	Calling endpoint
	 * @param array $Data
	 *        	Request data
	 * @return mixed Result of the request
	 */
	public function post_request(string $Endpoint, array $Data = [])
	{
		$FullURL = $this->URL . '/' . ltrim($Endpoint, '/');

		list ($Body, $Code) = $this->send_request($FullURL, $this->get_post_headers(), 'POST', $Data);

		$this->dispatch_result($FullURL, $Code);

		return ($Body);
	}

	/**
	 * Method sends GET request to REST server.
	 *
	 * @param string $Endpoint
	 *        	Calling endpoint.
	 * @return mixed Result of the remote call.
	 */
	public function get_request(string $Endpoint)
	{
		$FullURL = $this->URL . '/' . ltrim($Endpoint, '/');

		$FullURL = str_replace(' ', '%20', $FullURL);

		list ($Body, $Code) = $this->send_request($FullURL, $this->get_common_headers(), 'GET');

		$this->dispatch_result($FullURL, $Code);

		return ($Body);
	}

	/**
	 * Method sets idempotence key.
	 * To remove the key just call this method the second time with the '' parameter
	 *
	 * @param string $Key
	 *        	Idempotence key
	 */
	public function set_idempotency_key(string $Key)
	{
		$this->IdempotencyKey = $Key;
	}

	/**
	 * Method returns idempotency key
	 *
	 * @return string Idempotency key
	 */
	public function get_idempotency_key(): string
	{
		return ($this->IdempotencyKey);
	}

	/**
	 * Method returns URL
	 *
	 * @return string URL
	 */
	public function get_url(): string
	{
		return ($this->URL);
	}

	/**
	 * Method returns headers
	 *
	 * @return array Headers
	 */
	public function get_headers(): array
	{
		return ($this->Headers);
	}
}

?>