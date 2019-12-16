<?php
require_once (__DIR__ . '/../view.php');

/**
 * View class for testing purposes
 *
 * @author Dodonov A.A.
 */
class TestingView extends View
{

    public function view_test(): string
    {
        return ('rendered content');
    }
    
    public function view_test2(): string
    {
        return ('rendered content 2');
    }
}

class ViewTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function test_constructor()
    {
        $View = new TestingView('test');

        $this->assertEquals('test', $View->ViewName, 'Invalid constructor call');
    }

    /**
     * Testing render
     */
    public function test_render()
    {
        $View = new TestingView('test');

        $this->assertEquals('rendered content', $View->render(), 'Invalid view renderring');
        $this->assertEquals('rendered content 2', $View->render('test2'), 'Invalid view renderring');
    }
}

?>