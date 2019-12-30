<?php
require_once (__DIR__ . '/../../../../../autoloader.php');

class TemplateResourcesTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing additing CSS file.
     */
    public function testAdditingSingleCSSFile()
    {
        $TemplateResources = new \Mezon\HtmlTemplate\TemplateResources();

        $this->assertEquals(0, count($TemplateResources->getCssFiles()), 'CSS files array must be empty');

        $TemplateResources->addCssFile('./res/test.css');

        $this->assertEquals(1, count($TemplateResources->getCssFiles()), 'CSS files array must be NOT empty');

        $TemplateResources->clear();
    }

    /**
     * Testing additing CSS files.
     */
    public function testAdditingMultypleCSSFiles()
    {
        $TemplateResources = new \Mezon\HtmlTemplate\TemplateResources();

        $this->assertEquals(0, count($TemplateResources->getCssFiles()), 'CSS files array must be empty');

        $TemplateResources->addCssFiles([
            './res/test.css',
            './res/test2.css'
        ]);

        $this->assertEquals(2, count($TemplateResources->getCssFiles()), 'CSS files array must be NOT empty');

        $TemplateResources->clear();
    }

    /**
     * Testing additing CSS files.
     */
    public function testDoublesCSSExcluding()
    {
        $TemplateResources = new \Mezon\HtmlTemplate\TemplateResources();

        $this->assertEquals(0, count($TemplateResources->getCssFiles()), 'CSS files array must be empty');

        $TemplateResources->addCssFiles([
            './res/test.css',
            './res/test.css'
        ]);

        $this->assertEquals(1, count($TemplateResources->getCssFiles()), 'Only one path must be added');

        $TemplateResources->addCssFile('./res/test.css');

        $this->assertEquals(1, count($TemplateResources->getCssFiles()), 'Only one path must be added');

        $TemplateResources->clear();
    }

    /**
     * Testing additing JS file.
     */
    public function testAdditingSingleJSFile()
    {
        $TemplateResources = new \Mezon\HtmlTemplate\TemplateResources();

        $this->assertEquals(0, count($TemplateResources->getJsFiles()), 'JS files array must be empty');

        $TemplateResources->addJsFile('./include/js/test.js');

        $this->assertEquals(1, count($TemplateResources->getJsFiles()), 'JS files array must be NOT empty');

        $TemplateResources->clear();
    }

    /**
     * Testing additing JS files.
     */
    public function testAdditingMultypleJSFiles()
    {
        $TemplateResources = new \Mezon\HtmlTemplate\TemplateResources();

        $this->assertEquals(0, count($TemplateResources->getJsFiles()), 'JS files array must be empty');

        $TemplateResources->addJsFiles([
            './include/js/test.js',
            './include/js//test2.js'
        ]);

        $this->assertEquals(2, count($TemplateResources->getJsFiles()), 'JS files array must be NOT empty');

        $TemplateResources->clear();
    }

    /**
     * Testing additing JS files.
     */
    public function testDoublesJSExcluding()
    {
        $TemplateResources = new \Mezon\HtmlTemplate\TemplateResources();

        $this->assertEquals(0, count($TemplateResources->getJsFiles()), 'JS files array must be empty');

        $TemplateResources->addJsFiles([
            './include/js/test.js',
            './include/js/test.js'
        ]);

        $this->assertEquals(1, count($TemplateResources->getJsFiles()), 'Only one path must be added');

        $TemplateResources->addJsFile('./include/js/test.js');

        $this->assertEquals(1, count($TemplateResources->getJsFiles()), 'Only one path must be added');

        $TemplateResources->clear();
    }
}

?>