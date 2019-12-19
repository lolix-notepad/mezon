<?php
namespace Mezon\CRUDService;

/**
 * Class CRUDServiceLogic
 *
 * @package CRUDService
 * @subpackage CRUDServiceLogic
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/13)
 * @copyright Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../gui/vendor/fields-algorithms/vendor/filter/filter.php');
require_once (__DIR__ . '/../../../gui/vendor/form-builder/form-builder.php');
require_once (__DIR__ . '/../../../service/vendor/service-logic/service-logic.php');
require_once (__DIR__ . '/../../../utils/utils.php');

require_once (__DIR__ . '/../crud-service-model/crud-service-model.php');

define('NOW', 'NOW()');
define('CREATION_DATE_FIELD_NAME', 'creation_date');
define('DOMAIN_ID_FIELD_NAME', 'domain_id');
define('ORDER_FIELD_NAME', 'order');
define('FIELD_FIELD_NAME', 'field');
define('FIELD_TYPE_CUSTOM', 'custom');
define('ITEMS_FIELD_NAME', 'items');
define('ENTITY_FIELD_NAME', 'entity');
define('FIELDS_FIELD_NAME', 'fields');

// TODO add camel-case
/**
 * Class handles CRUD logic.
 *
 * @author Dodonov A.A.
 */
class CRUDServiceLogic extends \Mezon\Service\ServiceLogic
{

    /**
     * Form builder
     */
    var $FormBuilder = false;

    /**
     * Method deletes the specified record
     *
     * @return integer id of the deleted record
     */
    public function delete_record()
    {
        $DomainId = $this->get_domain_id();
        $Where = \Mezon\GUI\FieldsAlgorithms\Filter::add_filter_condition([
            'id = ' . intval($this->ParamsFetcher->get_param('id'))
        ]);

        return ($this->Model->delete_filtered($DomainId, $Where));
    }

    /**
     * Method deletes filtered records
     */
    public function delete_filtered()
    {
        $DomainId = $this->get_domain_id();
        $Where = \Mezon\GUI\FieldsAlgorithms\Filter::add_filter_condition([]);

        return ($this->Model->delete_filtered($DomainId, $Where));
    }

    /**
     * Method returns records
     *
     * @param integer $DomainId
     *            Domain id
     * @param array $Order
     *            Sorting settings
     * @param integer $From
     *            Starting record
     * @param integer $Limit
     *            Fetch limit
     * @return array of records after all transformations
     */
    public function get_records($DomainId, $Order, $From, $Limit): array
    {
        $Records = $this->Model->get_simple_records($DomainId, $From, $Limit, \Mezon\GUI\FieldsAlgorithms\Filter::add_filter_condition([]), $Order);

        return ($Records);
    }

    /**
     * Method returns domain id.
     *
     * @return integer Domain id.
     */
    public function get_domain_id()
    {
        // records are not separated between domains
        if ($this->Model->has_field(DOMAIN_ID_FIELD_NAME) === false) {
            return (false);
        }

        if (isset($_GET['cross_domain']) && intval($_GET['cross_domain'])) {
            if ($this->has_permit($this->Model->get_entity_name() . '-manager')) {
                $DomainId = false;
            } else {
                throw (new \Exception('User "' . $this->get_self_login_value() . '" has no permit "' . $this->Model->get_entity_name() . '-manager"'));
            }
        } else {
            $DomainId = $this->get_self_id_value();
        }

        return ($DomainId);
    }

    /**
     * Method returns records
     *
     * @return array of records after all transformations.
     */
    public function list_record(): array
    {
        $DomainId = $this->get_domain_id();
        $Order = $this->ParamsFetcher->get_param(ORDER_FIELD_NAME, [
            FIELD_FIELD_NAME => 'id',
            ORDER_FIELD_NAME => 'ASC'
        ]);

        $From = $this->ParamsFetcher->get_param('from', 0);
        $Limit = $this->ParamsFetcher->get_param('limit', 1000000000);

        return ($this->get_records($DomainId, $Order, $From, $Limit));
    }

    /**
     * Method returns all records
     *
     * @return array of records after all transformations
     */
    public function all(): array
    {
        $DomainId = $this->get_domain_id();
        $Order = $this->ParamsFetcher->get_param(ORDER_FIELD_NAME, [
            FIELD_FIELD_NAME => 'id',
            ORDER_FIELD_NAME => 'ASC'
        ]);

        return ($this->get_records($DomainId, $Order, 0, 1000000000));
    }

    /**
     * Method returns all records created since $Date
     *
     * @return array List of records created since $Date
     */
    public function new_records_since(): array
    {
        $DomainId = $this->get_domain_id();
        $Date = $this->ParamsFetcher->get_param('date');

        if ($this->Model->has_field(CREATION_DATE_FIELD_NAME) === false) {
            throw (new \Exception('Field "creation_date" was not found'));
        }

        return ($this->Model->new_records_since($DomainId, $Date));
    }

