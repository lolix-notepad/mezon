<?php

/**
 * Class DNS
 *
 * @package     Mezon
 * @subpackage  DNS
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/15)
 * @copyright   Copyright (c) 2019, aeon.org
 */

/**
 * DNS class for fetching data about services location
 * 
 * For example the call DNS::resolve_host('example') will return URL of the example service
 */
class DNS
{

	/**
	 * Method returns list of available services.
	 */
	public static function get_services()
	{
		global $DNSRecords;

		return (implode(', ', array_keys($DNSRecords)));
	}

	/**
	 * Method returns true if the service was defined.
	 *
	 * @param string $ServiceName
	 *        	Service name
	 */
	public static function service_exists($ServiceName)
	{
		global $DNSRecords;

		return (isset($DNSRecords[$ServiceName]));
	}

	/**
	 * Method resolves host
	 *
	 * @param string $ServiceName
	 *        	Service name
	 */
	public static function resolve_host($ServiceName)
	{
		global $DNSRecords;

		if (! isset($DNSRecords[$ServiceName])) {
			throw (new Exception('Service "' . $ServiceName . '" was not found among services: ' . self::get_services(), - 1));
		}

		if (is_string($DNSRecords[$ServiceName])) {
			return ($DNSRecords[$ServiceName]);
		} else {
			throw (new Exception('Invalid URL "' . serialize($DNSRecords[$ServiceName]) . '"', - 1));
		}
	}
}

?>