<?php
namespace Mezon\GUI\Field;

/**
 * Class FormHeader
 *
 * @package Field
 * @subpackage FormHeader
 * @author Dodonov A.A.
 * @version v.1.0 (2019/09/04)
 * @copyright Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../text-field/text-field.php');

// TODO add camel-case
/**
 * Form header control
 */
class FormHeader extends \Mezon\GUI\Field\TextField
{

    /**
     * Generating input feld
     *
     * @return string HTML representation of the input field
     */
    public function html(): string
    {
        $Content = '<div class="form-group col-md-12">';
        $Content .= strlen($this->Text) ? '<h3>' . $this->Text . '</h3>' : '';
        $Content .= '</div>';

        return ($Content);
    }

    /**
     * Does control fills all row
     */
    public function fill_all_row(): bool
    {
        return (false);
    }
}

?>