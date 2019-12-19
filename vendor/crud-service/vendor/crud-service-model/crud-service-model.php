<?php
namespace Mezon\CRUDService;

/**
 * Class CRUDServiceModel
 *
 * @package CRUDService
 * @subpackage CRUDServiceModel
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../functional/functional.php');
require_once (__DIR__ . '/../../../service/vendor/db-service-model/db-service-model.php');

// TODO add camel-case
/**
 * CRUD service's default model
 *
 * @author Dodonov A.A.
 */
class CRUDServiceModel extends \Mezon\Service\DBServiceModel
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
     * Method transforms record before it will be returned with the new_records_since method
     *
     * @param array $Records
     *            Record to be transformed
     */
    protected function last_new_records_since(array &$Records)
    {
        $this->get_records_transformer($Records);
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
    protected function add_domain_id_condition($DomainId, array $Where = []): array
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
    public function new_records_since($DomainId, $Date)
    {
        $Where = $this->add_domain_id_condition($DomainId);

        $Where[] = 'creation_date >= "' . date('Y-m-d H:i:s', strtotime($Date)) . '"';

        $Connection = $this->get_connection();

        $Records = $Connection->select($this->get_fields_names(), $this->TableName, implode(' AND ', $Where));

        $this->last_new_records_since($Records);

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
    public function records_count($DomainId = false, array $Where = [
        '1=1'
    ]): int
    {
        $Where = $this->add_domain_id_condition($DomainId, $Where);

        $Records = $this->get_connection()->select('COUNT( * ) AS records_count', $this->TableName, implode(' AND ', $Where));

        if (count($Records) === 0) {
            return (0);
        }

        return (\Mezon\Functional::get_field($Records[0], 'records_count'));
    }

    /**
     * Method fetches records before transformation
     *
     * @param integer|boolean $DomainId
     *            Id of the domain
     * @param integer $From
     *            Starting record
     * @param integer $Limit
     *            Fetch limit
     * @param array $Where
     *            Fetch condition
     * @param array $Order
     *            Sorting condition
     * @return array of records
     */
    public function get_simple_records($DomainId, $From, $Limit, $Where, $Order = [
        'field' => 'id',
        'order' => 'ASC'
    ])
    {
        $Where = $this->add_domain_id_condition($DomainId, $Where);

        $Records = $this->get_connection()->select($this->get_fields_names(), $this->TableName, implode(' AND ', $Where) . ' ORDER BY ' . htmlspecialchars($Order['field']) . ' ' . htmlspecialchars($Order['order']), $From, $Limit);

        return ($Records);
    }

    /**
     * Method transforms record before it will be returned with the get_records method
     *
     * @param array $Records
     *            Record to be transformed
     *            
     * @codeCoverageIgnore
     */
    protected function get_records_transformer(array &$Records)
    {}

    /**
     * Method fetches records after transformation
     *
     * @param integer|boolean $DomainId
     *            Id of the domain
     * @param integer $From
     *            Starting record
     * @param integer $Limit
     *            Fetch limit
     * @param array $Where
     *            Fetch condition
     * @param array $Order
     *            Sorting condition
     * @return array of records
     */
    public function get_records($DomainId, $From, $Limit, $Where = [
        '1=1'
    ], $Order = [
        'field' => 'id',
        'order' => 'ASC'
    ])
    {
        $Records = $this->get_simple_records($DomainId, $From, $Limit, $Where, $Order);

        $this->get_records_transformer($Records);

        return ($Records);
    }

    /**
     * Method transforms record before it will be returned with the last_records method
     *
     * @param array $Records
     *            Record to be transformed
     */
    protected function last_records_transformer(array &$Records)
    {
        $this->get_records_transformer($Records);
    }

    /**
     * Method returns last $Count records
     *
     * @param integer|boolean $DomainId
     *            Id of the domain
     * @param integer $Count
     *            Amount of records to be returned
     * @param array $Where
     *            Filter conditions
     * @return array List of the last $Count records
     */
    public function last_records($DomainId, $Count, $Where)
    {
        $Where = $this->add_domain_id_condition($DomainId, $Where);

        $Records = $this->get_connection()->select($this->get_fields_names(), $this->TableName, implode(' AND ', $Where) . ' ORDER BY id DESC', 0, $Count);

        $this->last_records_transformer($Records);

        return ($Records);
    }

    /**
     * Method transforms record before it will be returned with the fetch_records_by_ids method
     *
     * @param array $Records
     *            Record to be transformed
     */
    protected function fetch_records_by_ids_transformer(array &$Records)
    {
        $this->get_records_transformer($Records);
    }

    /**
     * Method fetches records bythe specified fields
     *
     * @param integer|bool $DomainId
     *            Domain id
     * @param string $ids
     *            ids of records to be fetched
     * @return array list of records
     */
    public function fetch_records_by_ids($DomainId, string $ids)
    {
        if ($DomainId === false) {
            $Where = 'id IN ( ' . $ids . ' )';
        } else {
            $Where = 'id IN ( ' . $ids . ' ) AND domain_id = ' . intval($DomainId);
        }

        $Records = $this->get_connection()->select($this->get_fields_names(), $this->TableName, $Where);

        if (count($Records) == 0) {
            throw (new \Exception('Record with id in ' . $ids . ' and domain = ' . ($DomainId === false ? 'false' : $DomainId) . ' was not found', - 1));
        }

        $this->fetch_records_by_ids_transformer($Records);

        return ($Records);
    }

    /**
     * Method returns amount of records in table, grouped by the specified field
     *
     * @param integer|bool $DomainId
     *            Domain id
     * @param string $FieldName
     *            Grouping field
     * @param array $Where
     *            Filtration conditions
     * @return array Records with stat
     */
    public function records_count_by_field($DomainId, string $FieldName, array $Where): array
    {
        $Where = $this->add_domain_id_condition($DomainId, $Where);

        $Records = $this->get_connection()->select($FieldName . ' , COUNT( * ) AS records_count', $this->TableName, implode(' AND ', $Where) . ' GROUP BY ' . $FieldName);

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
    public function delete_filtered($DomainId, array $Where)
    {
        if ($DomainId === false) {
            return ($this->get_connection()->delete($this->TableName, implode(' AND ', $Where)));
        } else {
            return ($this->get_connection()->delete($this->TableName, implode(' AND ', $Where) . ' AND domain_id = ' . intval($DomainId)));
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
    public function update_basic_fields($DomainId, array $Record, array $Where)
    {
        $Where = $this->add_domain_id_condition($DomainId, $Where);

        $Connection = $this->get_connection();

        $Connection->update($this->TableName, $Record, implode(' AND ', $Where));

        return ($Record);
    }

    /**
     * Method fetches fields for model manipulation
     *
     * @return array fetched fields
     */
    public function fetch_fields(): array
    {
        $Record = [];

        foreach ($this->FieldsAlgorithms->get_fields_names() as $Name) {
            $Field = $this->FieldsAlgorithms->get_object($Name);
            if ($Field->get_type() == 'custom') {
                continue;
            }
            if ($Name == 'id' || $Name == 'domain_id') {
                continue;
            }
            if ($Name == 'modification_date' || $Name == 'creation_date') {
                $Record[$Name] = 'NOW()';
                continue;
            }

            $this->FieldsAlgorithms->fetch_field($Record, $Name);
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
    public function insert_basic_fields(array $Record, $DomainId = 0)
    {
        if ($this->has_field('domain_id')) {
            $Record['domain_id'] = $DomainId;
        }

        if (count($Record) === 0) {
            $Msg = 'Trying to create empty record. Be shure that you have passed at least one of these fields : ';

            throw (new \Exception($Msg . $this->get_fields_names()));
        }

        $Record['id'] = $this->get_connection()->insert($this->TableName, $Record);

        return ($Record);
    }
}

?>