<?php
/**
 * Class GentellaTemplate
 *
 * @package     Mezon
 * @subpackage  GentellaTemplate
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/17)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../html-template/html-template.php');
require_once (__DIR__ . '/../template-engine/template-engine.php');
require_once (__DIR__ . '/../template-resources/template-resources.php');

/**
 * Template class
 */
class GentellaTemplate extends HTMLTemplate
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

        $this->set_page_var('action', '');
    }

    /**
     * Get close button markup
     *
     * @return string Close button markup
     */
    protected static function get_close_button(): string
    {
        return ('<button type="button" class="close" data-dismiss="alert" aria-label="Close">' . '<span aria-hidden="true">×</span></button>');
    }

    /**
     * Compilation of the message 
     * 
     * @param string $MsgType Type of the message
     * @param string $Message Message
     * @return string Message block markup
     */
    protected static function get_message_content(string $MsgType, string $Message): string
    {
        $Content  = '<div class="x_content" style="margin: 0; padding: 0;">';
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
    public static function success_message_content(string $Message): string
    {
        return(self::get_message_content('alert-success', $Message));
    }

    /**
     * Method compiles info message content
     *
     * @param string $Message
     *            Message to be compiled
     */
    public static function info_message_content(string $Message): string
    {
        return(self::get_message_content('alert-info', $Message));
    }

    /**
     * Method compiles warning message content
     *
     * @param string $Message
     *            Message to be compiled
     */
    public static function warning_message_content(string $Message): string
    {
        return(self::get_message_content('alert-warning', $Message));
    }

    /**
     * Method compiles danger message content
     *
     * @param string $Message
     *            Message to be compiled
     */
    public static function danger_message_content(string $Message): string
    {
        return(self::get_message_content('alert-danger', $Message));
    }
}

?>