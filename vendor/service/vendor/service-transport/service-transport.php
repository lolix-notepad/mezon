<?php
namespace Mezon\Service;

/**
 * Base class for all transports
 *
 * @package Service
 * @subpackage ServiceTransport
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Base class for all transports
 *
 * @author Dodonov A.A.
 */
abstract class ServiceTransport implements ServiceTransportInterface
{

    /**
     * Request params fetcher
     *
     * @var ServiceRequestParams
     */
    var $ParamsFetcher = false;

    /**
     * Service's logic
     *
     * @var ServiceLogic
     */
    var $ServiceLogic = false;

    /**
     * Router
     *
     * @var \Mezon\Router
     */
    var $Router = false;

    /**
     * Security provider
     *
     * @var ServiceSecurityProvider $SecurityProvider Provider of the securitty routines
     */
    public $SecurityProvider = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Router = new \Mezon\Router();
    }

    /**
     * Method searches necessary logic object
     *
     * @param string $Method
     *            Necessary method
     * @return ServiceLogic Logic object
     */
    protected function getNecessaryLogic(string $Method): ServiceLogic
    {
        if (is_object($this->ServiceLogic)) {
            if (method_exists($this->ServiceLogic, $Method)) {
                return ($this->ServiceLogic);
            } else {
                throw (new \Exception('The method "' . $Method . '" was not found in the "' . get_class($this->ServiceLogic) . '"', - 1));
            }
        } elseif (is_array($this->ServiceLogic)) {
            foreach ($this->ServiceLogic as $Logic) {
                if (method_exists($Logic, $Method)) {
                    return ($Logic);
                }
            }

            throw (new \Exception('The method "' . $Method . '" was not found in the set of logic objects', - 1));
        } else {
            throw (new \Exception('Logic was not found', - 2));
        }
        // @codeCoverageIgnoreStart
    }

    // @codeCoverageIgnoreEnd

    /**
     * Method creates session
     *
     * @param bool|string $Token
     *            Session token
     */
    public function createSession(string $Token = ''): string
    {
        // must be overriden
        return ($Token);
    }

    /**
     * Method adds's route
     *
     * @param string $Route
     *            Route
     * @param string $Method
     *            Logic method to be called
     * @param string $Request
     *            HTTP request method
     * @param string $CallType
     *            Type of the call
     */
    public function addRoute(string $Route, string $Method, string $Request, string $CallType = 'callLogic'): void
    {
        $LocalServiceLogic = $this->getNecessaryLogic($Method);

        if ($CallType == 'public_call') {
            $this->Router->addRoute($Route, function () use ($LocalServiceLogic, $Method) {
                return ($this->callPublicLogic($LocalServiceLogic, $Method, []));
            }, $Request);
        } else {
            $this->Router->addRoute($Route, function () use ($LocalServiceLogic, $Method) {
                return ($this->callLogic($LocalServiceLogic, $Method, []));
            }, $Request);
        }
    }

    /**
     * Method loads single route
     *
     * @param array $Route
     *            Route description
     */
    public function loadRoute(array $Route): void
    {
        if (! isset($Route['route'])) {
            throw (new \Exception('Field "route" must be set'));
        }
        if (! isset($Route['callback'])) {
            throw (new \Exception('Field "callback" must be set'));
        }
        $Method = isset($Route['method']) ? $Route['method'] : 'GET';
        $CallType = isset($Route['call_type']) ? $Route['call_type'] : 'callLogic';

        $this->addRoute($Route['route'], $Route['callback'], $Method, $CallType);
    }

    /**
     * Method loads routes
     *
     * @param array $Routes
     *            Route descriptions
     */
    public function loadRoutes(array $Routes): void
    {
        foreach ($Routes as $Route) {
            $this->loadRoute($Route);
        }
    }

    /**
     * Method loads routes from config file
     *
     * @param string $Path
     *            Path to the routes description
     */
    public function loadRoutesFromConfig(string $Path = './conf/routes.php')
    {
        if (file_exists($Path)) {
            $Routes = (include ($Path));

            $this->loadRoutes($Routes);
        } else {
            throw (new \Exception('Route ' . $Path . ' was not found', 1));
        }
    }

    /**
     * Method runs logic functions
     *
     * @param ServiceLogic $ServiceLogic
     *            object with all service logic
     * @param string $Method
     *            Logic's method to be executed
     * @param array $Params
     *            Logic's parameters
     * @return mixed Result of the called method
     */
    public function callLogic(ServiceBaseLogicInterface $ServiceLogic, string $Method, array $Params = [])
    {
        try {
            $Params['SessionId'] = $this->createSession();

            return (call_user_func_array([
                $ServiceLogic,
                $Method
            ], $Params));
        } catch (\Exception $e) {
            return ($this->errorResponse($e));
        }
    }

    /**
     * Method runs logic functions
     *
     * @param ServiceBaseLogicInterface $ServiceLogic
     *            object with all service logic
     * @param string $Method
     *            Logic's method to be executed
     * @param array $Params
     *            Logic's parameters
     * @return mixed Result of the called method
     */
    public function callPublicLogic(ServiceBaseLogicInterface $ServiceLogic, string $Method, array $Params = [])
    {
        try {
            return (call_user_func_array([
                $ServiceLogic,
                $Method
            ], $Params));
        } catch (\Exception $e) {
            return ($this->errorResponse($e));
        }
    }

    /**
     * Error response compilator
     *
     * @param mixed $e
     *            Exception object
     * @return array Error data
     */
    public function errorResponse($e): array
    {
        return ([
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'service' => 'service',
            'call_stack' => $this->formatCallStack($e),
            'host' => 'console'
        ]);
    }

    /**
     * Method returns parameter
     *
     * @param string $Param
     *            Parameter name
     * @param mixed $Default
     *            Default value
     * @return string Parameter value
     */
    public function getParam(string $Param, $Default = false)
    {
        return ($this->ParamsFetcher->getParam($Param, $Default));
    }

    /**
     * Formatting call stack
     *
     * @param mixed $e
     *            Exception object
     * @return array Call stack
     */
    protected function formatCallStack($e): array
    {
        $Stack = $e->getTrace();

        foreach ($Stack as $i => $Call) {
            $Stack[$i] = (@$Call['file'] == '' ? 'lambda : ' : @$Call['file'] . ' (' . $Call['line'] . ') : ') . (@$Call['class'] == '' ? '' : $Call['class'] . '->') . $Call['function'];
        }

        return ($Stack);
    }

    /**
     * Method runs router
     *
     * @codeCoverageIgnore
     */
    public function run(): void
    {
        print($this->Router->callRoute($_GET['r']));
    }

    /**
     * Method processes exception
     *
     * @param \Exception $e
     *            Exception object
     * @codeCoverageIgnore
     */
    public function handleException($e): void
    {
        print('<pre>' . $e->getTraceAsString());
    }

    /**
     * Method fetches actions for routes
     *
     * @param ServiceBaseLogicInterface $ActionsSource
     *            Source of actions
     */
    public function fetchActions(ServiceBaseLogicInterface $ActionsSource): void
    {
        $Methods = get_class_methods($ActionsSource);

        foreach ($Methods as $Method) {
            if (strpos($Method, 'action_') === 0) {
                $Route = str_replace([
                    'action_',
                    '_'
                ], [
                    '',
                    '-'
                ], $Method);

                $this->Router->addRoute($Route, function () use ($ActionsSource, $Method) {
                    return ($this->callPublicLogic($ActionsSource, $Method, []));
                }, 'GET');

                $this->Router->addRoute($Route, function () use ($ActionsSource, $Method) {
                    return ($this->callPublicLogic($ActionsSource, $Method, []));
                }, 'POST');
            }
        }
    }

    /**
     * Method constructs request data fetcher
     *
     * @return ServiceRequestParams Request data fetcher
     */
    public function getParamsFetcher(): ServiceRequestParams
    {
        if ($this->ParamsFetcher !== false) {
            return ($this->ParamsFetcher);
        }

        return ($this->ParamsFetcher = $this->createFetcher());
    }
}

?>