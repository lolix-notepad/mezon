<?php

class HtmlTemplateUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing construction with default path
     */
    public function testConstructor1()
    {
        // setup and test body
        $Template = new \Mezon\HtmlTemplate\HtmlTemplate(__DIR__ . '/test-data/', 'index', [
            'main'
        ]);

        $Content = $Template->compile();

        // assertions
        $this->assertStringContainsString('<body>', $Content, 'Layout was not setup');
        $this->assertStringContainsString('<section>', $Content, 'Block was not setup');
    }

    /**
     * Testing construction with flexible path
     */
    public function testConstructor2()
    {
        // setup and test body
        $Template = new \Mezon\HtmlTemplate\HtmlTemplate(__DIR__ . '/test-data/res/', 'index2', [
            'main'
        ]);

        $Content = $Template->compile();

        // assertions
        $this->assertStringContainsString('<body>', $Content, 'Layout was not setup');
        $this->assertStringContainsString('<section>', $Content, 'Block was not setup');
    }

    /**
     * Testing invalid construction
     */
    public function testInvalidConstructor()
    {
        $this->expectException(Exception::class);

        // setup and test body
        new \Mezon\HtmlTemplate\HtmlTemplate(__DIR__, 'index2', [
            'main'
        ]);
    }

    /**
     * Testing that all unused place holders will be removed
     */
    public function testCompile()
    {
        // setup
        $Template = new \Mezon\HtmlTemplate\HtmlTemplate(__DIR__ . '/test-data/res/', 'index2', [
            'main'
        ]);
        $_SERVER['HTTP_HOST'] = 'host';

        // test body
        $Result = $Template->compile();

        // assertions
        $this->assertStringNotContainsStringIgnoringCase('{title}', $Result);
    }

    /**
     * Testing unexisting block
     */
    public function testGetUnexistingBlock()
    {
        // setup and test body
        $Template = new \Mezon\HtmlTemplate\HtmlTemplate(__DIR__ . '/test-data/', 'index', [
            'main'
        ]);

        $this->expectException(Exception::class);

        // test body
        $Template->getBlock('unexisting');
    }
}
