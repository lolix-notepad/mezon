<?php
/**
 * Class FieldsAlgorithms
 *
 * @package     CRUDService
 * @subpackage  FieldsAlgorithms
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/08)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../security/security.php');

require_once (__DIR__ . '/../field/vendor/checkboxes-field/checkboxes-field.php');
require_once (__DIR__ . '/../field/vendor/custom-field/custom-field.php');
require_once (__DIR__ . '/../field/vendor/form-header/form-header.php');
require_once (__DIR__ . '/../field/vendor/input-date/input-date.php');
require_once (__DIR__ . '/../field/vendor/input-file/input-file.php');
require_once (__DIR__ . '/../field/vendor/input-text/input-text.php');
require_once (__DIR__ . '/../field/vendor/label-field/label-field.php');
require_once (__DIR__ . '/../field/vendor/record-field/record-field.php');
require_once (__DIR__ . '/../field/vendor/select/select.php');
require_once (__DIR__ . '/../field/vendor/textarea/textarea.php');

require_once (__DIR__ . '/../form-builder/vendor/rows-field/rows-field.php');

/**
 * Class constructs forms
 */
class FieldsAlgorithms
{

    /**
     * List of control objects
     *
     * @var array
     */
    var $FieldObjects = [];

    /**
     * Entity name
     *
     * @var string
     */
    var $EntityName = false;

    /**
     * Session Id
     *
     * @var string
     */
    var $SessionId = '';

    /**
     * Constructor
     *
     * @param array $Fields
     *            List of all fields
     * @param string $EntityName
     *            Entity name
     */
    public function __construct(array $Fields = [], string $EntityName = '')
    {
        $this->EntityName = $EntityName;

        foreach ($Fields as $Name => $Field) {
            $Field['name'] = $Name;
            $Field['name-prefix'] = $this->EntityName;

            $this->FieldObjects[$Name] = $this->init_object($Field);
        }
    }

    /**
     * Returning date value
     *
     * @param string $Value
     *            Value to be made secure
     * @return string Secure value
     */
    protected function get_date_value(string $Value): string
    {
        if ($Value == '""') {
            return ('');
        } else {
            return (date('Y-m-d', strtotime($Value)));
        }
    }

    /**
     * Returning date value
     *
     * @param array $Value
     *            Value to be made secure
     * @return array Secure value
     */
    protected function get_external_value(array $Value): array
    {
        foreach ($Value as $i => $Item) {
            $Value[$i] = intval($Item);
        }

        return ($Value);
    }

    /**
     * Method returns true if the entity has custom fields
     * False otherwise
     *
     * @return bool true if the entity has custom fields
     */
    public function has_custom_fields(): bool
    {
        foreach ($this->FieldObjects as $Field) {
            if ($Field->get_type() == 'custom') {
                return (true);
            }
        }

        return (false);
    }

    /**
     * Method returns typed value
     *
     * @param string $Type
     *            of the field
     * @param string|array $Value
     *            of the field
     * @param bool $StoreFiles
     *            Need the uploaded file to be stored
     * @return mixed Secured value
     */
    public function get_typed_value(string $Type, $Value, bool $StoreFiles = true)
    {
        $Result = '';

        switch ($Type) {
            case ('integer'):
                $Result = intval($Value);
                break;

            case ('string'):
                $Result = Security::get_string_value($Value);
                break;

            case ('file'):
                $Result = Security::get_file_value($Value, $StoreFiles);
                break;

            case ('date'):
                $Result = $this->get_date_value($Value);
                break;

            case ('external'):
                $Result = $this->get_external_value($Value);
                break;

            default:
                throw (new Exception('Undefined type "' . $Type . '"'));
        }

        return ($Result);
    }

    /**
     * Method validates if the field $Field exists
     *
     * @param string $Field
     *            Field name
     */
    public function validate_field_existance(string $Field)
    {
        if (! isset($this->FieldObjects[$Field])) {
            throw (new Exception('Field "' . $Field . '" was not found'));
        }
    }

    /**
     * Getting secure value
     *
     * @param string $Field
     *            Field name
     * @param mixed $Value
     *            Field value
     * @param bool $StoreFiles
     *            Should we store files
     * @return mixed Secure value of the field
     */
    public function get_secure_value(string $Field, $Value, bool $StoreFiles = true)
    {
        $this->validate_field_existance($Field);

        return ($this->get_typed_value($this->FieldObjects[$Field]->get_type(), $Value, $StoreFiles));
    }

    /**
     * Getting secure values
     *
     * @param string $Field
     *            Field name
     * @param mixed $Values
     *            Field values
     * @param bool $StoreFiles
     *            Should we store files
     * @return mixed Secure values of the field or one value
     */
    public function get_secure_values(string $Field, $Values, bool $StoreFiles = true)
    {
        $Return = [];

        if (is_array($Values)) {
            foreach ($Values as $i => $Value) {
                $Return[$i] = $this->get_secure_value($Field, $Value, $StoreFiles);
            }
        } else {
            $Return = $this->get_secure_value($Field, $Values, $StoreFiles);
        }

        return ($Return);
    }

    /**
     * Method returns field wich names are started from $Prefix
     *
     * @param string $Prefix
     *            of the fieldsto be fetched
     * @param bool $StoreFiles
     *            Should we store files
     * @return array Fetched fields
     */
    public function get_values_for_prefix(string $Prefix, bool $StoreFiles = true): array
    {
        $Records = [];

        foreach (array_keys($this->FieldObjects) as $Name) {
            if (isset($_POST[$Prefix . $Name])) {
                $Records[$Name] = $this->get_secure_values($Name, $_POST[$Prefix . $Name], $StoreFiles);
            }
        }

        return ($Records);
    }

