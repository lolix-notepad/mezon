<?php
require_once (__DIR__ . '/../../../autoloader.php');

/**
 * View class for testing purposes
 *
 * @author Dodonov A.A.
 */
class TestingView extends \Mezon\View
{

    public function viewTest(): string
    {
        return ('rendered content');
    }

    public function viewTest2(): string
    {
        return ('rendered content 2');
    }
}

class ViewUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructor()
    {
        $View = new TestingView('test');

        $this->assertEquals('test', $View->getViewName(), 'Invalid constructor call');
    }

    /**
     * Testing render
     */
    public function testRender()
    {
        $View = new TestingView('test');

        $this->assertEquals('rendered content', $View->render(), 'Invalid view renderring');
        $this->assertEquals('rendered content 2', $View->render('test2'), 'Invalid view renderring');
    }
}
