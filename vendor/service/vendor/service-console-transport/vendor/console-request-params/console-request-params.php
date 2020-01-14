<?php
namespace Mezon\Service\ServiceConsoleTransport\ConsoleRequestParams;

/**
 * Class ConsoleRequestParams
 *
 * @package ServiceConsoleTransport
 * @subpackage ConsoleRequestParams
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/12)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Request params fetcher
 */
class ConsoleRequestParams implements \Mezon\Service\ServiceRequestParamsInterface
{

    /**
     * Method returns session id from HTTP header.
     *
     * @return string Session id.
     */
    protected function getSessionId()
    {
        return ('');
    }

    /**
     * Method returns parameter.
     *
     * @param string $Param
     *            - parameter name.
     * @param mixed $Default
     *            - default value.
     * @return string Parameter value.
     */
    public function getParam($Param, $Default = false)
    {
        global $argv;

        if (isset($argv[$Param])) {
            return ($argv[$Param]);
        }

        return ($Default);
    }
}
