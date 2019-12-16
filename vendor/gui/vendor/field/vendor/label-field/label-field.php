<?php

/**
 * Class LabelField
 *
 * @package     Field
 * @subpackage  LabelField
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/09/04)
 * @copyright   Copyright (c) 2019, aeon.org
 */

require_once (__DIR__ . '/../text-field/text-field.php');

/**
 * Form header control
 */
class LabelField extends TextField
{

    /**
     * Generating input feld
     *
     * @return string HTML representation of the input field
     */
    public function html(): string
    {
        $Content = '<div class="form-group col-md-12">';
        $Content .= '<label class="control-label">' . $this->Text . '</label>';
        $Content .= '</div>';

        return ($Content);
    }
}

?>