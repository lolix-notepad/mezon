<?php
namespace Mezon\CommonApplication;

/**
 * Class CommonApplication
 *
 * @package Mezon
 * @subpackage CommonApplication
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/07)
 * @copyright Copyright (c) 2019, aeon.org
 */

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
class CommonApplication extends \Mezon\Application\Application
{

    /**
     * Application's template
     *
     * @var \Mezon\HtmlTemplate\HtmlTemplate
     */
    protected $Template = false;

    /**
     * Constructor
     *
     * @param \Mezon\HtmlTemplate\HtmlTemplate $Template
     *            Template
     */
    public function __construct(\Mezon\HtmlTemplate\HtmlTemplate $Template)
    {
        parent::__construct();

        $this->Template = $Template;

        $this->Router->setNoProcessorFoundErrorHandler([
            $this,
            'noRouteFoundErrorHandler'
        ]);
    }

    /**
     * Method handles 404 errors
     *
     * @param string $Route
     * @codeCoverageIgnore
     */
    public function noRouteFoundErrorHandler(string $Route): void
    {
        $this->redirect_to('/404');
    }

    /**
     * Method renders common parts of all pages.
     *
     * @return array List of common parts.
     */
    public function crossRender(): array
    {
        return ([]);
    }

    /**
     * Formatting call stack
     *
     * @param mixed $e
     *            Exception object
     */
    protected function formatCallStack($e): array
    {
        $Stack = $e->getTrace();

        foreach ($Stack as $i => $Call) {
            $Stack[$i] = (@$Call['file'] == '' ? 'lambda : ' : @$Call['file'] . ' (' . $Call['line'] . ') : ') .
                (@$Call['class'] == '' ? '' : $Call['class'] . '->') . $Call['function'];
        }

        return ($Stack);
    }

    /**
     * Method formats exception object
     *
     * @param \Exception $e
     *            Exception
     * @return object Formatted exception object
     */
    protected function baseFormatter(\Exception $e): object
    {
        $Error = new \stdClass();
        $Error->message = $e->getMessage();
        $Error->code = $e->getCode();
        $Error->call_stack = $this->formatCallStack($e);
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
     * @param \Mezon\Service\ServiceRestTransport\RestException $e
     *            RestException object.
     */
    public function handleRestException(\Mezon\Service\ServiceRestTransport\RestException $e): void
    {
        $Error = $this->baseFormatter($e);

        $Error->http_body = $e->http_body;

        print('<pre>' . json_encode($Error, JSON_PRETTY_PRINT));
    }

    /**
     * Method processes exception.
     *
     * @param \Exception $e
     *            Exception object.
     */
    public function handleException(\Exception $e): void
    {
        $Error = $this->baseFormatter($e);

        print('<pre>' . json_encode($Error, JSON_PRETTY_PRINT));
    }

    /**
     * Running application.
     */
    public function run(): void
    {
        try {
            $CallRouteResult = $this->callRoute();
            if (is_array($CallRouteResult) === false) {
                throw (new \Exception('Route was not called properly'));
            }

            $Result = array_merge($CallRouteResult, $this->crossRender());

            if (is_array($Result)) {
                foreach ($Result as $Key => $Value) {
                    $Content = $Value instanceof \Mezon\Application\ViewInterface ? $Value->render() : $Value;

                    $this->Template->setPageVar($Key, $Content);
                }
            }

            print($this->Template->compile());
        } catch (\Mezon\Service\ServiceRestTransport\RestException $e) {
            $this->handleRestException($e);
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Getting template
     *
     * @return \Mezon\HtmlTemplate\HtmlTemplate Application's template
     */
    public function getRemplate(): \Mezon\HtmlTemplate\HtmlTemplate
    {
        return ($this->Template);
    }

    /**
     * Setting template
     *
     * @param mixed $Template
     *            Template
     */
    public function setTemplate($Template): void
    {
        $this->Template = $Template;
    }
}
