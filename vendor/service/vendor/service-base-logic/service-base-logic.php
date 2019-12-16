<?php
/**
 * Class ServiceBaseLogic
 *
 * @package     Service
 * @subpackage  ServiceBaseLogic
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/17)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../service-base-logic-interface/service-base-logic-interface.php');
require_once (__DIR__ . '/../service-model/service-model.php');
require_once (__DIR__ . '/../service-request-params/service-request-params.php');

/**
 * Class stores all service's logic
 *
 * @author Dodonov A.A.
 */
class ServiceBaseLogic implements ServiceBaseLogicInterface
{

    /**
     * Security provider
     *
     * @var ServiceSecurityProvider
     */
    var $SecurityProvider = null;

    /**
     * Request params fetcher
     */
    var $ParamsFetcher = false;

    /**
     * Model
     */
    var $Model = false;

    /**
     * Constructor
     *
     * @param ServiceRequestParams $ParamsFetcher
     *            Params fetcher
     * @param object $SecurityProvider
     *            Security provider
     * @param mixed $Model
     *            Service model
     */
    public function __construct(ServiceRequestParams $ParamsFetcher, object $SecurityProvider, $Model = null)
    {
        $this->ParamsFetcher = $ParamsFetcher;

        $this->SecurityProvider = $SecurityProvider;

        if (is_string($Model)) {
            $this->Model = new $Model();
        } else {
            $this->Model = $Model;
        }
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
    protected function get_param($Param, $Default = false)
    {
        return ($this->ParamsFetcher->get_param($Param, $Default));
    }
}

?>