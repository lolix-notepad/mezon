<?php
/**
 * Class ServiceHTTPTransport
 *
 * @package     Service
 * @subpackage  ServiceHTTPTransport
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/13)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/vendor/http-request-params/http-request-params.php');
require_once (__DIR__ . '/../service-transport/service-transport.php');
require_once (__DIR__ . '/../service-mock-security-provider/service-mock-security-provider.php');

/**
 * HTTP transport for all services
 *
 * @author Dodonov A.A.
 */
class ServiceHTTPTransport extends ServiceTransport implements ServiceTransportInterface
{

    /**
     * Constructor
     *
     * @param mixed $SecurityProvider
     *            Security provider
     */
    public function __construct($SecurityProvider = 'ServiceMockSecurityProvider')
    {
        parent::__construct();

        if (is_string($SecurityProvider)) {
            $this->SecurityProvider = new $SecurityProvider($this->get_params_fetcher());
        } else {
            $this->SecurityProvider = $SecurityProvider;
        }
    }

    /**
     * Method creates session from existing token or fetched from HTTP headers
     *
     * @param string $Token
     *            Session token
     * @return string Session token
     */
    public function create_session(string $Token = ''): string
    {
        return ($this->SecurityProvider->create_session($Token));
    }
    
    /**
     * Method creates parameters fetcher
     *
     * @return ServiceRequestParams paremeters fetcher
     */
    public function create_fetcher(): ServiceRequestParams
    {
        return(new HTTPRequestParams($this->Router));
    }

    /**
     * Method outputs HTTP header
     *
     * @param string $Header
     *            Header name
     * @param string $Value
     *            Header value
     * @codeCoverageIgnore
     */
    protected function header(string $Header, string $Value)
    {
        header($Header . ':' . $Value);
    }

    /**
     * Method runs logic functions
     *
     * @param ServiceBaseLogicInterface $ServiceLogic
     *            object with all service logic
     * @param string $Method
     *            logic's method to be executed
     * @param array $Params
     *            logic's parameters
     * @return mixed Result of the called method
     */
    public function call_logic(ServiceBaseLogicInterface $ServiceLogic, string $Method, array $Params = [])
    {
        $this->header('Content-type', 'text/html; charset=utf-8');

        return (parent::call_logic($ServiceLogic, $Method, $Params));
    }

    /**
     * Method runs logic functions
     *
     * @param ServiceBaseLogicInterface $ServiceLogic
     *            object with all service logic
     * @param string $Method
     *            logic's method to be executed
     * @param array $Params
     *            logic's parameters
     * @return mixed Result of the called method
     */
    public function call_public_logic(ServiceBaseLogicInterface $ServiceLogic, string $Method, array $Params = [])
    {
        $this->header('Content-type', 'text/html; charset=utf-8');

        return (parent::call_public_logic($ServiceLogic, $Method, $Params));
    }
}

?>