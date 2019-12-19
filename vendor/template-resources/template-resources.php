<?php
namespace Mezon;
/**
 * Class TemplateResources
 *
 * @package     Mezon
 * @subpackage  TemplateResources
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/17)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../conf/conf.php');

// TODO add camel-case
// TODO do we need this class?
// TODO may be we should move it somewhere?

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
			self::$CSSFiles = array();
		}
		if (self::$JSFiles === false) {
			self::$JSFiles = array();
		}
	}

	/**
	 * Additing single CSS file
	 *
	 * @param string $CSSFile
	 *        	CSS file
	 */
	function add_css_file(string $CSSFile)
	{
		// additing only unique paths
		if (array_search($CSSFile, self::$CSSFiles) === false) {
			self::$CSSFiles[] = _expand_string($CSSFile);
		}
	}

	/**
	 * Additing multyple CSS files
	 *
	 * @param array $CSSFiles
	 *        	CSS files
	 */
	function add_css_files(array $CSSFiles)
	{
		foreach ($CSSFiles as $CSSFile) {
			$this->add_css_file($CSSFile);
		}
	}

	/**
	 * Method returning added CSS files
	 */
	function get_css_files()
	{
		return (self::$CSSFiles);
	}

	/**
	 * Additing single CSS file
	 *
	 * @param string $JSFile
	 *        	JS file
	 */
	function add_js_file($JSFile)
	{
		// additing only unique paths
		if (array_search($JSFile, self::$JSFiles) === false) {
			self::$JSFiles[] = _expand_string($JSFile);
		}
	}

	/**
	 * Additing multyple CSS files
	 *
	 * @param array $JSFiles
	 *        	JS files
	 */
	function add_js_files(array $JSFiles)
	{
		foreach ($JSFiles as $JSFile) {
			$this->add_js_file($JSFile);
		}
	}

	/**
	 * Method returning added JS files.
	 */
	function get_js_files()
	{
		return (self::$JSFiles);
	}

	/**
	 * Method clears loaded resources.
	 */
	function clear()
	{
		self::$CSSFiles = array();

		self::$JSFiles = array();
	}
}

?>