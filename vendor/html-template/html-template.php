<?php
namespace Mezon;
/**
 * Class HTMLTemplate
 *
 * @package     Mezon
 * @subpackage  HTMLTemplate
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/07)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../template-engine/template-engine.php');
require_once (__DIR__ . '/../template-resources/template-resources.php');

// TODO add camel-case
/**
 * Template class
 *
 * @author Dodonov A.A.
 */
class HTMLTemplate
{

    /**
     * Loaded template content
     */
    private $Template = false;

    /**
     * Loaded resources
     */
    private $Resources = false;

    /**
     * Path to the template folder
     */
    private $Path = false;

    /**
     * Page blocks
     */
    private $Blocks = [];

    /**
     * Page variables
     */
    private $PageVars = array();

    /**
     * Template сonstructor
     *
     * @param string $Path
     *            Path to template
     * @param string $Template
     *            Page layout
     * @param array $Blocks
     *            Page blocks
     */
    public function __construct(string $Path, string $Template = 'index', array $Blocks = [])
    {
        $this->Path = $Path;

        $this->reset_layout($Template);

        $this->Resources = new \Mezon\TemplateResources();

        $this->Blocks = [];

        foreach ($Blocks as $BlockName) {
            $this->add_block($BlockName);
        }

        // output all blocks in one place
        // but each block can be inserted in {$BlockName} places
        $this->set_page_var('content-blocks', implode('', $this->Blocks));
    }

    /**
     * Setting page variables
     *
     * @param string $Var
     *            Variable name
     * @param mixed $Value
     *            Variable value
     */
    public function set_page_var(string $Var, $Value): void
    {
        $this->PageVars[$Var] = $Value;
    }

    /**
     * Setting page variables from file's content
     *
     * @param string $Var
     *            Variable name
     * @param mixed $Path
     *            Path to file
     */
    public function set_page_var_from_file(string $Var, string $Path): void
    {
        $this->set_page_var($Var, file_get_contents($Path));
    }

    /**
     * Compiling the page with it's variables
     *
     * @param string $Content
     *            Compiling content
     */
    public function compile_page_vars(string &$Content): void
    {
        foreach ($this->PageVars as $Key => $Value) {
            if (is_array($Value) === false && is_object($Value) === false) {
                // only scalars can be substituted in this way
                $Content = str_replace('{' . $Key . '}', $Value, $Content);
            }
        }

        $Content = TemplateEngine::unwrap_blocks($Content, $this->PageVars);

        $Content = TemplateEngine::compile_switch($Content);
    }

    /**
     * Method resets layout
     *
     * @param string $Template
     *            Template name
     */
    public function reset_layout(string $Template): void
    {
        $Template .= '.html';

        if (file_exists($this->Path . '/' . $Template)) {
            $this->Template = file_get_contents($this->Path . '/' . $Template);
        } elseif (file_exists($this->Path . '/res/templates/' . $Template)) {
            $this->Template = file_get_contents($this->Path . '/res/templates/' . $Template);
        } else {
            throw (new \Exception('Template file on the path ' . $this->Path . ' was not found', - 1));
        }
    }

    /**
     * Method returns compiled page resources
     *
     * @return string Compiled resources includers
     */
    private function get_resources(): string
    {
        $Content = '';

        $CSSFiles = $this->Resources->get_css_files();
        foreach ($CSSFiles as $CSSFile) {
            $Content .= '
        <link href="' . $CSSFile . '" rel="stylesheet" type="text/css">';
        }

        $JSFiles = $this->Resources->get_js_files();
        foreach ($JSFiles as $JSFile) {
            $Content .= '
        <script src="' . $JSFile . '"></script>';
        }

        return ($Content);
    }

    /**
     * Compile template
     *
     * @return string Compiled template
     */
    public function compile(): string
    {
        $this->set_page_var('resources', $this->get_resources());
        $this->set_page_var('mezon-http-path', get_config_value('@mezon-http-path'));
        $this->set_page_var('service-http-path', get_config_value('@service-http-path'));
        if (isset($_SERVER['HTTP_HOST'])) {
            $this->set_page_var('host', $_SERVER['HTTP_HOST']);
        }

        foreach ($this->Blocks as $BlockName => $Block) {
            $this->set_page_var($BlockName, $Block);
        }

        $this->compile_page_vars($this->Template);

        $this->Template = preg_replace('/\{[a-zA-z0-9\-]*\}/', '', $this->Template);

        return ($this->Template);
    }

    /**
     * Method returns block's content
     *
     * @param string $BlockName
     * @return string block's content
     */
    protected function read_block(string $BlockName): string
    {
        if (file_exists($this->Path . '/res/blocks/' . $BlockName . '.tpl')) {
            $BlockContent = file_get_contents($this->Path . '/res/blocks/' . $BlockName . '.tpl');
        } elseif (file_exists($this->Path . '/blocks/' . $BlockName . '.tpl')) {
            $BlockContent = file_get_contents($this->Path . '/blocks/' . $BlockName . '.tpl');
        } else {
            throw (new \Exception('Block ' . $BlockName . ' was not found', - 1));
        }

        return ($BlockContent);
    }

    /**
     * Method adds block to page
     *
     * @param string $BlockName
     *            Name of the additing block
     */
    public function add_block(string $BlockName): void
    {
        $this->Blocks[$BlockName] = $this->read_block($BlockName);
    }

    /**
     * Method returns block's content
     *
     * @param string $BlockName
     * @return string block's content
     */
    public function get_block(string $BlockName): string
    {
        return ($this->read_block($BlockName));
    }
}

?>