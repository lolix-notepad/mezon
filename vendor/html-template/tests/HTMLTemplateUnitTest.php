<?php
require_once (__DIR__ . '/../../../autoloader.php');

class HtmlTemplateUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing construction with default path
     */
    public function testConstructor1()
    {
        // setup and test body
        $Template = new \Mezon\HtmlTemplate(__DIR__ . '/test-data/', 'index', [
            'main'
        ]);

        $Content = $Template->compile();

        // assertions
        $this->assertContains('<body>', $Content, 'Layout was not setup');
        $this->assertContains('<section>', $Content, 'Block was not setup');
    }

    /**
     * Testing construction with flexible path
     */
    public function testConstructor2()
    {
        // setup and test body
        $Template = new \Mezon\HtmlTemplate(__DIR__ . '/test-data/res/', 'index2', [
            'main'
        ]);

        $Content = $Template->compile();

        // assertions
        $this->assertContains('<body>', $Content, 'Layout was not setup');
        $this->assertContains('<section>', $Content, 'Block was not setup');
    }

    /**
     * Testing invalid construction
     */
    public function testInvalidConstructor()
    {
        try {
            // setup and test body
            $Template = new \Mezon\HtmlTemplate(__DIR__, 'index2', [
                'main'
            ]);
            $this->fail('Exception must be thrown ' . serialize($Template));
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing that all unused place holders will be removed
     */
    public function testCompile()
    {
        // setup
        $Template = new \Mezon\HtmlTemplate(__DIR__ . '/test-data/res/', 'index2', [
            'main'
        ]);
        $_SERVER['HTTP_HOST'] = 'host';

        // test body
        $Result = $Template->compile();

        // assertions
        $this->assertNotContains('{title}', $Result);
    }

    /**
     * Testing unexisting block
     */
    public function testGetUnexistingBlock()
    {
        // setup and test body
        $Template = new \Mezon\HtmlTemplate(__DIR__ . '/test-data/', 'index', [
            'main'
        ]);

        try {
            // test body
            $Template->getBlock('unexisting');

            // assertions
            $this->fail('Exception wile block reading must be thrown but it was not');
        } catch (Exception $e) {
            // assertions
            $this->addToAssertionCount(1);
        }
    }
}
