<?php
namespace Mezon\Service;
/**
 * Class ServiceRESTTransport
 *
 * @package     Service
 * @subpackage  ServiceRESTTransport
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/17)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../service-http-transport/vendor/http-request-params/http-request-params.php');
require_once (__DIR__ . '/../service-http-transport/service-http-transport.php');

require_once (__DIR__ . '/vendor/rest-exception/rest-exception.php');

// TODO add camel-case
/**
 * REST transport for all services.
 *
 * @author Dodonov A.A.
 */
class ServiceRESTTransport extends ServiceHTTPTransport
{

    /**
     * Method runs logic functions.
     *
     * @param ServiceBaseLogicInterface $ServiceLogic
     *            -
     *            object with all service logic.
     * @param string $Method
     *            -
     *            logic's method to be executed.
     * @param array $Params
     *            -
     *            logic's parameters.
     * @return mixed Result of the called method.
     */
    public function call_logic(ServiceBaseLogicInterface $ServiceLogic, string $Method, array $Params = [])
    {
        $this->header('Content-type', 'application/json');

        try {
            $Params['SessionId'] = $this->create_session();

            return (call_user_func_array([
                $ServiceLogic,
                $Method
            ], $Params));
        } catch (ServiceRESTTransport\RESTException $e) {
            return ($this->error_response($e));
        } catch (\Exception $e) {
            return (parent::error_response($e));
        }
    }

    /**
     * Method runs logic functions.
     *
     * @param ServiceBaseLogicInterface $ServiceLogic
     *            -
     *            object with all service logic.
     * @param string $Method
     *            -
     *            logic's method to be executed.
     * @param array $Params
     *            -
     *            logic's parameters.
     * @return mixed Result of the called method.
     */
    public function call_public_logic(ServiceBaseLogicInterface $ServiceLogic, string $Method, array $Params = [])
    {
        $this->header('Content-type', 'application/json');

        try {
            return (call_user_func_array([
                $ServiceLogic,
                $Method
            ], $Params));
        } catch (ServiceRESTTransport\RESTException $e) {
            return ($this->error_response($e));
        } catch (\Exception $e) {
            return (parent::error_response($e));
        }
    }

    /**
     * Method runs router.
     */
    public function run(): void
    {
        // @codeCoverageIgnoreStart
        print(json_encode($this->Router->call_route($_GET['r'])));
        // @codeCoverageIgnoreEnd
    }

    /**
     * Error response compilator
     *
     * @param mixed $e
     *            Exception object
     * @return array Error data
     */
    public function error_response($e): array
    {
        $Return = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'service' => 'service',
            'call_stack' => $this->format_call_stack($e),
            'host' => 'console'
        ];

        if ($e instanceof ServiceRESTTransport\RESTException) {
            $Return['http_code'] = $e->getHttpCode();
            $Return['http_body'] = $e->getHttpBody();
        }

        return ($Return);
    }

    /**
     * Method processes exception
     *
     * @param $e \Exception
     *            object
     */
    public function handle_exception($e):void
    {
        // @codeCoverageIgnoreStart
        header('Content-type:application/json');

        print(json_encode($this->error_response($e)));
        // @codeCoverageIgnoreEnd
    }
}

?>