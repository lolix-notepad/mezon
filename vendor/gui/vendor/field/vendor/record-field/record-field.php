<?php
namespace Mezon\GUI\Field;
/**
 * Class RecordField
 *
 * @package     Field
 * @subpackage  RecordField
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/09/13)
 * @copyright   Copyright (c) 2019, aeon.org
 */

/**
 * Record field control
 */
class RecordField extends RemoteField
{

    /**
     * Bind field
     *
     * @var string
     */
    var $BindField = '';

    /**
     * Layout
     *
     * @var array
     */
    var $Layout = [];

    /**
     * Method fetches bind field property from the description
     *
     * @param array $FieldDescription
     *            Field description
     */
    protected function initBindField(array $FieldDescription)
    {
        if (isset($FieldDescription['bind-field'])) {
            $this->BindField = $FieldDescription['bind-field'];
        } else {
            throw (new \Exception('Bind field is not defined', - 1));
        }
    }

    /**
     * Method fetches layout from the description
     *
     * @param array $FieldDescription
     *            Field description
     */
    protected function initLayout(array $FieldDescription)
    {
        if (isset($FieldDescription['layout'])) {
            $this->Layout = $FieldDescription['layout'];
        } else {
            throw (new \Exception('Layout is not defined', - 1));
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
        parent::__construct($FieldDescription, $Value);

        $this->initBindField($FieldDescription);

        $this->initLayout($FieldDescription);
    }

    /**
     * Getting list of records
     *
     * @return array List of records
     */
    protected function getFields(): array
    {
        // @codeCoverageIgnoreStart
        return ($this->getClient()->getRemoteCreationFormFieldsJson());
        // @codeCoverageIgnoreEnd
    }

    /**
     * Generating records feld
     *
     * @return string HTML representation of the records field
     */
    public function html(): string
    {
        // getting fields
        $FormFields = new \Mezon\GUI\FieldsAlgorithms($this->getFields(), $this->NamePrefix);
        $FormFields->removeField($this->BindField);

        // getting form
        $FormBuilder = new \Mezon\GUI\FormBuilder($FormFields, $this->SessionId, $this->NamePrefix, $this->Layout, $this->Batch);

        // getting HTML
        return ($FormBuilder->compileFormFields());
    }
}

?>