<?php
namespace Mezon\Service\ServiceRequestParamsInterface;

/**
 * Interface ServiceRequestParamsInterface
 *
 * @package Service
 * @subpackage ServiceRequestParamsInterface
 * @author Dodonov A.A.
 * @version v.1.0 (2019/10/31)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Request params fetcher
 */
interface ServiceRequestParamsInterface
{

    /**
     * Method returns request parameter
     *
     * @param string $Param
     *            parameter name
     * @param mixed $Default
     *            default value
     * @return mixed Parameter value
     */
    public function getParam($Param, $Default = false);
}
