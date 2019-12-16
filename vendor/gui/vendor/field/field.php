<?php

/**
 * Class Field
 *
 * @package     GUI
 * @subpackage  Field
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/20)
 * @copyright   Copyright (c) 2019, aeon.org
 */

/**
 * Base class for all other fields
 */
class Field
{

    /**
     * Name of the field
     *
     * @var string
     */
    protected $Name;

    /**
     * Is field required
     *
     * @var bool
     */
    protected $Required;

    /**
     * Field name's prefix
     *
     * @var string
     */
    protected $NamePrefix;

    /**
     * Is field custom
     *
     * @var bool
     */
    protected $Custom;

    /**
     * Is field batched
     *
     * @var bool
     */
    protected $Batch;

    /**
     * Is field disabled
     *
     * @var bool
     */
    protected $Disabled;

    /**
     * Toggler selector
     *
     * @var string
     */
    protected $Toggler;

    /**
     * Toggle value
     *
     * @var string
     */
    protected $ToggleValue;

    /**
     * Field value
     *
     * @var string
     */
    protected $Value;

    /**
     * Title value
     *
     * @var string
     */
    protected $Title = '';

    /**
     * Visible field or not
     *
     * @var boolean
     */
    protected $Visible = true;

    /**
     * Does field have got label or not
     *
     * @var boolean
     */
    protected $HasLabel = false;

    /**
     * FIeld type
     *
     * @var string
     */
    protected $Type = '';

    /**
     * Method fetches field name from the description
     *
     * @param array $FieldDescription
     *            Field description
     */
    protected function init_name(array $FieldDescription)
    {
        if (isset($FieldDescription['name'])) {
            $this->Name = $FieldDescription['name'];
        } else {
            throw (new Exception('Name of the field is not defined', - 1));
        }
    }

    /**
     * Method fetches field required property from the description
     *
     * @param array $FieldDescription
     *            Field description
     */
    protected function init_required(array $FieldDescription)
    {
        if (isset($FieldDescription['required']) === false) {
            $this->Required = false;
        } else {
            $this->Required = $FieldDescription['required'] == 1;
        }
    }

    /**
     * Method fetches field custom property from the description
     *
     * @param array $FieldDescription
     *            Field description
     */
    protected function init_custom(array $FieldDescription)
    {
        if (isset($FieldDescription['custom']) === false) {
            $this->Custom = false;
        } else {
            $this->Custom = $FieldDescription['custom'] == 1;
        }
    }

    /**
     * Method fetches field batch property from the description
     *
     * @param array $FieldDescription
     *            Field description
     */
    protected function init_batch(array $FieldDescription)
    {
        if (isset($FieldDescription['batch']) === false) {
            $this->Batch = false;
        } else {
            $this->Batch = $FieldDescription['batch'] == 1;
        }
    }

    /**
     * Method fetches field disabled property from the description
     *
     * @param array $FieldDescription
     *            Field description
     */
    protected function init_disabled(array $FieldDescription)
    {
        if (isset($FieldDescription['disabled']) === false) {
            $this->Disabled = false;
        } else {
            $this->Disabled = $FieldDescription['disabled'] == 1;
        }
    }

    /**
     * Method fetches field toggler property from the description
     *
     * @param array $FieldDescription
     *            Field description
     */
    protected function init_toggler(array $FieldDescription)
    {
        if (isset($FieldDescription['toggler']) === false) {
            $this->Toggler = '';
        } else {
            $this->Toggler = $FieldDescription['toggler'];
        }
    }

    /**
     * Method fetches field toggle value property from the description
     *
     * @param array $FieldDescription
     *            Field description
     */
    protected function init_toggle_value(array $FieldDescription)
    {
        if (isset($FieldDescription['toggle-value']) === false || $this->Toggler === '') {
            $this->ToggleValue = '';
        } else {
            $this->ToggleValue = $FieldDescription['toggle-value'];
        }
    }

    /**
     * Method fetches field title
     *
     * @param array $FieldDescription
     *            Field description
     */
    protected function init_title(array $FieldDescription)
    {
        if (isset($FieldDescription['title']) === true) {
            $this->Title = $FieldDescription['title'];
        }
    }

    /**
     * Method fetches field's visibility
     *
     * @param array $FieldDescription
     *            Field description
     */
    protected function init_visible(array $FieldDescription)
    {
        if (isset($FieldDescription['visible']) === true) {
            $this->Visible = $FieldDescription['visible'] == 1;
        }
    }

    /**
     * Method fetches label display
     *
     * @param array $FieldDescription
     *            Field description
     */
    protected function init_has_label(array $FieldDescription)
    {
        if (isset($FieldDescription['has-label']) === true) {
            $this->HasLabel = $FieldDescription['has-label'] == 1;
        }
    }

    /**
     * Constructor
     *
     * @param array $FieldDescription
     *            Field description
     * @param string $Value
     *            Field value
     */
    public function __construct(array $FieldDescription, string $Value = '')
    {
        $this->init_name($FieldDescription);

        $this->init_required($FieldDescription);

        $this->NamePrefix = $FieldDescription['name-prefix'];

        $this->init_custom($FieldDescription);

        $this->init_batch($FieldDescription);

        $this->init_disabled($FieldDescription);

        $this->init_toggler($FieldDescription);

        $this->init_toggle_value($FieldDescription);

        $this->init_title($FieldDescription);

        $this->init_visible($FieldDescription);

        $this->init_has_label($FieldDescription);

        $this->Value = $Value;

        $this->Type = $FieldDescription['type'];
    }

    /**
     * Method returns compiled field
     *
     * @return string Compiled field
     */
    public function html(): string
    {
        return ($this->NamePrefix . $this->Name . ($this->Required ? 1 : 0) . ($this->Custom ? 1 : 0) . ($this->Batch ? 1 : 0) . ($this->Disabled ? 1 : 0) . $this->Toggler . $this->ToggleValue);
    }

    /**
     * Method returns title
     *
     * @return string Title
     */
    public function get_title(): string
    {
        return ($this->Title);
    }

    /**
     * Method returns of the field required
     *
     * @return bool Is field required
     */
    public function is_required(): bool
    {
        return ($this->Required);
    }

    /**
     * Method returns field's visibility
     *
     * @return bool Is field visible
     */
    public function is_visible(): bool
    {
        return ($this->Visible);
    }

    /**
     * Method returns field's label existence
     *
     * @return bool Has label
     */
    public function has_label(): bool
    {
        return ($this->HasLabel);
    }

    /**
     * Does control fills all row
     *
     * @return bool Dows field fills hole row
     */
    public function fill_all_row(): bool
    {
        return (false);
    }

    /**
     * Getting field type
     *
     * @return string Field type
     */
    public function get_type(): string
    {
        return ($this->Type);
    }
}

?>