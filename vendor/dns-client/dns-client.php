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
class DnsClient
{

    /**
     * Method returns list of available services
     *
     * @return string List of services
     */
    public static function getServices(): string
    {
        global $DNSRecords;

        return (implode(', ', array_keys($DNSRecords)));
    }

    /**
     * Method returns true if the service was defined.
     *
     * @param string $ServiceName
     *            Service name
     *            return bool Does service exists
     */
    public static function serviceExists(string $ServiceName): bool
    {
        global $DNSRecords;

        return (isset($DNSRecords[$ServiceName]));
    }

    /**
     * Method resolves host
     *
     * @param string $ServiceName
     *            Service name
     * @return string Service URL
     */
    public static function resolveHost(string $ServiceName): string
    {
        global $DNSRecords;

        if (! isset($DNSRecords[$ServiceName])) {
            throw (new \Exception(
                'Service "' . $ServiceName . '" was not found among services: ' . self::getServices(),
                - 1));
        }

        if (is_string($DNSRecords[$ServiceName])) {
            return ($DNSRecords[$ServiceName]);
        } else {
            throw (new \Exception('Invalid URL "' . serialize($DNSRecords[$ServiceName]) . '"', - 1));
        }
    }

    /**
     * Method saves service URL
     *
     * @param string $ServiceName
     *            Service name
     * @param string $ServiceUrl
     *            Service URL
     */
    public static function setService(string $ServiceName, string $ServiceUrl): void
    {
        global $DNSRecords;

        $DNSRecords[$ServiceName] = $ServiceUrl;
    }
}
