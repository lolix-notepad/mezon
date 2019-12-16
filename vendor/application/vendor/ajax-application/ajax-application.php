<?php
/**
 * Class AjaxApplication
 *
 * @package     Application
 * @subpackage  AjaxApplication
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/09/27)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../application.php');

/**
 * Base class of the ajax-application
 */
class AjaxApplication extends Application
{

    /**
     * Validating authorization for ajax requests
     *
     * Must be overriden in the base class
     */
    protected function validate_authorization_for_ajax_requests()
    {}

    /**
     * Method finishes ajax requests processing
     */
    protected function ajax_request_success()
    {
        print(json_encode([
            "code" => 0
        ]));

        exit(0);
    }

    /**
     * Method finishes ajax requests processing and returns result
     */
    protected function ajax_request_result($Result)
    {
        print(json_encode($Result));

        exit(0);
    }

    /**
     * Method finishes ajax requests processing
     *
     * @param string $Message
     *            Error message
     * @param int $Code
     *            Error code
     */
    protected function ajax_request_error(string $Message, int $Code = - 1)
    {
        print(json_encode([
            "message" => $Message,
            "code" => $Code
        ]));

        exit(0);
    }

    /**
     * Method processes exception.
     *
     * @param Exception $e
     *            Exception object.
     */
    public function handle_exception(Exception $e)
    {
        $Error = new stdClass();
        $Error->message = $e->getMessage();
        $Error->code = $e->getCode();
        if (isset($e->HTTPBody)) {
            $Error->http_body = $e->HTTPBody;
        }
        $Error->call_stack = $this->format_call_stack($e);
        $Error->host = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        
        print(json_encode($Error, JSON_PRETTY_PRINT));
    }
}

?>