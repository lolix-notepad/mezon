<?php
namespace Mezon\Service;

/**
 * Interface ServiceTransportInterface
 *
 * @package Service
 * @subpackage ServiceTransportInterface
 * @author Dodonov A.A.
 * @version v.1.0 (2019/12/11)
 * @copyright Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../service-request-params/service-request-params.php');

/**
 * Interface for all transports
 *
 * @author Dodonov A.A.
 */
interface ServiceTransportInterface
{

    /**
     * Method creates parameters fetcher
     *
     * @return ServiceRequestParams paremeters fetcher
     */
    public function createFetcher(): ServiceRequestParams;
}

?>