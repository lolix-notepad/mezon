<?php
/**
 * Class Service
 *
 * @package     Mezon
 * @subpackage  ServiceBase
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/12/09)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../service-rest-transport/service-rest-transport.php');

/**
 * Base service class
 *
 * It bounds together transport, request parameters fetcher, logic, authorization and model
 *
 * @author Dodonov A.A.
 */
class ServiceBase
{

    /**
     * Service's ransport
     *
     * @var object Service transport object
     */
    var $ServiceTransport = false;

    /**
     * Service's logic
     *
     * @var ServiceLogic object
     */
    var $ServiceLogic = false;

    /**
     * Constructor
     *
     * @param mixed $ServiceTransport
     *            Service's transport
     * @param mixed $SecurityProvider
     *            Service's security provider
     * @param mixed $ServiceLogic
     *            Service's logic
     * @param mixed $ServiceModel
     *            Service's model
     */
    public function __construct($ServiceTransport = 'ServiceRESTTransport', $SecurityProvider = 'ServiceMockSecurityProvider', $ServiceLogic = 'ServiceBaseLogic', $ServiceModel = 'ServiceModel')
    {
        $this->init_transport($ServiceTransport, $SecurityProvider);

        $this->init_service_logic($ServiceLogic, $ServiceModel);

        $this->init_custom_routes();

        if ($this instanceof ServiceBaseLogicInterface) {
            $this->ServiceTransport->fetch_actions($this);
        }

        if ($this->ServiceLogic instanceof ServiceBaseLogicInterface) {
            $this->ServiceTransport->fetch_actions($this->ServiceLogic);
        } elseif (is_array($this->ServiceLogic)) {
            foreach ($this->ServiceLogic as $ActionsSet) {
                if ($ActionsSet instanceof ServiceBaseLogicInterface) {
                    $this->ServiceTransport->fetch_actions($ActionsSet);
                }
            }
        }
    }

    /**
     * Method inits service's transport
     *
     * @param mixed $ServiceTransport
     *            Service's transport
     * @param mixed $SecurityProvider
     *            Service's security provider
     */
    protected function init_transport($ServiceTransport, $SecurityProvider)
    {
        if (is_string($ServiceTransport)) {
            $this->ServiceTransport = new $ServiceTransport($SecurityProvider);
        } else {
            $this->ServiceTransport = $ServiceTransport;
        }
    }

    /**
     * Method constructs service logic if necessary
     *
     * @param mixed $ServiceLogic
     *            Service logic class name of object itself
     * @param mixed $ServiceModel
     *            Service model class name of object itself
     * @return Service logic object
     */
    protected function construct_service_logic($ServiceLogic, $ServiceModel)
    {
        if (is_string($ServiceLogic)) {
            $Result = new $ServiceLogic($this->ServiceTransport->get_params_fetcher(), $this->ServiceTransport->SecurityProvider, $ServiceModel);
        } else {
            $Result = $ServiceLogic;
        }

        return ($Result);
    }

    /**
     * Method inits service's logic
     *
     * @param mixed $ServiceLogic
     *            Service's logic
     * @param mixed $ServiceModel
     *            Service's Model
     */
    protected function init_service_logic($ServiceLogic, $ServiceModel)
    {
        if (is_array($ServiceLogic)) {
            $this->ServiceLogic = [];

            foreach ($ServiceLogic as $Logic) {
                $this->ServiceLogic[] = $this->construct_service_logic($Logic, $ServiceModel);
            }
        } else {
            $this->ServiceLogic = $this->construct_service_logic($ServiceLogic, $ServiceModel);
        }

        $this->ServiceTransport->ServiceLogic = $this->ServiceLogic;
    }

    /**
     * Method inits custom routes if necessary
     */
    protected function init_custom_routes()
    {
        $Reflector = new ReflectionClass(get_class($this));
        $ClassPath = dirname($Reflector->getFileName());

        if (file_exists($ClassPath . '/conf/routes.php')) {
            $this->ServiceTransport->load_routes_from_config($ClassPath . '/conf/routes.php');
        }

        if (file_exists($ClassPath . '/conf/routes.json')) {
            $this->ServiceTransport->load_routes(json_decode(file_get_contents($ClassPath . '/conf/routes.json'), true));
        }
    }

    /**
     * Running $this->ServiceTransport run loop
     */
    public function run()
    {
        $this->ServiceTransport->run();
    }
}

?>