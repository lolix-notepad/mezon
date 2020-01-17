<?php
namespace Mezon\Mezon;

/**
 * Class Mezon
 *
 * @package Mezon
 * @subpackage Mezon
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Mezon's main class.
 */
class Mezon
{

    /**
     * Connection to DB.
     */
    protected static $crud = false;

    /**
     * Method validates dsn fields
     *
     * @param string $connectionName
     *            Connectio name
     */
    protected static function validateDsn(string $connectionName)
    {
        if (\Mezon\Conf\Conf::getConfigValue($connectionName . '/dsn') === false) {
            throw (new \Exception($connectionName . '/dsn not set'));
        }

        if (\Mezon\Conf\Conf::getConfigValue($connectionName . '/user') === false) {
            throw (new \Exception($connectionName . '/user not set'));
        }

        if (\Mezon\Conf\Conf::getConfigValue($connectionName . '/password') === false) {
            throw (new \Exception($connectionName . '/password not set'));
        }
    }

    /**
     * Contructing connection to database object
     *
     * @return \Mezon\PdoCrud\PdoCrud connection object wich is no initialized
     */
    protected static function constructConnection(): \Mezon\PdoCrud\PdoCrud
    {
        return new \Mezon\PdoCrud\PdoCrud();
    }

    /**
     * Method returns database connection
     *
     * @param string $connectionName
     *            Connectio name
     */
    public static function getDbConnection(string $connectionName = 'default-db-connection')
    {
        if (self::$crud !== false) {
            return self::$crud;
        }

        self::validateDsn($connectionName);

        self::$crud = self::constructConnection();

        self::$crud->connect(
            [
                'dsn' => \Mezon\Conf\Conf::getConfigValue($connectionName . '/dsn'),
                'user' => \Mezon\Conf\Conf::getConfigValue($connectionName . '/user'),
                'password' => \Mezon\Conf\Conf::getConfigValue($connectionName . '/password')
            ]);

        return self::$crud;
    }
}
