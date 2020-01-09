<?php
namespace Mezon\Gui\Field;

/**
 * Class TextField
 *
 * @package Field
 * @subpackage TextField
 * @author Dodonov A.A.
 * @version v.1.0 (2019/09/04)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Text field control
 */
class TextField implements \Mezon\Gui\Control
{

    /**
     * Text content
     *
     * @var string
     */
    protected $Text = '';

    /**
     * Constructor
     *
     * @param array $FieldDescription
     *            Field description
     */
    public function __construct(array $FieldDescription)
    {
        if (isset($FieldDescription['text'])) {
            $this->Text = $FieldDescription['text'];
        }
    }

    /**
     * Generating input feld
     *
     * @return string HTML representation of the input field
     */
    public function html(): string
    {
        return ($this->Text);
    }

    /**
     * Does control fills all row
     */
    public function fillAllRow(): bool
    {
        return (true);
    }
}
