<?php
namespace Mezon\Application\ViewInterface;

/**
 * Interface ViewInterface
 *
 * @package Application
 * @subpackage ViewInterface
 * @author Dodonov A.A.
 * @version v.1.0 (2020/01/12)
 * @copyright Copyright (c) 2020, aeon.org
 */

/**
 * Base interface for all views
 */
interface ViewInterface
{

    /**
     * Method renders content from view
     *
     * @param string $ViewName
     *            View name to be rendered
     * @return string Generated content
     */
    public function render(string $ViewName = ''): string;

    /**
     * Method returns view name
     *
     * @return string view name
     */
    public function getViewName(): string;
}
