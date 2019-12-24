<?php
namespace Mezon;

/**
 * Class DNS
 *
 * @package Mezon
 * @subpackage DNS
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/15)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * DNS class for fetching data about services location
 *
 * For example the call DNS::resolveHost('example') will return URL of the example service
 */
class DNS
{

    /**
     * Method returns list of available services.
     */
    public static function getServices()
    {
        global $DNSRecords;

        return (implode(', ', array_keys($DNSRecords)));
    }

    /**
     * Method returns true if the service was defined.
     *
     * @param string $ServiceName
     *            Service name
     */
    public static function serviceExists($ServiceName)
    {
        global $DNSRecords;

        return (isset($DNSRecords[$ServiceName]));
    }

    /**
     * Method resolves host
     *
     * @param string $ServiceName
     *            Service name
     */
    public static function resolveHost($ServiceName)
    {
        global $DNSRecords;

        if (! isset($DNSRecords[$ServiceName])) {
            throw (new \Exception('Service "' . $ServiceName . '" was not found among services: ' . self::getServices(), - 1));
        }

        if (is_string($DNSRecords[$ServiceName])) {
            return ($DNSRecords[$ServiceName]);
        } else {
            throw (new \Exception('Invalid URL "' . serialize($DNSRecords[$ServiceName]) . '"', - 1));
        }
    }
}

?>