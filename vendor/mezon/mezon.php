<?php
namespace Mezon;

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
    protected static $Crud = false;

    /**
     * Method validates dsn fields
     *
     * @param string $ConnectionName
     *            Connectio name
     */
    protected static function validateDsn(string $ConnectionName)
    {
        if (\Mezon\Conf::getConfigValue($ConnectionName . '/dsn') === false) {
            throw (new \Exception($ConnectionName . '/dsn not set'));
        }

        if (\Mezon\Conf::getConfigValue($ConnectionName . '/user') === false) {
            throw (new \Exception($ConnectionName . '/user not set'));
        }

        if (\Mezon\Conf::getConfigValue($ConnectionName . '/password') === false) {
            throw (new \Exception($ConnectionName . '/password not set'));
        }
    }

    /**
     * Contructing connection to database object
     *
     * @return \Mezon\PdoCrud connection object wich is no initialized
     */
    protected static function constructConnection(): \Mezon\PdoCrud
    {
        print('NOT MOCKED!!!!!!!!!');
        return (new \Mezon\PdoCrud());
    }

    /**
     * Method returns database connection
     *
     * @param string $ConnectionName
     *            Connectio name
     */
    public static function getDbConnection(string $ConnectionName = 'default-db-connection')
    {
        if (self::$Crud !== false) {
            return (self::$Crud);
        }

        self::validateDsn($ConnectionName);

        self::$Crud = self::constructConnection();

        self::$Crud->connect(
            [
                'dsn' => \Mezon\Conf::getConfigValue($ConnectionName . '/dsn'),
                'user' => \Mezon\Conf::getConfigValue($ConnectionName . '/user'),
                'password' => \Mezon\Conf::getConfigValue($ConnectionName . '/password')
            ]);

        return (self::$Crud);
    }
}
