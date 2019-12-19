<?php
namespace Mezon\Service\ServiceHTTPTransport;

/**
 * Class HTTPRequestParams
 *
 * @package ServiceHTTPTransport
 * @subpackage HTTPRequestParams
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/07)
 * @copyright Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../service-request-params/service-request-params.php');

// TODO add camel-case
/**
 * Request params fetcher.
 */
class HTTPRequestParams implements \Mezon\Service\ServiceRequestParams
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
    protected function get_session_id_from_headers(array $Headers)
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
    protected function get_http_request_headers(): array
    {
        $Headers = getallheaders();

        return ($Headers === false ? [] : $Headers);
    }

    /**
     * Method returns session id from HTTP header
     *
     * @return string Session id
     */
    protected function get_session_id()
    {
        $Headers = $this->get_http_request_headers();

        return ($this->get_session_id_from_headers($Headers));
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
    public function get_param($Param, $Default = false)
    {
        $Headers = $this->get_http_request_headers();

        $Return = $Default;

        if ($Param == 'session_id') {
            $Return = $this->get_session_id();
        } elseif ($this->Router->has_param($Param)) {
            $Return = $this->Router->get_param($Param);
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

?>