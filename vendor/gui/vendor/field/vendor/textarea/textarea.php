<?php
namespace Mezon\GUI\Field;
/**
 * Class Textarea
 *
 * @package     Field
 * @subpackage  Textarea
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/09/04)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../field.php');

// TODO add camel-case

/**
 * Text area control
 */
class Textarea extends \Mezon\GUI\Field
{

    /**
     * Generating textarea field
     *
     * @return string HTML representation of the textarea field
     */
    public function html(): string
    {
        $Content = '<textarea class="resizable_textarea form-control"';
        $Content .= $this->Required ? ' required="required"' : '';
        $Content .= ' type="text" name="' . $this->NamePrefix . '-' . $this->Name . ($this->Batch ? '[{_creation_form_items_counter}]' : '') . '"';
        $Content .= $this->Disabled ? ' disabled ' : '';
        $Content .= $this->Toggler === '' ? '' : 'toggler="' . $this->Toggler . '" ';
        $Content .= $this->Toggler === '' ? '' : 'toggle-value="' . $this->ToggleValue . '"';
        $Content .= '>' . $this->Value;
        $Content .= '</textarea>';

        return ($Content);
    }
}

?>