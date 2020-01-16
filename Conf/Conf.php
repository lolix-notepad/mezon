<?php
namespace Mezon\Conf;

/**
 * Configuration routines
 *
 * @package Mezon
 * @subpackage Conf
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/07)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Config data
 *
 * @author Dodonov A.A.
 */
class Conf
{

    /**
     * Built-in constants
     */
    public const APP_HTTP_PATH_STRING = '@app-http-path';

    /**
     * Built-in constants
     */
    public const MEZON_HTTP_PATH_STRING = '@mezon-http-path';

    /**
     * Config data
     *
     * @var array
     */
    public static $AppConfig = [];

    /**
     * Function returns specified config key
     * If the key does not exists then $DefaultValue will be returned
     *
     * @param string $Route
     *            Key route in config
     * @param mixed $DefaultValue
     *            Default value if the key was not found
     * @return mixed Key value
     */
    public static function getConfigValue($Route, $DefaultValue = false)
    {
        return (\Mezon\Conf\getConfigValue($Route, $DefaultValue));
    }

    /**
     * Function sets specified config key with value $Value
     *
     * @param array $Route
     *            Route to key
     * @param mixed $Value
     *            Value to be set
     */
    public static function setConfigValue($Route, $Value)
    {
        \Mezon\Conf\setConfigValue($Route, $Value);
    }

    /**
     * Function adds specified value $Value into array with path $Route in the config
     *
     * @param string $Route
     *            Route to key
     * @param mixed $Value
     *            Value to be set
     */
    public static function addConfigValue(string $Route, $Value)
    {
        \Mezon\Conf\addConfigValue($Route, $Value);
    }

    /**
     * Validating key existance
     *
     * @param mixed $Route
     *            Route to key
     * @return bool True if the key exists, false otherwise
     */
    public static function configKeyExists($Route): bool
    {
        return (\Mezon\Conf\configKeyExists($Route));
    }

    /**
     * Deleting config value
     *
     * @param mixed $Route
     *            Route to key
     * @return bool True if the key was deleted, false otherwise
     */
    public static function deleteConfigValue($Route): bool
    {
        return (\Mezon\Conf\deleteConfigValue($Route));
    }

    /**
     * Method sets connection details to config
     *
     * @param string $Name
     *            Config key
     * @param string $DSN
     *            DSN
     * @param string $User
     *            DB User login
     * @param string $Password
     *            DB User password
     */
    public static function addConnectionToConfig(string $Name, string $DSN, string $User, string $Password)
    {
        \Mezon\Conf\addConnectionToConfig($Name, $DSN, $User, $Password);
    }

    /**
     * Method expands string
     *
     * @param string $Value
     *            value to be expanded;
     * @return mixed Expanded value.
     */
    public static function expandString($Value)
    {
        return (\Mezon\Conf\expandString($Value));
    }
}

/**
 * Method expands string
 *
 * @param string $Value
 *            value to be expanded;
 * @return mixed Expanded value.
 */
function expandString($Value)
{
    if (is_string($Value)) {
        $Value = str_replace([
            \Mezon\Conf\Conf::APP_HTTP_PATH_STRING,
            \Mezon\Conf\Conf::MEZON_HTTP_PATH_STRING
        ], [
            @Conf::$AppConfig[\Mezon\Conf\Conf::APP_HTTP_PATH_STRING],
            @Conf::$AppConfig[\Mezon\Conf\Conf::MEZON_HTTP_PATH_STRING]
        ], $Value);
    } elseif (is_array($Value)) {
        foreach ($Value as $FieldName => $FieldValue) {
            $Value[$FieldName] = expandString($FieldValue);
        }
    } elseif (is_object($Value)) {
        foreach ($Value as $FieldName => $FieldValue) {
            $Value->$FieldName = expandString($FieldValue);
        }
    }

    return ($Value);
}

/**
 * Function returns specified config key
 * If the key does not exists then $DefaultValue will be returned
 *
 * @param string $Route
 *            Key route in config
 * @param mixed $DefaultValue
 *            Default value if the key was not found
 * @return mixed Key value
 */
