<?php
namespace Mezon\CrudService;

/**
 * Class CrudServiceModel
 *
 * @package CrudService
 * @subpackage CrudServiceModel
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Crud service's default model
 *
 * @author Dodonov A.A.
 */
class CrudServiceModel extends \Mezon\Service\DbServiceModel
{

    /**
     * Constructor
     *
     * @param string|array $Fields
     *            fields of the model
     * @param string $TableName
     *            name of the table
     * @param string $EntityName
     *            name of the entity
     */
    public function __construct($Fields = '*', string $TableName = '', string $EntityName = '')
    {
        parent::__construct($Fields, $TableName, $EntityName);
    }

    /**
     * Method transforms record before it will be returned with the newRecordsSince method
     *
     * @param array $Records
     *            Record to be transformed
     */
    protected function lastNewRecordsSince(array &$Records)
    {
        $this->getRecordsTransformer($Records);
    }

    /**
     * Method adds domain conditions
     *
     * @param int|bool $DomainId
     *            Do we have domain limitations
     * @param array $Where
     *            where condition
     * @return array where condition with domain_id limitations
     */
    protected function addDomainIdCondition($DomainId, array $Where = []): array
    {
        if ($DomainId === false) {
            if (count($Where) === 0) {
                $Where[] = '1 = 1';
            }
        } else {
            $Where[] = 'domain_id = ' . intval($DomainId);
        }

        return ($Where);
    }

    /**
     * Method returns all records created since $Date
     *
     * @param int|bool $DomainId
     *            Do we have domain limitations
     * @param \datetime $Date
     *            Start of the period
     * @return array List of records created since $Date
     */
    public function newRecordsSince($DomainId, $Date)
    {
        $Where = $this->addDomainIdCondition($DomainId);

        $Where[] = 'creation_date >= "' . date('Y-m-d H:i:s', strtotime($Date)) . '"';

        $Connection = $this->getConnection();

        $Records = $Connection->select($this->getFieldsNames(), $this->TableName, implode(' AND ', $Where));

        $this->lastNewRecordsSince($Records);

        return ($Records);
    }

    /**
     * Method returns amount of records in table
     *
     * @param int|bool $DomainId
     *            Do we have domain limitations
     * @param array $Where
     *            Filter
     * @return number Amount of records
     */
    public function recordsCount($DomainId = false, array $Where = [
        '1=1'
    ]): int
    {
        $Where = $this->addDomainIdCondition($DomainId, $Where);

        $Records = $this->getConnection()->select(
            'COUNT( * ) AS records_count',
            $this->TableName,
            implode(' AND ', $Where));

        if (count($Records) === 0) {
            return (0);
        }

        return (\Mezon\Functional\Functional::getField($Records[0], 'records_count'));
    }

    /**
     * Method fetches records before transformation
     *
     * @param int|bool $DomainId
     *            Id of the domain
     * @param int $From
     *            Starting record
     * @param int $Limit
     *            Fetch limit
     * @param array $Where
     *            Fetch condition
     * @param array $Order
     *            Sorting condition
     * @return array of records
     */
    public function getSimpleRecords($DomainId, $From, $Limit, $Where, $Order = [
        'field' => 'id',
        'order' => 'ASC'
    ])
    {
        $Where = $this->addDomainIdCondition($DomainId, $Where);

        $Records = $this->getConnection()->select(
            $this->getFieldsNames(),
            $this->TableName,
            implode(' AND ', $Where) . ' ORDER BY ' . htmlspecialchars($Order['field']) . ' ' .
            htmlspecialchars($Order['order']),
            $From,
            $Limit);

        return ($Records);
    }

    /**
     * Method transforms record before it will be returned with the getRecords method
     *
     * @param array $Records
     *            Record to be transformed
     *            
     * @codeCoverageIgnore
     */
    protected function getRecordsTransformer(array &$Records)
    {}

    /**
     * Method fetches records after transformation
     *
     * @param int|bool $DomainId
     *            Id of the domain
     * @param int $From
     *            Starting record
     * @param int $Limit
     *            Fetch limit
     * @param array $Where
     *            Fetch condition
     * @param array $Order
     *            Sorting condition
     * @return array of records
     */
    public function getRecords($DomainId, $From, $Limit, $Where = [
        '1=1'
    ], $Order = [
        'field' => 'id',
        'order' => 'ASC'
    ])
    {
        $Records = $this->getSimpleRecords($DomainId, $From, $Limit, $Where, $Order);

        $this->getRecordsTransformer($Records);

        return ($Records);
    }

    /**
     * Method transforms record before it will be returned with the lastRecords method
     *
     * @param array $Records
     *            Record to be transformed
     */
    protected function lastRecordsTransformer(array &$Records)
    {
        $this->getRecordsTransformer($Records);
    }

