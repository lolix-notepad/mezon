<?php
namespace Mezon\Service;
/**
 * Class CustomFieldsModel
 *
 * @package     Service
 * @subpackage  CustomFieldsModel
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/11/08)
 * @copyright   Copyright (c) 2019, aeon.org
 */

// TODO add camel-case
/**
 * Model for processing custom fields
 * 
 * @author Dodonov A.A.
 */
class CustomFieldsModel {
    /**
     * Table name
     */
    protected $TableName = '';

    /**
     * Constructor
     *
     * @param string $TableName
     *            name of the table
     */
    public function __construct(string $TableName)
    {
        $this->TableName = $TableName;
    }

    /**
     * Method returns connection to the DB.
     *
     * @return boolean|\Mezon\PdoCrud - PDO DB connection or false on error.
     */
    protected function get_connection():\Mezon\PdoCrud
    {
        // @codeCoverageIgnoreStart
        return (\Mezon\Mezon::get_db_connection());
        // @codeCoverageIgnoreEnd
    }

    /**
     * Method returns table name
     *
     * @return string Table name
     */
    protected function get_custom_fields_template_name(): string
    {
        return ($this->TableName . '_custom_field');
    }

    /**
     * Getting custom fields for object
     *
     * @param int $ObjectId
     *            Object id
     * @param array $Filter
     *            List of required fields or all
     * @return array Result of the fetching
     */
    public function get_custom_fields_for_object(int $ObjectId, array $Filter = [
        '*'
    ]): array
    {
        $Result = [];

        $CustomFields = $this->get_connection()->select('*', $this->get_custom_fields_template_name(), 'object_id = ' . $ObjectId);

        foreach ($CustomFields as $Field) {
            $FieldName = \Mezon\Functional::get_field($Field, 'field_name');

            // if the field in the list or all fields must be fetched
            if (in_array($FieldName, $Filter) || in_array('*', $Filter)) {
                $Result[$FieldName] = \Mezon\Functional::get_field($Field, 'field_value');
            }
        }

        return ($Result);
    }

    /**
     * Deleting custom fields for object
     *
     * @param int $ObjectId
     *            Object id
     * @param array $Filter
     *            List of required fields or all
     */
    public function delete_custom_fields_for_object(int $ObjectId, array $Filter = [
        '1=1'
    ])
    {
        $Condition = implode(' AND ', array_merge($Filter, [
            'object_id = ' . $ObjectId
        ]));

        $this->get_connection()->delete($this->get_custom_fields_template_name(), $Condition);
    }

    /**
     * Method sets custom field
     *
     * @param int $ObjectId
     *            Object id
     * @param string $FieldName
     *            Field name
     * @param string $FieldValue
     *            Field value
     */
    public function set_field_for_object(int $ObjectId, string $FieldName, string $FieldValue):void
    {
        $Connection = $this->get_connection();

        $ObjectId = intval($ObjectId);
        $FieldName = htmlspecialchars($FieldName);
        $FieldValue = htmlspecialchars($FieldValue);
        $Record = [
            'field_value' => $FieldValue
        ];

        if (count($this->get_custom_fields_for_object($ObjectId, [
            $FieldName
        ])) > 0) {
            $Connection->update($this->get_custom_fields_template_name(), $Record, 'field_name LIKE "' . $FieldName . '" AND object_id = ' . $ObjectId);
        } else {
            // in the previous line we have tried to update unexisting field, so create it
            $Record['field_name'] = $FieldName;
            $Record['object_id'] = $ObjectId;
            $Connection->insert($this->get_custom_fields_template_name(), $Record);
        }
    }

    /**
     * Method fetches custom fields for record
     *
     * @param array $Records
     *            List of records
     * @return array Transformed records
     */
    public function get_custom_fields_for_records(array $Records): array
    {
        foreach ($Records as $i => $Record) {
            $Records[$i]['custom'] = $this->get_custom_fields_for_object(\Mezon\Functional::get_field($Record, 'id'));
        }

        return ($Records);
    }
}

?>