    /**
     * Method removes field
     *
     * @param string $Name
     *            Field name
     */
    public function remove_field($Name)
    {
        unset($this->FieldObjects[$Name]);
    }

    /**
     * Method fetches returns custom fields for saving
     *
     * @param array $Record
     *            Record to be extended
     * @param string $Name
     *            Name od the field
     * @return array Extended record
     */
    public function fetch_custom_field(array &$Record, string $Name): array
    {
        if (! isset($this->FieldObjects[$Name])) {
            return ($Record);
        }

        $NestedFields = $this->FieldObjects[$Name]->get_fields();

        foreach ($NestedFields as $Name => $Field) {
            if (isset($_POST[$this->EntityName . '-' . $Name])) {
                $Record[$Name] = $this->get_typed_value($Field['type'], $_POST[$this->EntityName . '-' . $Name], true);
            }
        }

        return ($Record);
    }

    /**
     * Method fetches submitted field
     *
     * @param array $Record
     *            Record to be extended
     * @param string $Name
     *            Name od the field
     */
    public function fetch_field(array &$Record, string $Name)
    {
        if (isset($_POST[$this->EntityName . '-' . $Name])) {
            $Record[$Name] = $this->get_secure_value($Name, $_POST[$this->EntityName . '-' . $Name]);
        } elseif (isset($_FILES[$this->EntityName . '-' . $Name])) {
            $Record[$Name] = $this->get_secure_value($Name, $_FILES[$this->EntityName . '-' . $Name]);
        } elseif ($this->has_custom_fields()) {
            $Record = $this->fetch_custom_field($Record, $Name);
        }
    }

    /**
     * Method inits control
     *
     * @param array $Field
     *            Field
     * @return mixed Control
     */
    protected function init_object(array $Field)
    {
        if (isset($Field['items'])) {
            $Control = new Select($Field);
        } elseif (isset($Field['control']) && $Field['control'] == 'textarea') {
            $Control = new Textarea($Field);
        } elseif ($Field['type'] == 'external') {
            $Field['session-id'] = $this->SessionId;
            $Control = new CheckboxesField($Field);
        } elseif ($Field['type'] == 'records') {
            $Field['session-id'] = $this->SessionId;
            $Control = new RecordField($Field);
        } elseif ($Field['type'] == 'file') {
            $Control = new InputFile($Field);
        } elseif ($Field['type'] == 'date') {
            $Control = new InputDate($Field);
        } elseif ($Field['type'] == 'custom') {
            $Control = new CustomField($Field);
        } elseif ($Field['type'] == 'header') {
            $Control = new FormHeader($Field);
        } elseif ($Field['type'] == 'label') {
            $Control = new LabelField($Field);
        } elseif ($Field['type'] == 'rows') {
            $ControlHTML = '';
            foreach ($Field['type']['rows'] as $RowFieldName) {
                $Control = $this->get_object($RowFieldName);
                $ControlHTML .= $Control->html();
            }
            $Control = new RowsField($Field, $ControlHTML);
        } else {
            $Control = new InputText($Field);
        }

        return ($Control);
    }

    /**
     * Method returns field object
     *
     * @param string $Name
     *            Field name
     * @return Field Field object
     */
    public function get_object(string $Name): Field
    {
        return ($this->FieldObjects[$Name]);
    }

    /**
     * Method compiles field DOM
     *
     * @param string $Name
     *            Field name
     */
    public function get_compiled_field(string $Name)
    {
        $Control = $this->get_object($Name);

        return ($Control->html());
    }

    /**
     * Method compiles rows field
     *
     * @param string $Name
     *            field name
     * @param array $Record
     *            Data source
     * @return string Compiled control
     */
    protected function compile_rows_field(string $Name, array $Record): string
    {
        $FieldObject = $this->get_object($Name);

        $Content = ($FieldObject->has_label() ? '<div class="form-group ' . $this->EntityName . ' col-md-12">' . '<label class="control-label">' . $FieldObject->get_title() . ($FieldObject->is_required($Name) ? ' <span class="required">*</span>' : '') . '</label></div>' : '');

        $Content .= '<div>' . ($Template = $this->get_compiled_field($Name, $Record)) . '</div>';

        $Content .= '<div><div class="form-group col-md-12">';
        $Content .= '<button class="btn btn-success col-md-2" onclick="add_element_by_template( this , \'' . $Name . '\' )">Добавить</button>';
        $Content .= '</div></div>';

        $Content = str_replace('{_creation_form_items_counter}', '0', $Content);

        $Content .= '<template class="' . $Name . '"><div>';
        $Content .= $Template;
        $Content .= '<div class="form-group col-md-12">';
        $Content .= '<button class="btn btn-success col-md-2" onclick="add_element_by_template( this , \'' . $Name . '\' );">Добавить</button>';
        $Content .= '<button class="btn btn-danger col-md-2" onclick="remove_element_by_template( this );">Удалить</button>';
        $Content .= '</div></div>';
        $Content .= '</template>';

        return ($Content);
    }

    /**
     * Method returns array of fields names
     *
     * @return array
     */
    public function get_fields_names(): array
    {
        return (array_keys($this->FieldObjects));
    }
    
    /**
     * Method returns true if the field exists
     *
     * @param string $FieldName
     *            Field name
     * @return bool
     */
    public function has_field(string $FieldName): bool
    {
        // @codeCoverageIgnoreStart
        return (isset($this->FieldObjects[$FieldName]));
        // @codeCoverageIgnoreEnd
    }
}

?>