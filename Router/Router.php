<?php
namespace Mezon\Router;

/**
 * Class Router
 *
 * @package Mezon
 * @subpackage Router
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/15)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Router class
 */
class Router
{

    /**
     * Mapping of routes to their execution functions for GET requests
     *
     * @var array
     */
    private $GetRoutes = [];

    /**
     * Mapping of routes to their execution functions for GET requests
     *
     * @var array
     */
    private $PostRoutes = [];

    /**
     * Mapping of routes to their execution functions for PUT requests
     *
     * @var array
     */
    private $PutRoutes = [];

    /**
     * Mapping of routes to their execution functions for DELETE requests
     *
     * @var array
     */
    private $DeleteRoutes = [];

    /**
     * Method wich handles invalid route error
     *
     * @var array
     */
    private $InvalidRouteErrorHandler = [];

    /**
     * Parsed parameters of the calling router
     *
     * @var array
     */
    protected $Parameters = [];

    /**
     * Method returns request method
     *
     * @return string Request method
     */
    protected function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Constructor
     */
    function __construct()
    {
        $_SERVER['REQUEST_METHOD'] = $this->getRequestMethod();

        $this->InvalidRouteErrorHandler = [
            $this,
            'noProcessorFoundErrorHandler'
        ];
    }

    /**
     * Method fetches actions from the objects and creates GetRoutes for them
     *
     * @param object $Object
     *            Object to be processed
     */
    public function fetchActions(object $Object): void
    {
        $Methods = get_class_methods($Object);

        foreach ($Methods as $Method) {
            if (strpos($Method, 'action') === 0) {
                $Route = \Mezon\Router\Utils::convertMethodNameToRoute($Method);
                $this->GetRoutes["/$Route/"] = [
                    $Object,
                    $Method
                ];
                $this->PostRoutes["/$Route/"] = [
                    $Object,
                    $Method
                ];
            }
        }
    }

    /**
     * Method adds route and it's handler
     *
     * $Callback function may have two parameters - $Route and $Parameters. Where $Route is a called route,
     * and $Parameters is associative array (parameter name => parameter value) with URL parameters
     *
     * @param string $Route
     *            Route.
     * @param mixed $Callback
     *            Collback wich will be processing route call.
     * @param string $Request
     *            Request type.
     */
    public function addRoute(string $Route, $Callback, $Request = 'GET'): void
    {
        $Route = '/' . trim($Route, '/') . '/';

        if (is_array($Request)) {
            foreach ($Request as $r) {
                $this->addRoute($Route, $Callback, $r);
            }
        } else {
            $Routes = &$this->_getRoutesForMethod($Request);
            $Routes[$Route] = $Callback;
        }
    }

    /**
     * Method searches route processor
     *
     * @param mixed $Processors
     *            Callable router's processor
     * @param string $Route
     *            Route
     * @return mixed Result of the router processor
     */
    private function _findStaticRouteProcessor(&$Processors, string $Route)
    {
        foreach ($Processors as $i => $Processor) {
            // exact router or 'all router'
            if ($i == $Route || $i == '/*/') {
                if (is_callable($Processor) && is_array($Processor) === false) {
                    return $Processor($Route, []);
                }

                $FunctionName = $Processor[1];

                if (is_callable($Processor) &&
                    (method_exists($Processor[0], $FunctionName) || isset($Processor[0]->$FunctionName))) {
                    // passing route path and parameters
                    return call_user_func($Processor, $Route, []);
                } elseif (method_exists($Processor[0], $FunctionName) === false) {
                    $CallableDescription = \Mezon\Router\Utils::getCallableDescription($Processor);

                    throw (new \Exception("'$CallableDescription' does not exists"));
                } else {
                    $CallableDescription = \Mezon\Router\Utils::getCallableDescription($Processor);

                    throw (new \Exception("'$CallableDescription' must be callable entity"));
                }
            }
        }

        return false;
    }

