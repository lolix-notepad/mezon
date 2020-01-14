<?php
namespace Mezon\Service\ServiceHttpTransport;

/**
 * Class HttpRequestParams
 *
 * @package ServiceHttpTransport
 * @subpackage HttpRequestParams
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/07)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Request params fetcher.
 */
class HttpRequestParams implements \Mezon\Service\ServiceRequestParamsInterface
{

    /**
     * Router of the transport
     *
     * @var \Mezon\Router
     */
    protected $Router = false;

    /**
     * Constructor
     *
     * @param \Mezon\Router $Router
     *            Router object
     */
    public function __construct(\Mezon\Router &$Router)
    {
        $this->Router = $Router;
    }

    /**
     * Fetching auth token from headers
     *
     * @param array $Headers
     *            Request headers
     * @return string Session id
     */
    protected function getSessionIdFromHeaders(array $Headers)
    {
        if (isset($Headers['Authorization'])) {
            $Token = str_replace('Basic ', '', $Headers['Authorization']);

            return ($Token);
        } elseif (isset($Headers['Cgi-Authorization'])) {
            $Token = str_replace('Basic ', '', $Headers['Cgi-Authorization']);

            return ($Token);
        }

        throw (new \Exception('Invalid session token', 2));
    }

    /**
     * Method returns list of the request's headers
     *
     * @return array[string] Array of headers
     */
    protected function getHttpRequestHeaders(): array
    {
        $Headers = getallheaders();

        return ($Headers === false ? [] : $Headers);
    }

    /**
     * Method returns session id from HTTP header
     *
     * @return string Session id
     */
    protected function getSessionId()
    {
        $Headers = $this->getHttpRequestHeaders();

        return ($this->getSessionIdFromHeaders($Headers));
    }

    /**
     * Method returns request parameter
     *
     * @param string $Param
     *            parameter name
     * @param mixed $Default
     *            default value
     * @return mixed Parameter value
     */
    public function getParam($Param, $Default = false)
    {
        $Headers = $this->getHttpRequestHeaders();

        $Return = $Default;

        if ($Param == 'session_id') {
            $Return = $this->getSessionId();
        } elseif ($this->Router->hasParam($Param)) {
            $Return = $this->Router->getParam($Param);
        } elseif (isset($Headers[$Param])) {
            $Return = $Headers[$Param];
        } elseif (isset($_POST[$Param])) {
            $Return = $_POST[$Param];
        } elseif (isset($_GET[$Param])) {
            $Return = $_GET[$Param];
        }

        return ($Return);
    }
}
