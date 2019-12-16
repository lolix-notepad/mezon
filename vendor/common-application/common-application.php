<?php
/**
 * Class CommonApplication
 *
 * @package     Mezon
 * @subpackage  CommonApplication
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/07)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../application/application.php');
require_once (__DIR__ . '/../html-template/html-template.php');
require_once (__DIR__ . '/../router/router.php');
require_once (__DIR__ . '/../service/vendor/service-rest-transport/vendor/rest-exception/rest-exception.php');
require_once (__DIR__ . '/../view/view.php');

/**
 * Common application with any available template
 *
 * To load routes from the config call $this->load_routes_from_config('./conf/routes.json');
 *
 * The format of the *.json config must be like this:
 *
 * [
 * {
 * "route" : "/route1" ,
 * "callback" : "callback1" ,
 * "method" : "POST"
 * } ,
 * {
 * "route" : "/route2" ,
 * "callback" : "callback2" ,
 * "method" : ["GET" , "POST"]
 * }
 * ]
 */
class CommonApplication extends Application
{

    /**
     * Application's template.
     */
    protected $Template = false;

    /**
     * Constructor
     *
     * @param mixed $Template
     *            Template
     */
    public function __construct($Template)
    {
        parent::__construct();

        $this->Template = $Template;

        $this->Router->set_no_processor_found_error_handler([
            $this,
            'no_route_found_error_handler'
        ]);
    }

    /**
     * Method handles 404 errors
     *
     * @param string $Route
     * @codeCoverageIgnore
     */
    public function no_route_found_error_handler(string $Route): void
    {
        $this->redirect_to('/404');
    }

    /**
     * Method renders common parts of all pages.
     *
     * @return array List of common parts.
     */
    public function cross_render(): array
    {
        return ([]);
    }

    /**
     * Formatting call stack
     *
     * @param mixed $e
     *            Exception object
     */
    protected function format_call_stack($e): array
    {
        $Stack = $e->getTrace();

        foreach ($Stack as $i => $Call) {
            $Stack[$i] = (@$Call['file'] == '' ? 'lambda : ' : @$Call['file'] . ' (' . $Call['line'] . ') : ') . (@$Call['class'] == '' ? '' : $Call['class'] . '->') . $Call['function'];
        }

        return ($Stack);
    }

    /**
     * Method formats exception object
     *
     * @param Exception $e
     *            Exception
     * @return object Formatted exception object
     */
    protected function base_formatter(Exception $e): object
    {
        $Error = new stdClass();
        $Error->message = $e->getMessage();
        $Error->code = $e->getCode();
        $Error->call_stack = $this->format_call_stack($e);
        if (isset($_SERVER['HTTP_HOST']) && $_SERVER['REQUEST_URI']) {
            $Error->host = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        } else {
            $Error->host = 'undefined';
        }
        return ($Error);
    }

    /**
     * Method processes exception.
     *
     * @param RESTException $e
     *            RESTException object.
     */
    public function handle_rest_exception(RESTException $e): void
    {
        $Error = $this->base_formatter($e);

        $Error->http_body = $e->http_body;

        print('<pre>' . json_encode($Error, JSON_PRETTY_PRINT));
    }

    /**
     * Method processes exception.
     *
     * @param Exception $e
     *            Exception object.
     */
    public function handle_exception(Exception $e): void
    {
        $Error = $this->base_formatter($e);

        print('<pre>' . json_encode($Error, JSON_PRETTY_PRINT));
    }

    /**
     * Running application.
     */
    public function run(): void
    {
        try {
            $CallRouteResult = $this->call_route();
            if (is_array($CallRouteResult) === false) {
                throw (new Exception('Route was not called properly'));
            }

            $Result = array_merge($CallRouteResult, $this->cross_render());

            if (is_array($Result)) {
                foreach ($Result as $Key => $Value) {
                    $Content = $Value instanceof View ? $Value->render() : $Value;

                    $this->Template->set_page_var($Key, $Content);
                }
            }

            print($this->Template->compile());
        } catch (RESTException $e) {
            $this->handle_rest_exception($e);
        } catch (Exception $e) {
            $this->handle_exception($e);
        }
    }

    /**
     * Getting template
     *
     * @return HTMLTemplate Application's template
     */
    public function get_template(): HTMLTemplate
    {
        return ($this->Template);
    }

    /**
     * Setting template
     *
     * @param mixed $Template
     *            Template
     */
    public function set_template($Template): void
    {
        $this->Template = $Template;
    }
}

?>