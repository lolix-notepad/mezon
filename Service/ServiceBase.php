<?php
namespace Mezon\Service;

/**
 * Class Service
 *
 * @package Mezon
 * @subpackage ServiceBase
 * @author Dodonov A.A.
 * @version v.1.0 (2019/12/09)
 * @copyright Copyright (c) 2019, aeon.org
 */

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
    protected $ServiceTransport = false;

    /**
     * Service's logic
     *
     * @var \Mezon\Service\ServiceLogic|array Login object or list of logic objects
     */
    protected $ServiceLogic = false;

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
    public function __construct(
        $ServiceTransport = \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
        $SecurityProvider = \Mezon\Service\ServiceMockSecurityProvider::class,
        $ServiceLogic = \Mezon\Service\ServiceBaseLogic::class,
        $ServiceModel = \Mezon\Service\ServiceModel::class)
    {
        $this->initTransport($ServiceTransport, $SecurityProvider);

        $this->initServiceLogic($ServiceLogic, $ServiceModel);

        $this->initCustomRoutes();

        if ($this instanceof \Mezon\Service\ServiceBaseLogicInterface) {
            $this->ServiceTransport->fetchActions($this);
        }

        if ($this->ServiceLogic instanceof \Mezon\Service\ServiceBaseLogicInterface) {
            $this->ServiceTransport->fetchActions($this->ServiceLogic);
        } elseif (is_array($this->ServiceLogic)) {
            foreach ($this->ServiceLogic as $ActionsSet) {
                if ($ActionsSet instanceof \Mezon\Service\ServiceBaseLogicInterface) {
                    $this->ServiceTransport->fetchActions($ActionsSet);
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
    protected function initTransport($ServiceTransport, $SecurityProvider): void
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
     * @return \Mezon\Service\ServiceLogic logic object
     */
    protected function constructServiceLogic($ServiceLogic, $ServiceModel)
    {
        if (is_string($ServiceLogic)) {
            $Result = new $ServiceLogic(
                $this->ServiceTransport->getParamsFetcher(),
                $this->ServiceTransport->SecurityProvider,
                $ServiceModel);
        } else {
            $Result = $ServiceLogic;
        }

        return $Result;
    }

    /**
     * Method inits service's logic
     *
     * @param mixed $ServiceLogic
     *            Service's logic
     * @param mixed $ServiceModel
     *            Service's Model
     */
    protected function initServiceLogic($ServiceLogic, $ServiceModel): void
    {
        if (is_array($ServiceLogic)) {
            $this->ServiceLogic = [];

            foreach ($ServiceLogic as $Logic) {
                $this->ServiceLogic[] = $this->constructServiceLogic($Logic, $ServiceModel);
            }
        } else {
            $this->ServiceLogic = $this->constructServiceLogic($ServiceLogic, $ServiceModel);
        }

        $this->ServiceTransport->ServiceLogic = $this->ServiceLogic;
    }

    /**
     * Method inits custom routes if necessary
     */
    protected function initCustomRoutes(): void
    {
        $Reflector = new \ReflectionClass(get_class($this));
        $ClassPath = dirname($Reflector->getFileName());

        if (file_exists($ClassPath . '/conf/routes.php')) {
            $this->ServiceTransport->loadRoutesFromConfig($ClassPath . '/conf/routes.php');
        }

        if (file_exists($ClassPath . '/conf/routes.json')) {
            $this->ServiceTransport->loadRoutes(json_decode(file_get_contents($ClassPath . '/conf/routes.json'), true));
        }
    }

    /**
     * Running $this->ServiceTransport run loop
     */
    public function run(): void
    {
        $this->ServiceTransport->run();
    }

    /**
     * Method sets transport
     *
     * @param \Mezon\Service\ServiceTransport $Transport
     */
    public function setTransport(\Mezon\Service\ServiceTransport $Transport): void
    {
        $this->ServiceTransport = $Transport;
    }

    /**
     * Method returns transport
     *
     * @return \Mezon\Service\ServiceTransport
     */
    public function getTransport(): \Mezon\Service\ServiceTransport
    {
        return $this->ServiceTransport;
    }

    /**
     * Method returns logic
     * 
     * @return \Mezon\Service\ServiceLogic|array
     */
    public function getLogic()
    {
        return $this->ServiceLogic;
    }
}
