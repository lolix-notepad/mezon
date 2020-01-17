<?php
namespace Mezon\Router;

/**
 * Class Utils
 *
 * @package Router
 * @subpackage Utils
 * @author Dodonov A.A.
 * @version v.1.0 (2020/01/17)
 * @copyright Copyright (c) 2020, aeon.org
 */

/**
 * Router utilities class
 */
class Utils
{

    /**
     * Converting method name to route
     *
     * @param string $MethodName
     *            method name
     * @return string route
     */
    public static function convertMethodNameToRoute(string $MethodName): string
    {
        $MethodName = str_replace('action', '', $MethodName);

        if (ctype_upper($MethodName[0])) {
            $MethodName[0] = strtolower($MethodName[0]);
        }

        for ($i = 1; $i < strlen($MethodName); $i ++) {
            if (ctype_upper($MethodName[$i])) {
                $MethodName = substr_replace($MethodName, '-' . strtolower($MethodName[$i]), $i, 1);
            }
        }

        return $MethodName;
    }

    /**
     * Method prepares route for the next processing
     *
     * @param mixed $Route
     *            Route
     * @return string Trimmed route
     */
    public static function prepareRoute($Route): string
    {
        if (is_array($Route) && $Route[0] === '') {
            $Route = $_SERVER['REQUEST_URI'];
        }

        if ($Route == '/') {
            $Route = '/index/';
        }

        if (is_array($Route)) {
            $Route = implode('/', $Route);
        }

        return '/' . trim($Route, '/') . '/';
    }

    /**
     * Method compiles callable description
     *
     * @param mixed $Processor
     *            Object to be descripted
     * @return string Description
     */
    public static function getCallableDescription($Processor): string
    {
        if (is_string($Processor)) {
            return $Processor;
        } elseif (is_object($Processor[0])) {
            return get_class($Processor[0]) . '::' . $Processor[1];
        } else {
            return $Processor[0] . '::' . $Processor[1];
        }
    }
    
    /**
     * Method detects if the $String is a parameter or a static component of the route
     *
     * @param string $String
     *            String to be validated
     * @return bool Does we have parameter
     */
    public static function isParameter($String): bool
    {
        return $String[0] == '[' && $String[strlen($String) - 1] == ']';
    }
}