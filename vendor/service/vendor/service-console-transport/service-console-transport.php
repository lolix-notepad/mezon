<?php
namespace Mezon\Service;

/**
 * Class ServiceConsoleTransport
 *
 * @package Service
 * @subpackage ServiceConsoleTransport
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/07)
 * @copyright Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../service-transport/service-transport.php');

/**
 * Console transport for all services
 */
class ServiceConsoleTransport extends ServiceTransport
{

    /**
     * Execution result
     */
    var $Result;

    /**
     * Constructor
     *
     * @param mixed $SecurityProvider
     *            Security provider
     */
    public function __construct($SecurityProvider = '\Mezon\Service\ServiceMockSecurityProvider')
    {
        parent::__construct();

        if (is_string($SecurityProvider)) {
            $this->SecurityProvider = new $SecurityProvider($this->getParamsFetcher());
        } else {
            $this->SecurityProvider = $SecurityProvider;
        }
    }

    /**
     * Method creates parameters fetcher
     *
     * @return ServiceRequestParams paremeters fetcher
     */
    public function createFetcher(): ServiceRequestParams
    {
        return (new \Mezon\Service\ServiceConsoleTransport\ConsoleRequestParams($this->Router));
    }

    /**
     * Method runs router
     */
    public function run(): void
    {
        $this->Result = $this->Router->callRoute($_GET['r']);
    }
}

?>