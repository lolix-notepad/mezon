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
require_once (__DIR__ . '/../conf/conf.php');
require_once (__DIR__ . '/../pdo-crud/pdo-crud.php');


// TODO add camel-case

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
    protected static function validate_dsn(string $ConnectionName)
    {
        if (get_config_value($ConnectionName . '/dsn') === false) {
            throw (new \Exception($ConnectionName . '/dsn not set'));
        }

        if (get_config_value($ConnectionName . '/user') === false) {
            throw (new \Exception($ConnectionName . '/user not set'));
        }

        if (get_config_value($ConnectionName . '/password') === false) {
            throw (new \Exception($ConnectionName . '/password not set'));
        }
    }

    /**
     * Method returns database connection
     * 
     * @param string $ConnectionName Connectio name
     */
    public static function get_db_connection(string $ConnectionName = 'default-db-connection')
    {
        if (self::$CRUD !== false) {
            return (self::$CRUD);
        }

        self::validate_dsn($ConnectionName);

        self::$CRUD = new PDOCrud();

        self::$CRUD->connect([
            'dsn' => get_config_value($ConnectionName . '/dsn'),
            'user' => get_config_value($ConnectionName . '/user'),
            'password' => get_config_value($ConnectionName . '/password')
        ]);

        return (self::$CRUD);
    }
}

?>