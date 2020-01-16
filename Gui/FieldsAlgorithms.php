<?php
namespace Mezon\Gui;

/**
 * Class FieldsAlgorithms
 *
 * @package Gui
 * @subpackage FieldsAlgorithms
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/08)
 * @copyright Copyright (c) 2019, aeon.org
 */

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
    protected $FieldObjects = [];

    /**
     * Entity name
     *
     * @var string
     */
    protected $EntityName = false;

    /**
     * Session Id
     *
     * @var string
     */
    protected $SessionId = '';

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

            $this->FieldObjects[$Name] = $this->initObject($Field);
        }
    }

    /**
     * Returning date value
     *
     * @param string $Value
     *            Value to be made secure
     * @return string Secure value
     */
    protected function getDateValue(string $Value): string
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
    protected function getExternalValue(array $Value): array
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
    public function hasCustomFields(): bool
    {
        foreach ($this->FieldObjects as $Field) {
            if ($Field->getType() == 'custom') {
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
    public function getTypedValue(string $Type, $Value, bool $StoreFiles = true)
    {
        $Result = '';

        switch ($Type) {
            case ('integer'):
                $Result = intval($Value);
                break;

            case ('string'):
                $Result = \Mezon\Security\Security::getStringValue($Value);
                break;

            case ('file'):
                $Result = \Mezon\Security\Security::getFileValue($Value, $StoreFiles);
                break;

            case ('date'):
                $Result = $this->getDateValue($Value);
                break;

            case ('external'):
                $Result = $this->getExternalValue($Value);
                break;

            default:
                throw (new \Exception('Undefined type "' . $Type . '"'));
        }

        return ($Result);
    }

    /**
     * Method validates if the field $Field exists
     *
     * @param string $Field
     *            Field name
     */
    public function validateFieldExistance(string $Field)
    {
        if (! isset($this->FieldObjects[$Field])) {
            throw (new \Exception('Field "' . $Field . '" was not found'));
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
    public function getSecureValue(string $Field, $Value, bool $StoreFiles = true)
    {
        $this->validateFieldExistance($Field);

        return ($this->getTypedValue($this->FieldObjects[$Field]->getType(), $Value, $StoreFiles));
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
    public function getSecureValues(string $Field, $Values, bool $StoreFiles = true)
    {
        $Return = [];

        if (is_array($Values)) {
            foreach ($Values as $i => $Value) {
                $Return[$i] = $this->getSecureValue($Field, $Value, $StoreFiles);
            }
        } else {
            $Return = $this->getSecureValue($Field, $Values, $StoreFiles);
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
    public function getValuesForPrefix(string $Prefix, bool $StoreFiles = true): array
    {
        $Records = [];

        foreach (array_keys($this->FieldObjects) as $Name) {
            if (isset($_POST[$Prefix . $Name])) {
                $Records[$Name] = $this->getSecureValues($Name, $_POST[$Prefix . $Name], $StoreFiles);
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
    public function removeField($Name)
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
    public function fetchCustomField(array &$Record, string $Name): array
    {
        if (! isset($this->FieldObjects[$Name])) {
            return ($Record);
        }

        $NestedFields = $this->FieldObjects[$Name]->getFields();

        foreach ($NestedFields as $Name => $Field) {
            if (isset($_POST[$this->EntityName . '-' . $Name])) {
                $Record[$Name] = $this->getTypedValue($Field['type'], $_POST[$this->EntityName . '-' . $Name], true);
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
    public function fetchField(array &$Record, string $Name)
    {
        if (isset($_POST[$this->EntityName . '-' . $Name])) {
            $Record[$Name] = $this->getSecureValue($Name, $_POST[$this->EntityName . '-' . $Name]);
        } elseif (isset($_FILES[$this->EntityName . '-' . $Name])) {
            $Record[$Name] = $this->getSecureValue($Name, $_FILES[$this->EntityName . '-' . $Name]);
        } elseif ($this->hasCustomFields()) {
            $Record = $this->fetchCustomField($Record, $Name);
        }
    }

    /**
     * Factory method for creating controls
     *
     * @param array $Field
     *            field description
     * @return \Mezon\Gui\Field|\Mezon\Gui\Control constructed control
     */
    protected function constructControl(array $Field)
    {
        $TypeMap = [
            'external' => \Mezon\Gui\Field\CheckboxesField::class,
            'records' => \Mezon\Gui\Field\RecordField::class,
            'file' => \Mezon\Gui\Field\InputFile::class,
            'date' => \Mezon\Gui\Field\InputDate::class,
            'custom' => \Mezon\Gui\Field\CustomField::class,
            'header' => \Mezon\Gui\Field\FormHeader::class,
            'label' => \Mezon\Gui\Field\LabelField::class
        ];

        if (isset($Field['items'])) {
            return (new \Mezon\Gui\Field\Select($Field));
        } elseif (isset($Field['type'])) {
            if (in_array($Field['type'], array_keys($TypeMap))) {
                $ClassName = $TypeMap[$Field['type']];

                $Field['session-id'] = $this->SessionId;

                return (new $ClassName($Field));
            } else {
                return (new \Mezon\Gui\Field\InputText($Field));
            }
        }
    }

    /**
     * Method inits control
     *
     * @param array $Field
     *            Field
     * @return mixed Control
     */
    protected function initObject(array $Field)
    {
        if (isset($Field['control']) && $Field['control'] == 'textarea') {
            $Control = new \Mezon\Gui\Field\Textarea($Field);
        } elseif ($Field['type'] == 'rows') {
            // TODO move it to the constructor of \Mezon\Gui\FormBuilder\RowsField
            $ControlHTML = '';
            foreach ($Field['type']['rows'] as $RowFieldName) {
                $Control = $this->getObject($RowFieldName);
                $ControlHTML .= $Control->html();
            }
            $Control = new \Mezon\Gui\FormBuilder\RowsField($Field, $ControlHTML);
        } else {
            $Control = $this->constructControl($Field);
        }

        return ($Control);
    }

    /**
     * Method returns field object
     *
     * @param string $Name
     *            Field name
     * @return \Mezon\Gui\Field Field object
     */
    public function getObject(string $Name): \Mezon\Gui\Field
    {
        return ($this->FieldObjects[$Name]);
    }

    /**
     * Method compiles field DOM
     *
     * @param string $Name
     *            Field name
     */
    public function getCompiledField(string $Name)
    {
        $Control = $this->getObject($Name);

        return ($Control->html());
    }

    /**
     * Method returns array of fields names
     *
     * @return array
     */
    public function getFieldsNames(): array
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
    public function hasField(string $FieldName): bool
    {
        // @codeCoverageIgnoreStart
        return (isset($this->FieldObjects[$FieldName]));
        // @codeCoverageIgnoreEnd
    }

    /**
     * Method returns entity name
     *
     * @return string
     */
    public function getEntityName(): string
    {
        return ($this->EntityName);
    }

    /**
     * Method sets session id
     *
     * @param string $SessionId
     *            new session id
     */
    public function setSessionId(string $SessionId): void
    {
        $this->SessionId = $SessionId;
    }
}
