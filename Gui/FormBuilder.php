<?php
namespace Mezon\Gui;

/**
 * Class FormBuilder
 *
 * @package Gui
 * @subpackage FormBuilder
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/13)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Form builder class
 */
class FormBuilder
{

    /**
     * Fields algorithms
     */
    protected $FieldsAlgorithms = false;

    /**
     * Session id
     */
    protected $SessionId = false;

    /**
     * Entity name
     */
    protected $EntityName = false;

    /**
     * Layout
     */
    protected $Layout = false;

    /**
     * Multiple forms
     */
    protected $Batch = false;

    /**
     * Constructor
     *
     * @param \Mezon\Gui\FieldsAlgorithms $FieldsAlgorithms
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
    public function __construct(
        \Mezon\Gui\FieldsAlgorithms $FieldsAlgorithms,
        string $SessionId,
        string $EntityName,
        array $Layout,
        bool $Batch = false)
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
    protected function compileForFieldsWithNoLayout(array $Record = []): string
    {
        $Content = '';

        foreach ($this->FieldsAlgorithms->getFieldsNames() as $Name) {
            $Field = $this->FieldsAlgorithms->getObject($Name);
            if ($Name == 'id' || $Name == 'domain_id' || $Name == 'creation_date' || $Name == 'modification_date' ||
                $Field->isVisible() === false) {
                continue;
            }

            $Content .= '<div class="form-group ' . $this->EntityName . '">' . '<label class="control-label" >' .
                $Field->getTitle() . ($Field->isRequired($Name) ? ' <span class="required">*</span>' : '') . '</label>' .
                $Field->html() . '</div>';
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
    protected function compileField($Field, $Name)
    {
        $Control = $this->FieldsAlgorithms->getCompiledField($Name);

        $FieldObject = $this->FieldsAlgorithms->getObject($Name);

        if ($FieldObject->fillAllRow()) {
            return ($Control->html());
        }

        if ($FieldObject->isVisible() === false) {
            return ('');
        }

        $Content = '<div class="form-group ' . $this->EntityName . ' col-md-' . $Field['width'] . '">';

        if ($FieldObject->hasLabel()) {
            $Content .= '<label class="control-label" style="text-align: left;">' . $FieldObject->getTitle() .
                ($FieldObject->isRequired($Name) ? ' <span class="required">*</span>' : '') . '</label>';
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
    protected function compileForFieldsWithLayout(array $Record = []): string
    {
        $Content = '';

        foreach ($this->Layout['rows'] as $Row) {
            foreach ($Row as $Name => $Field) {
                $Content .= $this->compileField($Field, $Name, $Record);
            }
        }

        return ($Content);
    }

    /**
     * Method returns amount of columns in the form
     *
     * @return string|int Width of the column
     */
    protected function getFormWidth()
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
    public function compileFormFields($Record = [])
    {
        if (count($this->Layout) === 0) {
            return ($this->compileForFieldsWithNoLayout($Record));
        } else {
            return ($this->compileForFieldsWithLayout($Record));
        }
    }

    /**
     * Method compiles creation form
     *
     * @return string Compiled creation form
     */
    public function creationForm(): string
    {
        if (isset($_GET['no-header'])) {
            $Content = file_get_contents(__DIR__ . '/res/templates/creation_form_no_header.tpl');
        } else {
            $Content = file_get_contents(__DIR__ . '/res/templates/creation_form_header.tpl');
        }

        $Content .= file_get_contents(__DIR__ . '/res/templates/creation_form.tpl');

        $BackLink = isset($_GET['back-link']) ? $_GET['back-link'] : '../list/';

        $Content = str_replace('{fields}', $this->compileFormFields(), $Content);

        $Content = str_replace('{width}', $this->getFormWidth(), $Content);

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
    public function updatingForm(string $SessionId, array $Record): string
    {
        if (isset($_GET['no-header'])) {
            $Content = file_get_contents(__DIR__ . '/res/templates/updating_form_no_header.tpl');
        } else {
            $Content = file_get_contents(__DIR__ . '/res/templates/updating_form_header.tpl');
        }

        $Content .= file_get_contents(__DIR__ . '/res/templates/updating_form.tpl');

        $this->SessionId = $SessionId;
        $this->FieldsAlgorithms->setSessionId($SessionId);

        $Content = str_replace('{fields}', $this->compileFormFields($Record), $Content);

        return ($Content);
    }
}
