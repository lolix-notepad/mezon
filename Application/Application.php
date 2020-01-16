<?php
namespace Mezon\Application;

/**
 * Class Application
 *
 * @package Mezon
 * @subpackage Application
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/13)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Base class of the application
 */
class Application
{

    /**
     * Router object
     */
    protected $Router = null;

    /**
     * Constructor
     */
    function __construct()
    {
        // getting application's actions
        $this->Router = new \Mezon\Router\Router();

        $this->Router->fetchActions($this);
    }

    /**
     * Method calls route and returns it's content
     */
    protected function callRoute()
    {
        $Route = explode('/', trim(@$_GET['r'], '/'));

        if ($this->Router === null) {
            throw (new \Exception('this->Router was not set', - 2));
        }

        $Content = $this->Router->callRoute($Route);

        return $Content;
    }

    /**
     * Method loads single route
     *
     * @param array $Route
     *            Route settings
     */
    public function loadRoute(array $Route): void
    {
        if (isset($Route['route']) === false) {
            throw (new \Exception('Field "route" must be set'));
        }
        if (isset($Route['callback']) === false) {
            throw (new \Exception('Field "callback" must be set'));
        }
        $Class = isset($Route['class']) ? new $Route['class']() : $this;
        $this->Router->addRoute($Route['route'], [
            $Class,
            $Route['callback']
        ], isset($Route['method']) ? $Route['method'] : 'GET');
    }

    /**
     * Method loads routes
     *
     * @param array $Routes
     *            List of routes
     */
    public function loadRoutes(array $Routes): void
    {
        foreach ($Routes as $Route) {
            $this->loadRoute($Route);
        }
    }

    /**
     * Method loads routes from config file in *.php or *.json format
     *
     * @param string $Path
     *            Path of the config for routes
     */
    public function loadRoutesFromConfig(string $Path = './conf/routes.php'): void
    {
        if (file_exists($Path)) {
            if (substr($Path, - 5) === '.json') {
                // load config from json
                $Routes = json_decode(file_get_contents($Path), true);
            } else {
                // loadconfig from php
                $Routes = (include ($Path));
            }
            $this->loadRoutes($Routes);
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
    public function handleException(\Exception $e): void
    {
        print('<pre>' . $e);
    }

    /**
     * Running application
     */
    public function run(): void
    {
        try {
            print($this->callRoute());
        } catch (\Exception $e) {
            $this->handleException($e);
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

            return call_user_func_array($Function, $Args);
        }
    }

    /**
     * Method redirects user to another page
     *
     * @param string $URL
     *            New page
     */
    public function redirectTo($URL): void
    {
        // @codeCoverageIgnoreStart
        header('Location: ' . $URL);
        exit(0);
        // @codeCoverageIgnoreEnd
    }
}
