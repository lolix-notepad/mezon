<?php
namespace Mezon\Service;

/**
 * Class ServiceRestTransport
 *
 * @package Service
 * @subpackage ServiceRestTransport
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * REST transport for all services.
 *
 * @author Dodonov A.A.
 */
class ServiceRestTransport extends ServiceHttpTransport
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
    public function callLogic(ServiceBaseLogicInterface $ServiceLogic, string $Method, array $Params = [])
    {
        $this->header('Content-type', 'application/json');

        try {
            $Params['SessionId'] = $this->createSession();

            return (call_user_func_array([
                $ServiceLogic,
                $Method
            ], $Params));
        } catch (ServiceRestTransport\RestException $e) {
            return ($this->errorResponse($e));
        } catch (\Exception $e) {
            return (parent::errorResponse($e));
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
    public function callPublicLogic(ServiceBaseLogicInterface $ServiceLogic, string $Method, array $Params = [])
    {
        $this->header('Content-type', 'application/json');

        try {
            return (call_user_func_array([
                $ServiceLogic,
                $Method
            ], $Params));
        } catch (ServiceRestTransport\RestException $e) {
            return ($this->errorResponse($e));
        } catch (\Exception $e) {
            return (parent::errorResponse($e));
        }
    }

    /**
     * Method runs router.
     */
    public function run(): void
    {
        // @codeCoverageIgnoreStart
        print(json_encode($this->Router->callRoute($_GET['r'])));
        // @codeCoverageIgnoreEnd
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
        $Return = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'service' => 'service',
            'call_stack' => $this->formatCallStack($e),
            'host' => 'console'
        ];

        if ($e instanceof ServiceRestTransport\RestException) {
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
    public function handleException($e): void
    {
        // @codeCoverageIgnoreStart
        header('Content-type:application/json');

        print(json_encode($this->errorResponse($e)));
        // @codeCoverageIgnoreEnd
    }
}
