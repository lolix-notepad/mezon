<?php

/**
 * Class FormBuilder
 *
 * @package     CRUDService
 * @subpackage  FormBuilder
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/13)
 * @copyright   Copyright (c) 2019, aeon.org
 */

/**
 * Form builder class
 */
class FormBuilder
{

    /**
     * Fields algorithms
     */
    var $FieldsAlgorithms = false;

    /**
     * Session id
     */
    var $SessionId = false;

    /**
     * Entity name
     */
    var $EntityName = false;

    /**
     * Layout
     */
    var $Layout = false;

    /**
     * Multiple forms
     */
    var $Batch = false;

    /**
     * Constructor
     *
     * @param FieldsAlgorithms $FieldsAlgorithms
     *            Fields algorithms
     * @param string $SessionId
     *            Session id
     * @param string $EntityName
     *            Entity name
     * @param array $Layout
     *            Fields layout
     * @param bool $Batch
     *            Batch operations available
     */
    public function __construct(FieldsAlgorithms $FieldsAlgorithms, string $SessionId, string $EntityName, array $Layout, bool $Batch = false)
    {
        $this->FieldsAlgorithms = $FieldsAlgorithms;

        $this->SessionId = $SessionId;

        $this->EntityName = $EntityName;

        $this->Layout = $Layout;

        $this->Batch = $Batch;
    }

    /**
     * Method compiles form without layout
     *
     * @param array $Record
     *            Data source
     * @return string Compiled control
     */
    protected function compile_for_fields_with_no_layout(array $Record = []): string
    {
        $Content = '';

        foreach ($this->FieldsAlgorithms->get_fields_names() as $Name) {
            $Field = $this->FieldsAlgorithms->get_object($Name);
            if ($Name == 'id' || $Name == 'domain_id' || $Name == 'creation_date' || $Name == 'modification_date' || $Field->is_visible() === false) {
                continue;
            }

            $Content .= '<div class="form-group ' . $this->EntityName . '">' . '<label class="control-label" >' . $Field->get_title() . ($Field->is_required($Name) ? ' <span class="required">*</span>' : '') . '</label>' . $Field->html() . '</div>';
        }

        return ($Content);
    }

    /**
     * Method compiles atoic field
     *
     * @param array $Field
     *            Field description
     * @param string $Name
     *            HTML field name
     * @return string Compiled field
     */
    protected function compile_field($Field, $Name)
    {
        $Control = $this->FieldsAlgorithms->get_compiled_field($Name);

        $FieldObject = $this->FieldsAlgorithms->get_object($Name);

        if ($FieldObject->fill_all_row()) {
            return ($Control->html());
        }

        if ($FieldObject->is_visible() === false) {
            return ('');
        }

        $Content = '<div class="form-group ' . $this->EntityName . ' col-md-' . $Field['width'] . '">';

        if ($FieldObject->has_label()) {
            $Content .= '<label class="control-label" style="text-align: left;">' . $FieldObject->get_title() . ($FieldObject->is_required($Name) ? ' <span class="required">*</span>' : '') . '</label>';
        }

        $Content .= $Control . '</div>';

        return ($Content);
    }

    /**
     * Method compiles form with layout
     *
     * @param array $Record
     *            Record
     * @return string Compiled fields
     */
    protected function compile_for_fields_with_layout(array $Record = []): string
    {
        $Content = '';

        foreach ($this->Layout['rows'] as $Row) {
            foreach ($Row as $Name => $Field) {
                $Content .= $this->compile_field($Field, $Name, $Record);
            }
        }

        return ($Content);
    }

    /**
     * Method returns amount of columns in the form
     *
     * @return string|integer Width of the column
     */
    protected function get_form_width()
    {
        if (isset($_GET['form-width'])) {
            return (intval($_GET['form-width']));
        } elseif ($this->Layout === false || count($this->Layout) === 0) {
            return (6);
        } else {
            return ($this->Layout['width']);
        }
    }

    /**
     * Method compiles form fields
     *
     * @param array $Record
     *            Record
     * @return string Compiled fields
     */
    public function compile_form_fields($Record = [])
    {
        if (count($this->Layout) === 0) {
            return ($this->compile_for_fields_with_no_layout($Record));
        } else {
            return ($this->compile_for_fields_with_layout($Record));
        }
    }

    /**
     * Method compiles creation form
     *
     * @return string Compiled creation form
     */
    public function creation_form(): string
    {
        if (isset($_GET['no-header'])) {
            $Content = file_get_contents(__DIR__ . '/res/templates/creation_form_no_header.tpl');
        } else {
            $Content = file_get_contents(__DIR__ . '/res/templates/creation_form_header.tpl');
        }

        $Content .= file_get_contents(__DIR__ . '/res/templates/creation_form.tpl');

        $BackLink = isset($_GET['back-link']) ? $_GET['back-link'] : '../list/';

        $Content = str_replace('{fields}', $this->compile_form_fields(), $Content);

        $Content = str_replace('{width}', $this->get_form_width(), $Content);

        $Content = str_replace('{back-link}', $BackLink, $Content);

        return ($Content);
    }

    /**
     * Method compiles updating form
     *
     * @param string $SessionId
     *            Session id
     * @param array $Record
     *            Record to be updated
     * @return string Compiled updating form
     */
    public function updating_form(string $SessionId, array $Record): string
    {
        if (isset($_GET['no-header'])) {
            $Content = file_get_contents(__DIR__ . '/res/templates/updating_form_no_header.tpl');
        } else {
            $Content = file_get_contents(__DIR__ . '/res/templates/updating_form_header.tpl');
        }

        $Content .= file_get_contents(__DIR__ . '/res/templates/updating_form.tpl');

        $this->SessionId = $SessionId;
        $this->FieldsAlgorithms->SessionId = $SessionId;

        $Content = str_replace('{fields}', $this->compile_form_fields($Record), $Content);

        return ($Content);
    }
}

?>