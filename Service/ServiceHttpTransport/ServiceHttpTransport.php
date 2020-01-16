<?php
namespace Mezon\Service;

/**
 * Class ServiceHttpTransport
 *
 * @package Service
 * @subpackage ServiceHttpTransport
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/13)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * HTTP transport for all services
 *
 * @author Dodonov A.A.
 */
class ServiceHttpTransport extends \Mezon\Service\ServiceTransport
{

    /**
     * Constructor
     *
     * @param mixed $SecurityProvider
     *            Security provider
     */
    public function __construct($SecurityProvider = \Mezon\Service\ServiceMockSecurityProvider::class)
    {
        parent::__construct();

        if (is_string($SecurityProvider)) {
            $this->SecurityProvider = new $SecurityProvider($this->getParamsFetcher());
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
    public function createSession(string $Token = ''): string
    {
        return ($this->SecurityProvider->createSession($Token));
    }

    /**
     * Method creates parameters fetcher
     *
     * @return \Mezon\Service\ServiceRequestParamsInterface paremeters fetcher
     */
    public function createFetcher(): \Mezon\Service\ServiceRequestParamsInterface
    {
        return (new \Mezon\Service\ServiceHttpTransport\HttpRequestParams($this->Router));
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
     * @param \Mezon\Service\ServiceBaseLogicInterface $ServiceLogic
     *            object with all service logic
     * @param string $Method
     *            logic's method to be executed
     * @param array $Params
     *            logic's parameters
     * @return mixed Result of the called method
     */
    public function callLogic(\Mezon\Service\ServiceBaseLogicInterface $ServiceLogic, string $Method, array $Params = [])
    {
        $this->header('Content-type', 'text/html; charset=utf-8');

        return (parent::callLogic($ServiceLogic, $Method, $Params));
    }

    /**
     * Method runs logic functions
     *
     * @param \Mezon\Service\ServiceBaseLogicInterface $ServiceLogic
     *            object with all service logic
     * @param string $Method
     *            logic's method to be executed
     * @param array $Params
     *            logic's parameters
     * @return mixed Result of the called method
     */
    public function callPublicLogic(\Mezon\Service\ServiceBaseLogicInterface $ServiceLogic, string $Method, array $Params = [])
    {
        $this->header('Content-type', 'text/html; charset=utf-8');

        return (parent::callPublicLogic($ServiceLogic, $Method, $Params));
    }
}
