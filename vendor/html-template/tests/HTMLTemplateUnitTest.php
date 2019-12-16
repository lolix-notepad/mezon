<?php
require_once (__DIR__ . '/../html-template.php');

class HTMLTemplateUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing construction with default path
     */
    public function test_constructor_1()
    {
        // setup and test body
        $Template = new HTMLTemplate(__DIR__ . '/test-data/', 'index', [
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
    public function test_constructor_2()
    {
        // setup and test body
        $Template = new HTMLTemplate(__DIR__ . '/test-data/res/', 'index2', [
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
    public function test_invalid_constructor()
    {
        try {
            // setup and test body
            $Template = new HTMLTemplate(__DIR__, 'index2', [
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
    public function test_compile()
    {
        // setup
        $Template = new HTMLTemplate(__DIR__ . '/test-data/res/', 'index2', [
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
    public function test_get_unexisting_block()
    {
        // setup and test body
        $Template = new HTMLTemplate(__DIR__ . '/test-data/', 'index', [
            'main'
        ]);

        try {
            // test body
            $Template->get_block('unexisting');

            // assertions
            $this->fail('Exception wile block reading must be thrown but it was not');
        } catch (Exception $e) {
            // assertions
            $this->addToAssertionCount(1);
        }
    }
}

?>