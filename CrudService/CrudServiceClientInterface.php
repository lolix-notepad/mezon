<?php
namespace Mezon\CrudService;

/**
 * Interface CrudServiceClientInterface
 *
 * @package CrudService
 * @subpackage CrudServiceClientInterface
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/13)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Interface for basic Crud API client
 *
 * @author Dodonov A.A.
 */
interface CrudServiceClientInterface
{

    /**
     * Method returns all records by filter
     *
     * @param array $Filter
     *            Filtering settings
     * @param int $CrossDomain
     *            Cross domain security settings
     * @return array List of records
     * @codeCoverageIgnore
     */
    public function getRecordsBy($Filter, $CrossDomain = 0);

    /**
     * Method returns record by it's id
     *
     * @param int $id
     *            Id of the fetching record
     * @param number $CrossDomain
     *            Domain id
     * @return object fetched record
     * @codeCoverageIgnore
     */
    public function getById($id, $CrossDomain = 0);

    /**
     * Method returns records by their ids
     *
     * @param array $ids
     *            List of ids
     * @param number $CrossDomain
     *            Domain id
     * @return array Fetched records
     */
    public function getByIdsArray($ids, $CrossDomain = 0);

    /**
     * Method creates new record
     *
     * @param array $Data
     *            data for creating record
     * @return int id of the created record
     */
    public function create($Data);

    /**
     * Method updates new record
     *
     * @param int $id
     *            Id of the updating record
     * @param array $Data
     *            Data to be posted
     * @param int $CrossDomain
     *            Cross domain policy
     * @return mixed Result of the RPC call
     * @codeCoverageIgnore
     */
    public function update(int $id, array $Data, int $CrossDomain = 0);

    /**
     * Method returns creation form's fields in JSON format
     *
     * @codeCoverageIgnore
     */
    public function fields();

    /**
     * Method returns all records created since $Date
     *
     * @param \datetime $Date
     *            Start of the period
     * @return array List of records created since $Date
     * @codeCoverageIgnore
     */
    public function newRecordsSince($Date);

    /**
     * Method returns count of records
     *
     * @return array List of records created since $Date
     * @codeCoverageIgnore
     */
    public function recordsCount();

    /**
     * Method returns last $Count records
     *
     * @param int $Count
     *            Amount of records to be fetched
     * @param array $Filter
     *            Filter data
     * @return array $Count of last created records
     * @codeCoverageIgnore
     */
    public function lastRecords($Count, $Filter);

    /**
     * Method deletes record with $id
     *
     * @param int $id
     *            Id of the deleting record
     * @param int $CrossDomain
     *            Break domain's bounds or not
     * @return string Result of the deletion
     * @codeCoverageIgnore
     */
    public function delete(int $id, int $CrossDomain = 0): string;

    /**
     * Method returns count off records
     *
     * @param string $Field
     *            Field for grouping
     * @param array $Filter
     *            Filtering settings
     * @return array List of records created since $Date
     */
    public function recordsCountByField(string $Field, $Filter = false): array;

    /**
     * Method deletes records by filter
     *
     * @param int $CrossDomain
     *            Cross domain security settings
     * @param array $Filter
     *            Filtering settings
     * @codeCoverageIgnore
     */
    public function deleteFiltered($CrossDomain = 0, $Filter = false);

    /**
     * Method creates instance if the CrudServiceClient class
     *
     * @param string $Service
     *            Service to be connected to
     * @param string $Token
     *            Connection token
     * @return \Mezon\CrudService\CrudServiceClient Instance of the CrudServiceClient class
     */
    public static function instance(string $Service, string $Token): \Mezon\CrudService\CrudServiceClient;

    /**
     * Method returns some records of the user's domain
     *
     * @param int $From
     *            The beginnig of the fetching sequence
     * @param int $Limit
     *            Size of the fetching sequence
     * @param int $CrossDomain
     *            Cross domain security settings
     * @param array $Filter
     *            Filtering settings
     * @param array $Order
     *            Sorting settings
     * @return array List of records
     */
    public function getList(int $From = 0, int $Limit = 1000000000, $CrossDomain = 0, $Filter = false, $Order = false): array;

    /**
     * Method returns fields and layout
     *
     * @return array Fields and layout
     */
    public function getFields(): array;
}