    /**
     * Method returns records count
     *
     * @return integer Records count
     */
    public function records_count(): int
    {
        $DomainId = $this->get_domain_id();

        return ($this->Model->records_count($DomainId));
    }

    /**
     * Method returns last $Count records
     *
     * @return array List of the last $Count records
     */
    public function last_records()
    {
        $DomainId = $this->get_domain_id();
        $Count = $this->ParamsFetcher->get_param('count');
        $Filter = \Mezon\GUI\FieldsAlgorithms\Filter::add_filter_condition([
            '1 = 1'
        ]);

        return ($this->Model->last_records($DomainId, $Count, $Filter));
    }

    /**
     * Method compiles basic update record
     *
     * @param integer $id
     *            Id of the updating record
     * @return array with updated fields
     */
    protected function update_basic_fields($id)
    {
        $DomainId = $this->get_domain_id();
        $Record = $this->Model->fetch_fields();

        if ($this->Model->has_field('domain_id')) {
            $Record['domain_id'] = $this->get_self_id_value();
        }

        $Where = [
            "id = " . $this->get_param('id')
        ];

        return ($this->Model->update_basic_fields($DomainId, $Record, $Where));
    }

    /**
     * Method updates custom fields
     *
     * @param integer $id
     *            Id of the updating record
     * @param array|object $Record
     *            Updating data
     * @param array $CustomFields
     *            Custom fields to be updated
     * @return array|object - Updated data
     */
    protected function update_custom_fields($id, $Record, $CustomFields)
    {
        if (isset($CustomFields)) {
            foreach ($CustomFields as $Name => $Value) {
                $this->Model->set_field_for_object($id, $Name, $Value);
            }

            $Record['custom_fields'] = $CustomFields;
        }

        return ($Record);
    }

    /**
     * Method updates record and it's custom fields
     *
     * @return array Updated fields and their new values
     */
    public function update_record()
    {
        $id = $this->ParamsFetcher->get_param('id');

        $Record = $this->update_basic_fields($id);

        $Record = $this->update_custom_fields($id, $Record, $this->ParamsFetcher->get_param('custom_fields', null));

        $Record['id'] = $id;

        return ($Record);
    }

    /**
     * Method creates user
     *
     * @return array Created record
     */
    public function create_record()
    {
        $Record = $this->Model->fetch_fields();

        if ($this->Model->has_field('domain_id')) {
            $DomainId = $this->get_self_id_value();
        } else {
            $DomainId = false;
        }

        $Record = $this->Model->insert_basic_fields($Record, $DomainId);

        foreach ($this->Model->get_fields() as $Name => $Field) {
            $FieldName = $this->Model->get_entity_name() . '-' . $Name;
            if ($Field['type'] == 'external' && $this->ParamsFetcher->get_param($FieldName, false) !== false) {
                $Ids = $this->ParamsFetcher->get_param($FieldName);
                $Record = $this->Model->insert_external_fields($Record, $this->ParamsFetcher->get_param('session_id'), $Name, $Field, $Ids);
            }
        }

        return ($Record);
    }

    /**
     * Method returns exact record from the table.
     *
     * @return array Exact record.
     */
    public function exact()
    {
        $id = $this->ParamsFetcher->get_param('id');
        $DomainId = $this->get_domain_id();

        $Records = $this->Model->fetch_records_by_ids($DomainId, $id);

        return ($Records[0]);
    }

    /**
     * Method returns exact records from the table.
     *
     * @return array Exact list of records.
     */
    public function exact_list()
    {
        $ids = $this->ParamsFetcher->get_param('ids');
        $DomainId = $this->get_domain_id();

        return ($this->Model->fetch_records_by_ids($DomainId, $ids));
    }

    /**
     * Method returns records count, grouped by the specified field.
     *
     * @return integer Records count.
     */
    public function records_count_by_field()
    {
        $DomainId = $this->get_domain_id();

        $this->Model->validate_field_existance($this->ParamsFetcher->get_param(FIELD_FIELD_NAME));

        $Field = \Mezon\Security::get_string_value($this->ParamsFetcher->get_param(FIELD_FIELD_NAME));

        $Where = \Mezon\GUI\FieldsAlgorithms\Filter::add_filter_condition([]);

        return ($this->Model->records_count_by_field($DomainId, $Field, $Where));
    }

    /**
     * Fields descriptions.
     *
     * @return array Fields descriptions and layout
     */
    public function fields()
    {
        return ([
            'fields' => $this->Model->get_fields(),
            'layout' => $this->Layout
        ]);
    }
}

?>