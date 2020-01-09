<?php
namespace Mezon;

/**
 * Class CustomClient
 *
 * @package Mezon
 * @subpackage CustomClient
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/07)
 * @copyright Copyright (c) 2019, aeon.org
 */

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
     *            Service URL
     * @param array $Headers
     *            HTTP headers
     */
    public function __construct(string $URL, array $Headers = [])
    {
        if ($URL === false || $URL === '') {
            throw (new \Exception(
                'Service URL must be set in class ' . __CLASS__ . ' extended in ' . get_called_class() .
                ' and called from ' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
                - 23));
        }

        $this->URL = rtrim($URL, '/');

        $this->Headers = $Headers;
    }

    /**
     * Method send request to the URL
     *
     * @param string $URL
     *            URL
     * @param array $Headers
     *            Headers
     * @param string $Method
     *            Request HTTP Method
     * @param array $Data
     *            Request data
     * @return array Response body and HTTP code
     * @codeCoverageIgnore
     */
    protected function sendRequest(string $URL, array $Headers, string $Method, array $Data = []): array
    {
        return (\Mezon\CustomClient\CurlWrapper::sendRequest($URL, $Headers, $Method, $Data));
    }

    /**
     * Method gets result and validates it.
     *
     * @param string $URL
     *            Request URL
     * @param integer $Code
     *            Response HTTP code
     * @return mixed Request result
     */
    protected function dispatchResult(string $URL, int $Code)
    {
        if ($Code == 404) {
            throw (new \Exception("URL: $URL not found"));
        } elseif ($Code == 400) {
            throw (new \Exception("Bad request on URL $URL"));
        } elseif ($Code == 403) {
            throw (new \Exception("Auth error"));
        }
    }

    /**
     * Method returns common headers
     *
     * @return array Headers
     */
    protected function getCommonHeaders(): array
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
    protected function getPostHeaders(): array
    {
        $FullHeaders = $this->getCommonHeaders();

        $FullHeaders[] = 'Content-type: application/x-www-form-urlencoded';

        return ($FullHeaders);
    }

    /**
     * Method sends POST request to REST server
     *
     * @param string $Endpoint
     *            Calling endpoint
     * @param array $Data
     *            Request data
     * @return mixed Result of the request
     */
    public function postRequest(string $Endpoint, array $Data = [])
    {
        $FullURL = $this->URL . '/' . ltrim($Endpoint, '/');

        list ($Body, $Code) = $this->sendRequest($FullURL, $this->getPostHeaders(), 'POST', $Data);

        $this->dispatchResult($FullURL, $Code);

        return ($Body);
    }

    /**
     * Method sends GET request to REST server.
     *
     * @param string $Endpoint
     *            Calling endpoint.
     * @return mixed Result of the remote call.
     */
    public function getRequest(string $Endpoint)
    {
        $FullURL = $this->URL . '/' . ltrim($Endpoint, '/');

        $FullURL = str_replace(' ', '%20', $FullURL);

        list ($Body, $Code) = $this->sendRequest($FullURL, $this->getCommonHeaders(), 'GET');

        $this->dispatchResult($FullURL, $Code);

        return ($Body);
    }

    /**
     * Method sets idempotence key.
     * To remove the key just call this method the second time with the '' parameter
     *
     * @param string $Key
     *            Idempotence key
     */
    public function setIdempotencyKey(string $Key)
    {
        $this->IdempotencyKey = $Key;
    }

    /**
     * Method returns idempotency key
     *
     * @return string Idempotency key
     */
    public function getIdempotencyKey(): string
    {
        return ($this->IdempotencyKey);
    }

    /**
     * Method returns URL
     *
     * @return string URL
     */
    public function getUrl(): string
    {
        return ($this->URL);
    }

    /**
     * Method returns headers
     *
     * @return array Headers
     */
    public function getHeaders(): array
    {
        return ($this->Headers);
    }
}
