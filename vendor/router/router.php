<?php
namespace Mezon;

/**
 * Class Router
 *
 * @package Mezon
 * @subpackage Router
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/15)
 * @copyright Copyright (c) 2019, aeon.org
 */

// TODO add camel-case
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
    protected function get_request_method(): string
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Constructor
     */
    function __construct()
    {
        $_SERVER['REQUEST_METHOD'] = $this->get_request_method();

        $this->InvalidRouteErrorHandler = [
            $this,
            'no_processor_found_error_handler'
        ];
    }

    /**
     * Method fetches actions from the objects and creates GetRoutes for them
     *
     * @param object $Object
     *            Object to be processed
     */
    public function fetch_actions(object $Object)
    {
        $Methods = get_class_methods($Object);

        foreach ($Methods as $Method) {
            if (strpos($Method, 'action_') === 0) {
                $Route = str_replace([
                    'action_',
                    '_'
                ], [
                    '',
                    '-'
                ], $Method);
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
    public function add_route(string $Route, $Callback, $Request = 'GET')
    {
        $Route = '/' . trim($Route, '/') . '/';

        if (is_array($Request)) {
            foreach ($Request as $r) {
                $this->add_route($Route, $Callback, $r);
            }
        } else {
            $Routes = &$this->get_routes_for_method($Request);
            $Routes[$Route] = $Callback;
        }
    }

    /**
     * Method prepares route for the next processing
     *
     * @param mixed $Route
     *            Route
     * @return string Trimmed route
     */
    private function prepare_route($Route): string
    {
        if (is_array($Route) && $Route[0] === '') {
            $Route = $_SERVER['REQUEST_URI'];
        }

        if ($Route == '/') {
            $Route = '/index/';
        }

        if (is_array($Route)) {
            $Route = implode('/', $Route);
        }

        return ('/' . trim($Route, '/') . '/');
    }

    /**
     * Method compiles callable description
     *
     * @param mixed $Processor
     *            Object to be descripted
     * @return string Description
     */
    private function get_callable_description($Processor): string
    {
        if (is_string($Processor)) {
            return ($Processor);
        } elseif (is_object($Processor[0])) {
            return (get_class($Processor[0]) . '::' . $Processor[1]);
        } else {
            return ($Processor[0] . '::' . $Processor[1]);
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
    private function find_static_route_processor(&$Processors, string $Route)
    {
        foreach ($Processors as $i => $Processor) {
            // exact router or 'all router'
            if ($i == $Route || $i == '/*/') {
                if (is_callable($Processor) && is_array($Processor) === false) {
                    return ($Processor($Route, []));
                }

                $FunctionName = $Processor[1];

                if (is_callable($Processor) && (method_exists($Processor[0], $FunctionName) || isset($Processor[0]->$FunctionName))) {
                    // passing route path and parameters
                    return (call_user_func($Processor, $Route, []));
                } elseif (method_exists($Processor[0], $FunctionName) === false) {
                    $CallableDescription = $this->get_callable_description($Processor);

                    throw (new \Exception("'$CallableDescription' does not exists"));
                } else {
                    $CallableDescription = $this->get_callable_description($Processor);

                    throw (new \Exception("'$CallableDescription' must be callable entity"));
                }
            }
        }

        return (false);
    }

    /**
     * Method returns list of routes for the HTTP method.
     *
     * @param string $Method
     *            HTTP Method
     * @return array Routes
     */
    private function &get_routes_for_method(string $Method): array
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

        return ($Result);
    }

    /**
     * Method tries to process static routes without any parameters
     *
     * @param string $Route
     *            Route
     * @return mixed Result of the router processor
     */
    private function try_static_routes($Route)
    {
        $Routes = $this->get_routes_for_method($this->get_request_method());

        return ($this->find_static_route_processor($Routes, $Route));
    }

    /**
     * Method detects if the $String is a parameter or a static component of the route
     *
     * @param string $String
     *            String to be validated
     * @return bool Does we have parameter
     */
    private function is_parameter($String): bool
    {
        return ($String[0] == '[' && $String[strlen($String) - 1] == ']');
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
    private function match_parameter_and_component(&$Component, string $Parameter)
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

        return ($Return);
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
    private function match_route_and_pattern(array $CleanRoute, array $CleanPattern)
    {
        if (count($CleanRoute) !== count($CleanPattern)) {
            return (false);
        }

        $Paremeters = [];

        for ($i = 0; $i < count($CleanPattern); $i ++) {
            if ($this->is_parameter($CleanPattern[$i])) {
                $ParameterName = $this->match_parameter_and_component($CleanRoute[$i], $CleanPattern[$i]);

                // it's a parameter
                if ($ParameterName !== false) {
                    // parameter was matched, store it!
                    $Paremeters[$ParameterName] = $CleanRoute[$i];
                } else {
                    return (false);
                }
            } else {
                // it's a static part of the route
                if ($CleanRoute[$i] !== $CleanPattern[$i]) {
                    return (false);
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
    private function find_dynamic_route_processor(array &$Processors, string $Route)
    {
        $CleanRoute = explode('/', trim($Route, '/'));

        foreach ($Processors as $i => $Processor) {
            $CleanPattern = explode('/', trim($i, '/'));

            if ($this->match_route_and_pattern($CleanRoute, $CleanPattern) !== false) {
                return (call_user_func($Processor, $Route, $this->Parameters)); // return result of the router
            }
        }

        return (false);
    }

    /**
     * Method tries to process dynamic routes with parameters
     *
     * @param string $Route
     *            Route
     * @return string Result of the route call
     */
    private function try_dynamic_toutes(string $Route)
    {
        switch ($this->get_request_method()) {
            case ('GET'):
                $Result = $this->find_dynamic_route_processor($this->GetRoutes, $Route);
                break;

            case ('POST'):
                $Result = $this->find_dynamic_route_processor($this->PostRoutes, $Route);
                break;

            case ('PUT'):
                $Result = $this->find_dynamic_route_processor($this->PutRoutes, $Route);
                break;

            case ('DELETE'):
                $Result = $this->find_dynamic_route_processor($this->DeleteRoutes, $Route);
                break;

            default:
                throw (new \Exception('Unsupported request method'));
        }

        return ($Result);
    }

    /**
     * Method rturns all available routes
     */
    private function get_all_routes_trace()
    {
        return ((count($this->GetRoutes) ? 'GET:' . implode(', ', array_keys($this->GetRoutes)) . '; ' : '') . (count($this->PostRoutes) ? 'POST:' . implode(', ', array_keys($this->PostRoutes)) . '; ' : '') . (count($this->PutRoutes) ? 'PUT:' . implode(', ', array_keys($this->PutRoutes)) . '; ' : '') . (count($this->DeleteRoutes) ? 'DELETE:' . implode(', ', array_keys($this->DeleteRoutes)) : ''));
    }

    /**
     * Method processes no processor found error
     *
     * @param string $Route
     *            Route
     */
    public function no_processor_found_error_handler(string $Route)
    {
        throw (new \Exception('The processor was not found for the route ' . $Route . ' in ' . $this->get_all_routes_trace()));
    }

    /**
     * Method sets InvalidRouteErrorHandler function
     *
     * @param callable $Function
     *            Error handler
     */
    public function set_no_processor_found_error_handler(callable $Function)
    {
        $OldErrorHandler = $this->InvalidRouteErrorHandler;

        $this->InvalidRouteErrorHandler = $Function;

        return ($OldErrorHandler);
    }

    /**
     * Processing specified router
     *
     * @param string $Route
     *            Route
     */
    public function call_route($Route)
    {
        $Route = $this->prepare_route($Route);

        if (($Result = $this->try_static_routes($Route)) !== false) {
            return ($Result);
        }

        if (($Result = $this->try_dynamic_toutes($Route)) !== false) {
            return ($Result);
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
     * Method returns route object
     *
     * @param string $Route
     *            Route URL
     * @return object Route object
     */
    public function get_route(string $Route): object
    {
        if (isset($this->GetRoutes[$Route])) {
            return ($this->GetRoutes[$Route]);
        }
        if (isset($this->PostRoutes[$Route])) {
            return ($this->PostRoutes[$Route]);
        }
        if (isset($this->PutRoutes[$Route])) {
            return ($this->PutRoutes[$Route]);
        }
        if (isset($this->DeleteRoutes[$Route])) {
            return ($this->DeleteRoutes[$Route]);
        }
        throw (new \Exception('Route was not found'));
    }

    /**
     * Method returns route parameter
     *
     * @param string $Name
     *            Route parameter
     * @return string Route parameter
     */
    public function get_param(string $Name): string
    {
        if (isset($this->Parameters[$Name]) === false) {
            throw (new \Exception('Paremeter ' . $Name . ' was not found in route', - 1));
        }

        return ($this->Parameters[$Name]);
    }

    /**
     * Does parameter exists
     *
     * @param string $Name
     *            Param name
     * @return bool True if the parameter exists
     */
    public function has_param(string $Name): bool
    {
        return (isset($this->Parameters[$Name]));
    }
}

?>