    /**
     * Method returns list of routes for the HTTP method.
     *
     * @param string $Method
     *            HTTP Method
     * @return array Routes
     */
    private function &_getRoutesForMethod(string $Method): array
    {
        switch ($Method) {
            case ('GET'):
                $Result = &$this->GetRoutes;
                break;

            case ('POST'):
                $Result = &$this->PostRoutes;
                break;

            case ('PUT'):
                $Result = &$this->PutRoutes;
                break;

            case ('DELETE'):
                $Result = &$this->DeleteRoutes;
                break;

            default:
                throw (new \Exception('Unsupported request method'));
        }

        return $Result;
    }

    /**
     * Method tries to process static routes without any parameters
     *
     * @param string $Route
     *            Route
     * @return mixed Result of the router processor
     */
    private function _tryStaticRoutes($Route)
    {
        $Routes = $this->_getRoutesForMethod($this->getRequestMethod());

        return $this->_findStaticRouteProcessor($Routes, $Route);
    }

    /**
     * Matching parameter and component
     *
     * @param mixed $Component
     *            Component of the URL
     * @param string $Parameter
     *            Parameter to be matched
     * @return string Matched url parameter
     */
    private function _matchParameterAndComponent(&$Component, string $Parameter)
    {
        $ParameterData = explode(':', trim($Parameter, '[]'));
        $Return = false;

        switch ($ParameterData[0]) {
            case ('i'):
                if (is_numeric($Component)) {
                    $Component = $Component + 0;
                    $Return = $ParameterData[1];
                }
                break;
            case ('a'):
                if (preg_match('/^([a-z0-9A-Z_\/\-\.\@]+)$/', $Component)) {
                    $Return = $ParameterData[1];
                }
                break;
            case ('il'):
                if (preg_match('/^([0-9,]+)$/', $Component)) {
                    $Return = $ParameterData[1];
                }
                break;
            case ('s'):
                $Component = htmlspecialchars($Component, ENT_QUOTES);
                $Return = $ParameterData[1];
                break;
            default:
                throw (new \Exception('Illegal parameter type/value : ' . $ParameterData[0]));
        }

        return $Return;
    }

    /**
     * Method matches route and pattern
     *
     * @param array $CleanRoute
     *            Cleaned route splitted in parts
     * @param array $CleanPattern
     *            Route pattern
     * @return array Array of route's parameters
     */
    private function _matchRouteAndPattern(array $CleanRoute, array $CleanPattern)
    {
        if (count($CleanRoute) !== count($CleanPattern)) {
            return false;
        }

        $Paremeters = [];

        for ($i = 0; $i < count($CleanPattern); $i ++) {
            if (\Mezon\Router\Utils::isParameter($CleanPattern[$i])) {
                $ParameterName = $this->_matchParameterAndComponent($CleanRoute[$i], $CleanPattern[$i]);

                // it's a parameter
                if ($ParameterName !== false) {
                    // parameter was matched, store it!
                    $Paremeters[$ParameterName] = $CleanRoute[$i];
                } else {
                    return false;
                }
            } else {
                // it's a static part of the route
                if ($CleanRoute[$i] !== $CleanPattern[$i]) {
                    return false;
                }
            }
        }

        $this->Parameters = $Paremeters;
    }

    /**
     * Method searches dynamic route processor
     *
     * @param array $Processors
     *            Callable router's processor
     * @param string $Route
     *            Route
     * @return string Result of the router'scall or false if any error occured
     */
    private function _findDynamicRouteProcessor(array &$Processors, string $Route)
    {
        $CleanRoute = explode('/', trim($Route, '/'));

        foreach ($Processors as $i => $Processor) {
            $CleanPattern = explode('/', trim($i, '/'));

            if ($this->_matchRouteAndPattern($CleanRoute, $CleanPattern) !== false) {
                return call_user_func($Processor, $Route, $this->Parameters); // return result of the router
            }
        }

        return false;
    }

