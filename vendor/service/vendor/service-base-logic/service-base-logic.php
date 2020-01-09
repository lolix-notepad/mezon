<?php
namespace Mezon\Service;

/**
 * Class ServiceBaseLogic
 *
 * @package Service
 * @subpackage ServiceBaseLogic
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

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
    protected $SecurityProvider = null;

    /**
     * Request params fetcher
     */
    protected $ParamsFetcher = false;

    /**
     * Model
     *
     * @var \Mezon\Service\ServiceModel
     */
    protected $Model = false;

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
    protected function getParam($Param, $Default = false)
    {
        return ($this->ParamsFetcher->getParam($Param, $Default));
    }

    /**
     * Method returns model object
     * 
     * @return ?\Mezon\Service\ServiceModel Model
     */
    public function getModel(): ?\Mezon\Service\ServiceModel
    {
        return ($this->Model);
    }

    /**
     * Method return params fetcher
     *
     * @return object
     */
    public function getParamsFetcher(): object
    {
        // TODO add interface ParamsFetcherInterface
        return ($this->ParamsFetcher);
    }

    /**
     * Method returns security provider
     *
     * @return \Mezon\Service\ServiceSecurityProvider
     */
    public function getSecurityProvider(): \Mezon\Service\ServiceSecurityProvider
    {
        return ($this->SecurityProvider);
    }
}
