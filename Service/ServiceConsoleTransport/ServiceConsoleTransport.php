<?php
namespace Mezon\Service\ServiceConsoleTransport;

/**
 * Class ServiceConsoleTransport
 *
 * @package Service
 * @subpackage ServiceConsoleTransport
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/07)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Console transport for all services
 */
class ServiceConsoleTransport extends \Mezon\Service\ServiceTransport
{

    /**
     * Execution result
     */
    public $Result;

    /**
     * Constructor
     *
     * @param mixed $SecurityProvider
     *            Security provider
     */
    public function __construct(
        $SecurityProvider = \Mezon\Service\ServiceMockSecurityProvider::class)
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
     * @return \Mezon\Service\ServiceRequestParamsInterface paremeters fetcher
     */
    public function createFetcher(): \Mezon\Service\ServiceRequestParamsInterface
    {
        return new \Mezon\Service\ServiceConsoleTransport\ConsoleRequestParams($this->Router);
    }

    /**
     * Method runs router
     */
    public function run(): void
    {
        $this->Result = $this->Router->callRoute($_GET['r']);
    }
}
