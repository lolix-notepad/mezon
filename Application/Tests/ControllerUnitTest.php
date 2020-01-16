<?php
require_once ('autoload.php');

/**
 * Controller class for testing purposes
 *
 * @author Dodonov A.A.
 */
class TestingController extends \Mezon\Application\Controller
{

    public function controllerTest()
    {
        return ('computed content');
    }

    public function controllerTest2()
    {
        return ('computed content 2');
    }
}

class ControllerUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructor()
    {
        $Controller = new TestingController('Test');

        $this->assertEquals('Test', $Controller->getControllerName(), 'Invalid constructor call');
    }

    /**
     * Testing render
     */
    public function testRender()
    {
        $Controller = new TestingController('Test');

        $this->assertEquals('computed content', $Controller->run(), 'Invalid controller execution');
        $this->assertEquals('computed content 2', $Controller->run('test2'), 'Invalid controller execution');
    }
}
