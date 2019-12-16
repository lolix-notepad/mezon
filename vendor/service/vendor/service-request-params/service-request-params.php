<?php

/**
 * Class ServiceRequestParams
 *
 * @package     Service
 * @subpackage  ServiceRequestParams
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/10/31)
 * @copyright   Copyright (c) 2019, aeon.org
 */

/**
 * Request params fetcher
 */
interface ServiceRequestParams
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
    public function get_param($Param, $Default = false);
}

?>