    /**
     * Method tries to process dynamic routes with parameters
     *
     * @param string $Route
     *            Route
     * @return string Result of the route call
     */
    private function _tryDynamicRoutes(string $Route)
    {
        switch ($this->getRequestMethod()) {
            case ('GET'):
                $Result = $this->_findDynamicRouteProcessor($this->GetRoutes, $Route);
                break;

            case ('POST'):
                $Result = $this->_findDynamicRouteProcessor($this->PostRoutes, $Route);
                break;

            case ('PUT'):
                $Result = $this->_findDynamicRouteProcessor($this->PutRoutes, $Route);
                break;

            case ('DELETE'):
                $Result = $this->_findDynamicRouteProcessor($this->DeleteRoutes, $Route);
                break;

            default:
                throw (new \Exception('Unsupported request method'));
        }

        return $Result;
    }

    /**
     * Method rturns all available routes
     */
    private function _getAllRoutesTrace()
    {
        return (count($this->GetRoutes) ? 'GET:' . implode(', ', array_keys($this->GetRoutes)) . '; ' : '') .
            (count($this->PostRoutes) ? 'POST:' . implode(', ', array_keys($this->PostRoutes)) . '; ' : '') .
            (count($this->PutRoutes) ? 'PUT:' . implode(', ', array_keys($this->PutRoutes)) . '; ' : '') .
            (count($this->DeleteRoutes) ? 'DELETE:' . implode(', ', array_keys($this->DeleteRoutes)) : '');
    }

    /**
     * Method processes no processor found error
     *
     * @param string $Route
     *            Route
     */
    public function noProcessorFoundErrorHandler(string $Route)
    {
        throw (new \Exception(
            'The processor was not found for the route ' . $Route . ' in ' . $this->_getAllRoutesTrace()));
    }

    /**
     * Method sets InvalidRouteErrorHandler function
     *
     * @param callable $Function
     *            Error handler
     */
    public function setNoProcessorFoundErrorHandler(callable $Function)
    {
        $OldErrorHandler = $this->InvalidRouteErrorHandler;

        $this->InvalidRouteErrorHandler = $Function;

        return $OldErrorHandler;
    }

    /**
     * Processing specified router
     *
     * @param string $Route
     *            Route
     */
    public function callRoute($Route)
    {
        $Route = \Mezon\Router\Utils::prepareRoute($Route);

        if (($Result = $this->_tryStaticRoutes($Route)) !== false) {
            return $Result;
        }

        if (($Result = $this->_tryDynamicRoutes($Route)) !== false) {
            return $Result;
        }

        call_user_func($this->InvalidRouteErrorHandler, $Route);
    }

    /**
     * Method clears router data.
     */
    public function clear()
    {
        $this->GetRoutes = [];

        $this->PostRoutes = [];

        $this->PutRoutes = [];

        $this->DeleteRoutes = [];
    }

    /**
     * Method returns route parameter
     *
     * @param string $Name
     *            Route parameter
     * @return string Route parameter
     */
    public function getParam(string $Name): string
    {
        if (isset($this->Parameters[$Name]) === false) {
            throw (new \Exception('Paremeter ' . $Name . ' was not found in route', - 1));
        }

        return $this->Parameters[$Name];
    }

    /**
     * Does parameter exists
     *
     * @param string $Name
     *            Param name
     * @return bool True if the parameter exists
     */
    public function hasParam(string $Name): bool
    {
        return isset($this->Parameters[$Name]);
    }

    /**
     * Method returns true if the router exists
     *
     * @param string $Route
     *            checking route
     * @return bool true if the router exists, false otherwise
     */
    public function routeExists(string $Route): bool
    {
        $AllRoutes = array_merge($this->DeleteRoutes, $this->PutRoutes, $this->PostRoutes, $this->GetRoutes);

        return (isset($AllRoutes[$Route]));
    }
}
