<?php
namespace Mezon\GUI\Field;

/**
 * Class TextField
 *
 * @package Field
 * @subpackage TextField
 * @author Dodonov A.A.
 * @version v.1.0 (2019/09/04)
 * @copyright Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../control/control.php');

// TODO add camel-case
/**
 * Text field control
 */
class TextField implements \Mezon\GUI\Control
{

    /**
     * Text content
     *
     * @var string
     */
    var $Text = '';

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
    public function fill_all_row(): bool
    {
        return (true);
    }
}

?>