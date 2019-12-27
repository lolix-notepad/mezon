<?php
namespace Mezon;
/**
 * Class Mezon
 *
 * @package     Mezon
 * @subpackage  Mezon
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/17)
 * @copyright   Copyright (c) 2019, aeon.org
 */

/**
 * Mezon's main class.
 */
class Mezon
{

    /**
     * Connection to DB.
     */
    protected static $CRUD = false;

    /**
     * Method validates dsn fields
     * 
     * @param string $ConnectionName Connectio name
     */
    protected static function validateDsn(string $ConnectionName)
    {
        if (getConfigValue($ConnectionName . '/dsn') === false) {
            throw (new \Exception($ConnectionName . '/dsn not set'));
        }

        if (getConfigValue($ConnectionName . '/user') === false) {
            throw (new \Exception($ConnectionName . '/user not set'));
        }

        if (getConfigValue($ConnectionName . '/password') === false) {
            throw (new \Exception($ConnectionName . '/password not set'));
        }
    }

    /**
     * Method returns database connection
     * 
     * @param string $ConnectionName Connectio name
     */
    public static function getDbConnection(string $ConnectionName = 'default-db-connection')
    {
        if (self::$CRUD !== false) {
            return (self::$CRUD);
        }

        self::validateDsn($ConnectionName);

        self::$CRUD = new PDOCrud();

        self::$CRUD->connect([
            'dsn' => getConfigValue($ConnectionName . '/dsn'),
            'user' => getConfigValue($ConnectionName . '/user'),
            'password' => getConfigValue($ConnectionName . '/password')
        ]);

        return (self::$CRUD);
    }
}

?>