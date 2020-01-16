<?php
namespace Mezon\Gui\Field;

/**
 * Class Select
 *
 * @package Field
 * @subpackage Select
 * @author Dodonov A.A.
 * @version v.1.0 (2019/09/04)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Text area control
 */
class Select extends \Mezon\Gui\Field
{

    /**
     * Control items
     *
     * @var array
     */
    protected $Items = [];

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
        parent::__construct($FieldDescription, $Value);

        $ItemsSource = $FieldDescription['items'];

        if (is_string($ItemsSource) && function_exists($ItemsSource)) {
            // callback function forms a list of items
            $this->Items = $ItemsSource();
        } else {
            $this->Items = $ItemsSource;
        }
    }

    /**
     * Generating textarea field
     *
     * @return string HTML representation of the textarea field
     */
    public function html(): string
    {
        $Content = '<select class="form-control"';
        $Content .= $this->Required ? ' required="required"' : '';
        $Content .= ' type="text" name="' . $this->NamePrefix . '-' . $this->Name .
            ($this->Batch ? '[{_creation_form_items_counter}]' : '') . '"';
        $Content .= $this->Disabled ? ' disabled ' : '';
        $Content .= $this->Toggler === '' ? '' : 'toggler="' . $this->Toggler . '" ';
        $Content .= $this->Toggler === '' ? '' : 'toggle-value="' . $this->ToggleValue . '" ';
        $Content .= 'value="' . $this->Value . '">';

        foreach ($this->Items as $id => $Title) {
            $Content .= '<option value="' . $id . '">' . $Title . '</option>';
        }

        $Content .= '</select>';

        return ($Content);
    }
}
