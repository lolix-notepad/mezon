<?php
namespace Mezon\Service;
/**
 * Class ServiceSimpleRequestParams
 *
 * @package     Service
 * @subpackage  ServiceSimpleRequestParams
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/10/31)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../service-request-params/service-request-params.php');

/**
 * Request params fetcher.
 */
class ServiceSimpleRequestParams implements ServiceRequestParams
{

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

        if (isset($Headers[$Param])) {
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