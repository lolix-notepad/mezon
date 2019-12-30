<?php
namespace Mezon\Gui\Field;

/**
 * Class CustomField
 *
 * @package Field
 * @subpackage CustomField
 * @author Dodonov A.A.
 * @version v.1.0 (2019/09/13)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Custom field control
 */
class CustomField extends \Mezon\Gui\Field
{

    /**
     * Custom field's parts
     *
     * @var array
     */
    protected $Fields = [];

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

        $this->Fields = $FieldDescription['fields'];
    }

    /**
     * Method returns field's template
     *
     * @return string field's template
     */
    protected function getFieldTemplate(): string
    {
        // @codeCoverageIgnoreStart
        $Content = file_get_contents('./res/templates/field-' . $this->Name . '.tpl');

        if ($Content === false) {
            throw (new \Exception('Template field-' . $this->Name . '.tpl was not found'));
        }

        return ($Content);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Generating custom feld
     *
     * @return string HTML representation of the custom field
     */
    public function html(): string
    {
        $Content = \Mezon\TemplateEngine::printRecord($this->get_field_template(), [
            'name' => $this->Name,
            'name-prefix' => $this->NamePrefix,
            'disabled' => $this->Disabled ? 1 : 0,
            'batch' => $this->Batch ? 1 : 0,
            'custom' => $this->Custom,
            'required' => $this->Required ? 1 : 0,
            'toggler' => $this->Toggler,
            'toggle-value' => $this->ToggleValue
        ]);

        return ($Content);
    }

    /**
     * Method returns parts of the custom field
     *
     * @return array parts of the custom field
     */
    public function getFields(): array
    {
        return ($this->Fields);
    }
}

?>