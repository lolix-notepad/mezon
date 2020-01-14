<?php
namespace Mezon\HtmlTemplate;

/**
 * Class HtmlTemplate
 *
 * @package Mezon
 * @subpackage HtmlTemplate
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/07)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Template class
 *
 * @author Dodonov A.A.
 */
class HtmlTemplate
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

        $this->resetLayout($Template);

        $this->Resources = new \Mezon\HtmlTemplate\TemplateResources();

        $this->Blocks = [];

        foreach ($Blocks as $BlockName) {
            $this->addBlock($BlockName);
        }

        // output all blocks in one place
        // but each block can be inserted in {$BlockName} places
        $this->setPageVar('content-blocks', implode('', $this->Blocks));
    }

    /**
     * Setting page variables
     *
     * @param string $Var
     *            Variable name
     * @param mixed $Value
     *            Variable value
     */
    public function setPageVar(string $Var, $Value): void
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
    public function setPageVarFromFile(string $Var, string $Path): void
    {
        $this->setPageVar($Var, file_get_contents($Path));
    }

    /**
     * Compiling the page with it's variables
     *
     * @param string $Content
     *            Compiling content
     */
    public function compilePageVars(string &$Content): void
    {
        foreach ($this->PageVars as $Key => $Value) {
            if (is_array($Value) === false && is_object($Value) === false) {
                // only scalars can be substituted in this way
                $Content = str_replace('{' . $Key . '}', $Value, $Content);
            }
        }

        $Content = TemplateEngine::unwrapBlocks($Content, $this->PageVars);

        $Content = TemplateEngine::compileSwitch($Content);
    }

    /**
     * Method resets layout
     *
     * @param string $Template
     *            Template name
     */
    public function resetLayout(string $Template): void
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
    private function _getResources(): string
    {
        $Content = '';

        $CSSFiles = $this->Resources->getCssFiles();
        foreach ($CSSFiles as $CSSFile) {
            $Content .= '
        <link href="' . $CSSFile . '" rel="stylesheet" type="text/css">';
        }

        $JSFiles = $this->Resources->getJsFiles();
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
        $this->setPageVar('resources', $this->_getResources());
        $this->setPageVar('mezon-http-path', \Mezon\Conf::getConfigValue('@mezon-http-path'));
        $this->setPageVar('service-http-path', \Mezon\Conf::getConfigValue('@service-http-path'));
        if (isset($_SERVER['HTTP_HOST'])) {
            $this->setPageVar('host', $_SERVER['HTTP_HOST']);
        }

        foreach ($this->Blocks as $BlockName => $Block) {
            $this->setPageVar($BlockName, $Block);
        }

        $this->compilePageVars($this->Template);

        $this->Template = preg_replace('/\{[a-zA-z0-9\-]*\}/', '', $this->Template);

        return ($this->Template);
    }

    /**
     * Method returns block's content
     *
     * @param string $BlockName
     * @return string block's content
     */
    protected function readBlock(string $BlockName): string
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
    public function addBlock(string $BlockName): void
    {
        $this->Blocks[$BlockName] = $this->readBlock($BlockName);
    }

    /**
     * Method returns block's content
     *
     * @param string $BlockName
     * @return string block's content
     */
    public function getBlock(string $BlockName): string
    {
        return ($this->readBlock($BlockName));
    }
}
