<?php
namespace Mezon;

/**
 * Class Application
 *
 * @package Mezon
 * @subpackage Application
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/13)
 * @copyright Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../router/router.php');

define('NO_ROUTER', false);
define('NO_ROUTE', false);
define('NO_CALLBACK', false);

// TODO add camel-case
/**
 * Base class of the application
 */
class Application
{

    /**
     * Router object
     */
    protected $Router = false;

    /**
     * Constructor
     */
    function __construct()
    {
        // getting application's actions
        $this->Router = new Router();

        $this->Router->fetch_actions($this);
    }

    /**
     * Method calls route and returns it's content
     */
    protected function call_route()
    {
        $Route = explode('/', trim(@$_GET['r'], '/'));

        if ($this->Router == NO_ROUTER) {
            throw (new \Exception('this->Router was not set', - 2));
        }

        $Content = $this->Router->call_route($Route);

        return ($Content);
    }

    /**
     * Method loads single route
     *
     * @param array $Route
     *            Route settings
     */
    public function load_route(array $Route)
    {
        if (isset($Route['route']) == NO_ROUTE) {
            throw (new \Exception('Field "route" must be set'));
        }
        if (isset($Route['callback']) == NO_CALLBACK) {
            throw (new \Exception('Field "callback" must be set'));
        }
        $Class = isset($Route['class']) ? new $Route['class']() : $this;
        $this->Router->add_route($Route['route'], array(
            $Class,
            $Route['callback']
        ), isset($Route['method']) ? $Route['method'] : 'GET');
    }

    /**
     * Method loads routes
     *
     * @param array $Routes
     *            List of routes
     */
    public function load_routes(array $Routes)
    {
        foreach ($Routes as $Route) {
            $this->load_route($Route);
        }
    }

    /**
     * Method loads routes from config file in *.php or *.json format
     *
     * @param string $Path
     *            Path of the config for routes
     */
    public function load_routes_from_config(string $Path = './conf/routes.php')
    {
        if (file_exists($Path)) {
            if (substr($Path, - 5) === '.json') {
                // load config from json
                $Routes = json_decode(file_get_contents($Path), true);
            } else {
                // loadconfig from php
                $Routes = (include ($Path));
            }
            $this->load_routes($Routes);
        } else {
            throw (new \Exception('Route ' . $Path . ' was not found', 1));
        }
    }

    /**
     * Method processes exception
     *
     * @param \Exception $e
     *            Exception object to be formatted
     */
    public function handle_exception(\Exception $e)
    {
        print('<pre>' . $e);
    }

    /**
     * Running application
     */
    public function run()
    {
        try {
            print($this->call_route());
        } catch (\Exception $e) {
            $this->handle_exception($e);
        }
    }

    /**
     * Allowing to call methods added on the fly
     *
     * @param string $Method
     *            Method to be called
     * @param array $Args
     *            Arguments
     * @return mixed Result of the call
     */
    public function __call(string $Method, array $Args)
    {
        if (isset($this->$Method)) {
            $Function = $this->$Method;

            return (call_user_func_array($Function, $Args));
        }
    }

    /**
     * Method redirects user to another page
     *
     * @param string $URL
     *            New page
     */
    public function redirect_to($URL)
    {
        // @codeCoverageIgnoreStart
        header('Location: ' . $URL);
        exit(0);
        // @codeCoverageIgnoreEnd
    }
}

?>