<?php
namespace Mezon;

/**
 * Class PdoCrud
 *
 * @package Mezon
 * @subpackage PdoCrud
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/16)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Class provides simple CRUD operations
 */
class PdoCrud
{

    /**
     * PDO object
     */
    protected $PDO = false;

    /**
     * Method connects to the database
     *
     * @param array $ConnnectionData
     *            Connection settings
     * @codeCoverageIgnore
     */
    public function connect(array $ConnnectionData)
    {
        // no need to test this single string. assume that PDO developers did it
        $this->PDO = new \PDO($ConnnectionData['dsn'], $ConnnectionData['user'], $ConnnectionData['password']);

        $this->query('SET NAMES utf8');
    }

    /**
     * Method handles request errors
     *
     * @param mixed $Result
     *            Query result
     * @param string $Query
     *            SQL Query
     */
    protected function processQueryError($Result, string $Query)
    {
        if ($Result === false) {
            $ErrorInfo = $this->PDO->errorInfo();

            throw (new \Exception($ErrorInfo[2] . ' in statement ' . $Query));
        }
    }

    /**
     * Getting records
     *
     * @param string $Fields
     *            List of fields
     * @param string $TableNames
     *            List of tables
     * @param string $Where
     *            Condition
     * @param int $From
     *            First record in query
     * @param int $Limit
     *            Count of records
     * @return array List of records
     */
    public function select(
        string $Fields,
        string $TableNames,
        string $Where = '1 = 1',
        int $From = 0,
        int $Limit = 1000000): array
    {
        $Query = "SELECT $Fields FROM $TableNames WHERE $Where LIMIT " . intval($From) . ' , ' . intval($Limit);

        $Result = $this->query($Query);

        $this->processQueryError($Result, $Query);

        return ($Result->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * Method compiles set-query
     *
     * @param array $Record
     *            Inserting record
     * @return string Compiled query string
     */
    protected function setQuery(array $Record): string
    {
        $SetFieldsStatement = [];

        foreach ($Record as $Field => $Value) {
            if (is_string($Value) && strtoupper($Value) === 'INC') {
                $SetFieldsStatement[] = $Field . ' = ' . $Field . ' + 1';
            } elseif (is_string($Value) && strtoupper($Value) !== 'NOW()') {
                $SetFieldsStatement[] = $Field . ' = "' . $Value . '"';
            } elseif ($Value === null) {
                $SetFieldsStatement[] = $Field . ' = NULL';
            } else {
                $SetFieldsStatement[] = $Field . ' = ' . $Value;
            }
        }

        return (implode(' , ', $SetFieldsStatement));
    }

    /**
     * Method compiles set-multyple-query
     *
     * @param array $Records
     *            Inserting records
     * @return string Compiled query string
     */
    protected function setMultypleQuery(array $Records)
    {
        $Query = '( ' . implode(' , ', array_keys($Records[0])) . ' ) VALUES ';

        $Values = [];

        foreach ($Records as $Record) {
            $Values[] = "( '" . implode("' , '", array_values($Record)) . "' )";
        }

        return ($Query . implode(' , ', $Values));
    }

    /**
     * Updating records
     *
     * @param string $TableName
     *            Table name
     * @param array $Record
     *            Updating records
     * @param string $Where
     *            Condition
     * @param int $Limit
     *            Liti for afffecting records
     * @return int Count of updated records
     */
    public function update(string $TableName, array $Record, string $Where, int $Limit = 10000000)
    {
        $Query = 'UPDATE ' . $TableName . ' SET ' . $this->setQuery($Record) . ' WHERE ' . $Where . ' LIMIT ' . $Limit;

        $Result = $this->query($Query);

        $this->processQueryError($Result, $Query);

        return ($Result->rowCount());
    }

    /**
     * Deleting records
     *
     * @param string $TableName
     *            Table name
     * @param string $Where
     *            Condition
     * @param int $Limit
     *            Liti for afffecting records
     * @return int Count of deleted records
     */
    public function delete($TableName, $Where, $Limit = 10000000)
    {
        $Query = 'DELETE FROM ' . $TableName . ' WHERE ' . $Where . ' LIMIT ' . intval($Limit);

        $Result = $this->query($Query);

        $this->processQueryError($Result, $Query);

        return ($Result->rowCount());
    }

    /**
     * Method compiles lock queries
     *
     * @param array $Tables
     *            List of tables
     * @param array $Modes
     *            List of lock modes
     * @return string Query
     */
    protected function lockQuery(array $Tables, array $Modes): string
    {
        $Query = [];

        foreach ($Tables as $i => $Table) {
            $Query[] = $Table . ' ' . $Modes[$i];
        }

        $Query = 'LOCK TABLES ' . implode(' , ', $Query);

        return ($Query);
    }

    /**
     * Method locks tables
     *
     * @param array $Tables
     *            List of tables
     * @param array $Modes
     *            List of lock modes
     */
    public function lock(array $Tables, array $Modes)
    {
        $Query = $this->lockQuery($Tables, $Modes);

        $Result = $this->query($Query);

        $this->processQueryError($Result, $Query);
    }

    /**
     * Method unlocks locked tables
     */
    public function unlock()
    {
        $Result = $this->query('UNLOCK TABLES');

        $this->processQueryError($Result, 'UNLOCK TABLES');
    }

    /**
     * Method starts transaction
     */
    public function startTransaction()
    {
        // setting autocommit off
        $Result = $this->query('SET AUTOCOMMIT = 0');

        $this->processQueryError($Result, 'SET AUTOCOMMIT = 0');

        // starting transaction
        $Result = $this->query('START TRANSACTION');

        $this->processQueryError($Result, 'START TRANSACTION');
    }

    /**
     * Commiting transaction
     */
    public function commit()
    {
        // commit transaction
        $Result = $this->query('COMMIT');

        $this->processQueryError($Result, 'COMMIT');

        // setting autocommit on
        $Result = $this->query('SET AUTOCOMMIT = 1');

        $this->processQueryError($Result, 'SET AUTOCOMMIT = 1');
    }

    /**
     * Rollback transaction
     */
    public function rollback()
    {
        // rollback transaction
        $Result = $this->query('ROLLBACK');

        $this->processQueryError($Result, 'ROLLBACK');
    }

    /**
     * Method executes query
     *
     * @param string $Query
     *            Query
     * @return mixed Query execution result
     */
    public function query(string $Query)
    {
        // @codeCoverageIgnoreStart
        return ($this->PDO->query($Query));
        // @codeCoverageIgnoreEnd
    }

    /**
     * Method returns id of the last inserted record
     *
     * @return string id of the last inserted record
     */
    public function lastInsertId()
    {
        // @codeCoverageIgnoreStart
        return ($this->PDO->lastInsertId());
        // @codeCoverageIgnoreEnd
    }

    /**
     * Method inserts record
     *
     * @param string $TableName
     *            Table name
     * @param array $Record
     *            Inserting record
     * @return int New record's id
     */
    public function insert(string $TableName, array $Record): int
    {
        $Query = 'INSERT ' . $TableName . ' SET ' . $this->setQuery($Record);

        $Result = $this->query($Query);

        $this->processQueryError($Result, $Query);

        return ($this->lastInsertId());
    }

    /**
     * Method inserts record
     *
     * @param string $TableName
     *            Table name
     * @param array $Records
     *            Inserting records
     * @return int New record's id
     */
    public function insertMultyple(string $TableName, array $Records)
    {
        $Query = 'INSERT INTO ' . $TableName . ' ' . $this->setMultypleQuery($Records) . ';';

        $Result = $this->query($Query);

        $this->processQueryError($Result, $Query);

        return (0);
    }

    /**
     * Method destroys connection
     */
    public function __destruct()
    {
        $this->PDO = null;

        unset($this->PDO);
    }
}
