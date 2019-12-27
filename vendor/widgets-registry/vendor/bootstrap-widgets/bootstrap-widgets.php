<?php
namespace Mezon\WidgetsRegistry;

/**
 * Class BootstrapWidgets
 *
 * @package WidgetsRegistry
 * @subpackage BootstrapWidgets
 * @author Dodonov A.A.
 * @version v.1.0 (2019/09/02)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Bootstrap widgets
 */
class BootstrapWidgets implements WidgetsRegistryBase
{

    /**
     * Method returns widget
     *
     * @param string $Name
     *            Name of the widget
     * @return string Widget's HTML code
     */
    public function getWidget(string $Name): string
    {
        return (BootstrapWidgets::get($Name));
    }

    /**
     * Method returns widget
     *
     * @param string $Name
     *            Name of the widget
     * @return string Widget's HTML code
     */
    public static function get(string $Name): string
    {
        return (file_get_contents(__DIR__ . '/res/templates/' . $Name . '.tpl'));
    }
}

?>