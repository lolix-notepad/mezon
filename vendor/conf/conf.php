<?php
/**
 * Configuration routines
 * 
 * @package     Mezon
 * @subpackage  Conf
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/07)
 * @copyright   Copyright (c) 2019, aeon.org
 */
define('APP_HTTP_PATH_STRING', '@app-http-path');
define('MEZON_HTTP_PATH_STRING', '@mezon-http-path');

/**
 * Method expands string
 *
 * @param string $Value
 *        	value to be expanded;
 * @return mixed Expanded value.
 */
function _expand_string($Value)
{
	global $AppConfig;

	if (is_string($Value)) {
		$Value = str_replace([
			APP_HTTP_PATH_STRING,
			MEZON_HTTP_PATH_STRING
		], [
			@$AppConfig[APP_HTTP_PATH_STRING],
			@$AppConfig[MEZON_HTTP_PATH_STRING]
		], $Value);
	} elseif (is_array($Value)) {
		foreach ($Value as $FieldName => $FieldValue) {
			$Value[$FieldName] = _expand_string($FieldValue);
		}
	} elseif (is_object($Value)) {
		foreach ($Value as $FieldName => $FieldValue) {
			$Value->$FieldName = _expand_string($FieldValue);
		}
	}

	return ($Value);
}

/**
 * Function returns specified config key
 * If the key does not exists then $DefaultValue will be returned
 *
 * @param string $Route
 *        	Key route in config
 * @param mixed $DefaultValue
 *        	Default value if the key was not found
 * @return mixed Key value
 */
function get_config_value($Route, $DefaultValue = false)
{
	global $AppConfig;

	if (is_string($Route)) {
		$Route = explode('/', $Route);
	}

	if (isset($AppConfig[$Route[0]]) === false) {
		return ($DefaultValue);
	}

	$Value = $AppConfig[$Route[0]];

	for ($i = 1; $i < count($Route); $i ++) {
		if (isset($Value[$Route[$i]]) === false) {
			return ($DefaultValue);
		}

		$Value = $Value[$Route[$i]];
	}

	return (_expand_string($Value));
}

/**
 * Setting config value
 *
 * @param array $Config
 *        	Config values
 * @param array $Route
 *        	Route to key
 * @param mixed $Value
 *        	Value to be set
 */
function _set_config_value_rec(array &$Config, array $Route, $Value)
{
	if (isset($Config[$Route[0]]) === false) {
		$Config[$Route[0]] = [];
	}

	if (count($Route) > 1) {
		_set_config_value_rec($Config[$Route[0]], array_slice($Route, 1), $Value);
	} elseif (count($Route) == 1) {
		$Config[$Route[0]] = $Value;
	}
}

/**
 * Function sets specified config key with value $Value
 *
 * @param array $Route
 *        	Route to key
 * @param mixed $Value
 *        	Value to be set
 */
function set_config_value($Route, $Value)
{
	global $AppConfig;

	$Route = explode('/', $Route);

	if (count($Route) > 1) {
		_set_config_value_rec($AppConfig, $Route, $Value);
	} else {
		$AppConfig[$Route[0]] = $Value;
	}
}

/**
 * Additing value
 *
 * @param array $Config
 *        	Config values
 * @param array $Route
 *        	Route to key
 * @param mixed $Value
 *        	Value to be set
 */
function _add_config_value_rec(array &$Config, array $Route, $Value)
{
	if (isset($Config[$Route[0]]) === false) {
		$Config[$Route[0]] = [];
	}

	if (count($Route) > 1) {
		_add_config_value_rec($Config[$Route[0]], array_slice($Route, 1), $Value);
	} elseif (count($Route) == 1) {
		$Config[$Route[0]][] = $Value;
	}
}

/**
 * Function adds specified value $Value into array with path $Route in the config
 *
 * @param string $Route
 *        	Route to key
 * @param mixed $Value
 *        	Value to be set
 */
function add_config_value(string $Route, $Value)
{
	global $AppConfig;

	$Route = explode('/', $Route);

	if (count($Route) > 1) {
		_add_config_value_rec($AppConfig, $Route, $Value);
	} else {
		$AppConfig[$Route[0]] = [
			$Value
		];
	}
}

/**
 * Validating key existance
 *
 * @param mixed $Route
 *        	Route to key
 * @return bool True if the key exists, false otherwise
 */
function config_key_exists($Route): bool
{
	global $AppConfig;

	if (is_string($Route)) {
		$Route = explode('/', $Route);
	}

	// validating route
	$Value = $AppConfig[$Route[0]];

	for ($i = 1; $i < count($Route); $i ++) {
		if (isset($Value[$Route[$i]]) === false) {
			return (false);
		}

		$Value = $Value[$Route[$i]];
	}

	return (true);
}

/**
 * Deleting config element
 *
 * @param array $RouteParts
 *        	Route parts
 * @param array $ConfigPart
 *        	Config part
 */
function _delete_config(array $RouteParts, array &$ConfigPart)
{
	if (count($RouteParts) == 1) {
		// don't go deeper and delete the found staff
		unset($ConfigPart[$RouteParts[0]]);
	} else {
		// go deeper
		_delete_config(array_splice($RouteParts, 1), $ConfigPart[$RouteParts[0]]);

		if (count($ConfigPart[$RouteParts[0]]) == 0) {
			// remove empty parents
			unset($ConfigPart[$RouteParts[0]]);
		}
	}
}

/**
 * Deleting config value
 *
 * @param mixed $Route
 *        	Route to key
 */
function delete_config_value($Route)
{
	global $AppConfig;

	if (is_string($Route)) {
		$Route = explode('/', $Route);
	}

	if (config_key_exists($Route) === false) {
		return (false);
	}

	// route exists, so delete it
	_delete_config($Route, $AppConfig);

	return (true);
}

/**
 * Method sets connection details to config
 *
 * @param string $Name
 *        	Config key
 * @param string $DSN
 *        	DSN
 * @param string $User
 *        	DB User login
 * @param string $Password
 *        	DB User password
 */
function add_connection_to_config(string $Name, string $DSN, string $User, string $Password)
{
	set_config_value($Name . '/dsn', $DSN);
	set_config_value($Name . '/user', $User);
	set_config_value($Name . '/password', $Password);
}

set_config_value(APP_HTTP_PATH_STRING, 'http://' . @$_SERVER['HTTP_HOST'] . '/' . trim(@$_SERVER['REQUEST_URI'], '/'));
set_config_value(MEZON_HTTP_PATH_STRING, 'http://' . @$_SERVER['HTTP_HOST'] . '/' . trim(@$_SERVER['REQUEST_URI'], '/'));

set_config_value('res/images/favicon', '@mezon-http-path/res/images/favicon.ico');

add_config_value('res/css', '@mezon-http-path/res/css/application.css');

?>