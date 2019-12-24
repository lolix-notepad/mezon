<?php
namespace Mezon\GUI\FormBuilder;
/**
 * Class RowsField
 *
 * @package     FormBuidler
 * @subpackage  RowsField
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/09/22)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../field/field.php');

/**
 * Rows field control
 */
class RowsField extends \Mezon\GUI\Field
{

    /**
     * Rowed field content
     *
     * @var string
     */
    var $RowedField = '';

    /**
     * Constructor
     *
     * @param array $FieldDescription
     *            Field description
     * @param string $RowedField
     *            Compiled field to be rowed
     */
    public function __construct(array $FieldDescription, string $RowedField)
    {
        $this->RowedField = $RowedField;
    }

    /**
     * Generating input feld
     *
     * @return string HTML representation of the input field
     */
    public function html(): string
    {
        $Content = '<div><div class="form-group col-md-12">';
        $Content .= '<button class="btn btn-success col-md-2" onclick="add_element_by_template( this , \'' . $this->Name . '\' )">+</button>';
        $Content .= '</div></div>';

        $Content = str_replace('{_creation_form_items_counter}', '0', $Content);

        $Content .= '<template class="' . $this->Name . '"><div>';
        $Content .= $this->RowedField;
        $Content .= '<div class="form-group col-md-12">';
        $Content .= '<button class="btn btn-success col-md-2" onclick="add_element_by_template( this , \'' . $this->Name . '\' );">+</button>';
        $Content .= '<button class="btn btn-danger col-md-2" onclick="remove_element_by_template( this );">-</button>';
        $Content .= '</div></div>';
        $Content .= '</template>';

        return ($Content);
    }

    /**
     * Does control fills all row
     */
    public function fillAllRow(): bool
    {
        return (true);
    }
}

?>