function getConfigValue($Route, $DefaultValue = false)
{
    if (is_string($Route)) {
        $Route = explode('/', $Route);
    }

    if (isset(Conf::$AppConfig[$Route[0]]) === false) {
        return ($DefaultValue);
    }

    $Value = Conf::$AppConfig[$Route[0]];

    for ($i = 1; $i < count($Route); $i ++) {
        if (isset($Value[$Route[$i]]) === false) {
            return ($DefaultValue);
        }

        $Value = $Value[$Route[$i]];
    }

    return (expandString($Value));
}

/**
 * Setting config value
 *
 * @param array $Config
 *            Config values
 * @param array $Route
 *            Route to key
 * @param mixed $Value
 *            Value to be set
 */
function _setConfigValueRec(array &$Config, array $Route, $Value)
{
    if (isset($Config[$Route[0]]) === false) {
        $Config[$Route[0]] = [];
    }

    if (count($Route) > 1) {
        _setConfigValueRec($Config[$Route[0]], array_slice($Route, 1), $Value);
    } elseif (count($Route) == 1) {
        $Config[$Route[0]] = $Value;
    }
}

/**
 * Function sets specified config key with value $Value
 *
 * @param array $Route
 *            Route to key
 * @param mixed $Value
 *            Value to be set
 */
function setConfigValue($Route, $Value)
{
    $Route = explode('/', $Route);

    if (count($Route) > 1) {
        _setConfigValueRec(Conf::$AppConfig, $Route, $Value);
    } else {
        Conf::$AppConfig[$Route[0]] = $Value;
    }
}

/**
 * Additing value
 *
 * @param array $Config
 *            Config values
 * @param array $Route
 *            Route to key
 * @param mixed $Value
 *            Value to be set
 */
function _addConfigValueRec(array &$Config, array $Route, $Value)
{
    if (isset($Config[$Route[0]]) === false) {
        $Config[$Route[0]] = [];
    }

    if (count($Route) > 1) {
        _addConfigValueRec($Config[$Route[0]], array_slice($Route, 1), $Value);
    } elseif (count($Route) == 1) {
        $Config[$Route[0]][] = $Value;
    }
}

/**
 * Function adds specified value $Value into array with path $Route in the config
 *
 * @param string $Route
 *            Route to key
 * @param mixed $Value
 *            Value to be set
 */
function addConfigValue(string $Route, $Value)
{
    $Route = explode('/', $Route);

    if (count($Route) > 1) {
        _addConfigValueRec(Conf::$AppConfig, $Route, $Value);
    } else {
        Conf::$AppConfig[$Route[0]] = [
            $Value
        ];
    }
}

/**
 * Validating key existance
 *
 * @param mixed $Route
 *            Route to key
 * @return bool True if the key exists, false otherwise
 */
function configKeyExists($Route): bool
{
    if (is_string($Route)) {
        $Route = explode('/', $Route);
    }

    // validating route
    $Value = Conf::$AppConfig[$Route[0]];

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
 *            Route parts
 * @param array $ConfigPart
 *            Config part
 */
function _deleteConfig(array $RouteParts, array &$ConfigPart)
{
    if (count($RouteParts) == 1) {
        // don't go deeper and delete the found staff
        unset($ConfigPart[$RouteParts[0]]);
    } else {
        // go deeper
        _deleteConfig(array_splice($RouteParts, 1), $ConfigPart[$RouteParts[0]]);

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
 *            Route to key
 * @return bool True if the key was deleted, false otherwise
 */
function deleteConfigValue($Route): bool
{
    if (is_string($Route)) {
        $Route = explode('/', $Route);
    }

    if (configKeyExists($Route) === false) {
        return (false);
    }

    // route exists, so delete it
    _deleteConfig($Route, Conf::$AppConfig);

    return (true);
}

/**
 * Method sets connection details to config
 *
 * @param string $Name
 *            Config key
 * @param string $DSN
 *            DSN
 * @param string $User
 *            DB User login
 * @param string $Password
 *            DB User password
 */
function addConnectionToConfig(string $Name, string $DSN, string $User, string $Password)
{
    setConfigValue($Name . '/dsn', $DSN);
    setConfigValue($Name . '/user', $User);
    setConfigValue($Name . '/password', $Password);
}
