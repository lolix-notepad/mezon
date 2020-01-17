<?php
namespace Mezon\Application;

/**
 * Class AjaxApplication
 *
 * @package Application
 * @subpackage AjaxApplication
 * @author Dodonov A.A.
 * @version v.1.0 (2019/09/27)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Base class of the ajax-application
 */
class AjaxApplication extends \Mezon\Application\Application
{

    /**
     * Validating authorization for ajax requests
     *
     * Must be overriden in the base class
     */
    protected function validateAuthorizationForAjaxRequests()
    {}

    /**
     * Method finishes ajax requests processing
     */
    protected function ajaxRequestSuccess()
    {
        print(json_encode([
            "code" => 0
        ]));

        exit(0);
    }

    /**
     * Method finishes ajax requests processing and returns result
     */
    protected function ajaxRequestResult($result)
    {
        print(json_encode($result));

        exit(0);
    }

    /**
     * Method finishes ajax requests processing
     *
     * @param string $message
     *            Error message
     * @param int $code
     *            Error code
     */
    protected function ajaxRequestError(string $message, int $code = - 1)
    {
        print(json_encode([
            "message" => $message,
            "code" => $code
        ]));

        exit(0);
    }

    /**
     * Method processes exception.
     *
     * @param \Exception $e
     *            Exception object.
     */
    public function handleException(\Exception $e): void
    {
        $error = new \stdClass();
        $error->message = $e->getMessage();
        $error->code = $e->getCode();
        if (isset($e->HTTPBody)) {
            $error->httpBody = $e->HTTPBody;
        }
        $error->call_stack = $this->formatCallStack($e);
        $error->host = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        print(json_encode($error, JSON_PRETTY_PRINT));
    }
}