    /**
     * Method returns last $Count records
     *
     * @param int|bool $DomainId
     *            Id of the domain
     * @param int $Count
     *            Amount of records to be returned
     * @param array $Where
     *            Filter conditions
     * @return array List of the last $Count records
     */
    public function lastRecords($DomainId, $Count, $Where)
    {
        $Where = $this->addDomainIdCondition($DomainId, $Where);

        $Records = $this->getConnection()->select(
            $this->getFieldsNames(),
            $this->TableName,
            implode(' AND ', $Where) . ' ORDER BY id DESC',
            0,
            $Count);

        $this->lastRecordsTransformer($Records);

        return ($Records);
    }

    /**
     * Method transforms record before it will be returned with the fetchRecordsByIds method
     *
     * @param array $Records
     *            Record to be transformed
     */
    protected function fetchRecordsByIdsTransformer(array &$Records)
    {
        $this->getRecordsTransformer($Records);
    }

    /**
     * Method fetches records bythe specified fields
     *
     * @param int|bool $DomainId
     *            Domain id
     * @param string $ids
     *            ids of records to be fetched
     * @return array list of records
     */
    public function fetchRecordsByIds($DomainId, string $ids)
    {
        if ($DomainId === false) {
            $Where = 'id IN ( ' . $ids . ' )';
        } else {
            $Where = 'id IN ( ' . $ids . ' ) AND domain_id = ' . intval($DomainId);
        }

        $Records = $this->getConnection()->select($this->getFieldsNames(), $this->TableName, $Where);

        if (count($Records) == 0) {
            throw (new \Exception(
                'Record with id in ' . $ids . ' and domain = ' . ($DomainId === false ? 'false' : $DomainId) .
                ' was not found',
                - 1));
        }

        $this->fetchRecordsByIdsTransformer($Records);

        return ($Records);
    }

    /**
     * Method returns amount of records in table, grouped by the specified field
     *
     * @param int|bool $DomainId
     *            Domain id
     * @param string $FieldName
     *            Grouping field
     * @param array $Where
     *            Filtration conditions
     * @return array Records with stat
     */
    public function recordsCountByField($DomainId, string $FieldName, array $Where): array
    {
        $Where = $this->addDomainIdCondition($DomainId, $Where);

        $Records = $this->getConnection()->select(
            $FieldName . ' , COUNT( * ) AS records_count',
            $this->TableName,
            implode(' AND ', $Where) . ' GROUP BY ' . $FieldName);

        if (count($Records) === 0) {
            return ([
                'records_count' => 0
            ]);
        }

        return ($Records);
    }

    /**
     * Method deletes filtered records
     *
     * @param mixed $DomainId
     *            Domain id
     * @param array $Where
     *            Filtration conditions
     */
    public function deleteFiltered($DomainId, array $Where)
    {
        if ($DomainId === false) {
            return ($this->getConnection()->delete($this->TableName, implode(' AND ', $Where)));
        } else {
            return ($this->getConnection()->delete(
                $this->TableName,
                implode(' AND ', $Where) . ' AND domain_id = ' . intval($DomainId)));
        }
    }

    /**
     * Method updates records
     *
     * @param
     *            int DomainId Domain id. Pass false if we want to ignore domain_id security
     * @param array $Record
     *            New values for fields
     * @param array $Where
     *            Condition
     * @return array Updated fields
     */
    public function updateBasicFields($DomainId, array $Record, array $Where)
    {
        $Where = $this->addDomainIdCondition($DomainId, $Where);

        $Connection = $this->getConnection();

        $Connection->update($this->TableName, $Record, implode(' AND ', $Where));

        return ($Record);
    }

    /**
     * Method fetches fields for model manipulation
     *
     * @return array fetched fields
     */
    public function fetchFields(): array
    {
        $Record = [];

        foreach ($this->FieldsAlgorithms->getFieldsNames() as $Name) {
            $Field = $this->FieldsAlgorithms->getObject($Name);
            if ($Field->getType() == 'custom') {
                continue;
            }
            if ($Name == 'id' || $Name == 'domain_id') {
                continue;
            }
            if ($Name == 'modification_date' || $Name == 'creation_date') {
                $Record[$Name] = 'NOW()';
                continue;
            }

            $this->FieldsAlgorithms->fetchField($Record, $Name);
        }

        return ($Record);
    }

    /**
     * Method inserts basic fields
     *
     * @param array $Record
     *            Record to be inserted
     * @param mixed $DomainId
     *            Id of the domain
     * @return array Inserted record
     */
    public function insertBasicFields(array $Record, $DomainId = 0)
    {
        if ($this->hasField('domain_id')) {
            $Record['domain_id'] = $DomainId;
        }

        if (count($Record) === 0) {
            $Msg = 'Trying to create empty record. Be shure that you have passed at least one of these fields : ';

            throw (new \Exception($Msg . $this->getFieldsNames()));
        }

        $Record['id'] = $this->getConnection()->insert($this->TableName, $Record);

        return ($Record);
    }
}
