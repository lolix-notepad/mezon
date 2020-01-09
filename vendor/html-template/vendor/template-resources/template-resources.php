<?php
namespace Mezon\HtmlTemplate;

/**
 * Class TemplateResources
 *
 * @package Mezon
 * @subpackage TemplateResources
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Class collects resources for page.
 *
 * Any including components can add to the page their own resources without having access to the application or template.
 */
class TemplateResources
{

    /**
     * Custom CSS files to be included.
     */
    private static $CSSFiles = false;

    /**
     * Custom JS files to be included.
     */
    private static $JSFiles = false;

    /**
     * Constructor.
     */
    function __construct()
    {
        if (self::$CSSFiles === false) {
            self::$CSSFiles = [];
        }
        if (self::$JSFiles === false) {
            self::$JSFiles = [];
        }
    }

    /**
     * Additing single CSS file
     *
     * @param string $CSSFile
     *            CSS file
     */
    function addCssFile(string $CSSFile)
    {
        // additing only unique paths
        if (array_search($CSSFile, self::$CSSFiles) === false) {
            self::$CSSFiles[] = \Mezon\Conf::expandString($CSSFile);
        }
    }

    /**
     * Additing multyple CSS files
     *
     * @param array $CSSFiles
     *            CSS files
     */
    function addCssFiles(array $CSSFiles)
    {
        foreach ($CSSFiles as $CSSFile) {
            $this->addCssFile($CSSFile);
        }
    }

    /**
     * Method returning added CSS files
     */
    function getCssFiles()
    {
        return (self::$CSSFiles);
    }

    /**
     * Additing single CSS file
     *
     * @param string $JSFile
     *            JS file
     */
    function addJsFile($JSFile)
    {
        // additing only unique paths
        if (array_search($JSFile, self::$JSFiles) === false) {
            self::$JSFiles[] = \Mezon\Conf::expandString($JSFile);
        }
    }

    /**
     * Additing multyple CSS files
     *
     * @param array $JSFiles
     *            JS files
     */
    function addJsFiles(array $JSFiles)
    {
        foreach ($JSFiles as $JSFile) {
            $this->addJsFile($JSFile);
        }
    }

    /**
     * Method returning added JS files.
     */
    function getJsFiles()
    {
        return (self::$JSFiles);
    }

    /**
     * Method clears loaded resources.
     */
    function clear()
    {
        self::$CSSFiles = [];

        self::$JSFiles = [];
    }
}
