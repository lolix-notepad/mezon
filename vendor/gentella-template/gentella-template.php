<?php
namespace Mezon\GentellaTemplate;

/**
 * Class GentellaTemplate
 *
 * @package Mezon
 * @subpackage GentellaTemplate
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

// TODO move it to separate package

/**
 * Template class
 */
class GentellaTemplate extends \Mezon\HtmlTemplate
{

    /**
     * Template сonstructor
     *
     * @param string $Template
     *            Page layout
     */
    public function __construct(string $Template = 'index')
    {
        parent::__construct(dirname(__FILE__), $Template);

        $this->setPageVar('action', '');
    }

    /**
     * Get close button markup
     *
     * @return string Close button markup
     */
    protected static function getCloseButton(): string
    {
        return ('<button type="button" class="close" data-dismiss="alert" aria-label="Close">' .
            '<span aria-hidden="true">×</span></button>');
    }

    /**
     * Compilation of the message
     *
     * @param string $MsgType
     *            Type of the message
     * @param string $Message
     *            Message
     * @return string Message block markup
     */
    protected static function getMessageContent(string $MsgType, string $Message): string
    {
        $Content = '<div class="x_content" style="margin: 0; padding: 0;">';
        $Content .= '<div class="alert ' . $MsgType . ' alert-dismissible fade in" role="alert">';
        $Content .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        $Content .= '<span aria-hidden="true">×</span></button>' . $Message . '</div></div>';

        return ($Content);
    }

    /**
     * Method compiles success message content
     *
     * @param string $Message
     *            Message to be compiled
     */
    public static function successMessageContent(string $Message): string
    {
        return (self::getMessageContent('alert-success', $Message));
    }

    /**
     * Method compiles info message content
     *
     * @param string $Message
     *            Message to be compiled
     */
    public static function infoMessageContent(string $Message): string
    {
        return (self::getMessageContent('alert-info', $Message));
    }

    /**
     * Method compiles warning message content
     *
     * @param string $Message
     *            Message to be compiled
     */
    public static function warningMessageContent(string $Message): string
    {
        return (self::getMessageContent('alert-warning', $Message));
    }

    /**
     * Method compiles danger message content
     *
     * @param string $Message
     *            Message to be compiled
     */
    public static function dangerMessageContent(string $Message): string
    {
        return (self::getMessageContent('alert-danger', $Message));
    }
}
