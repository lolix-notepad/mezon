<?php
require_once ('autoload.php');

/**
 * View class
 *
 * @author Dodonov A.A.
 */
class TestView extends \Mezon\Application\View
{

    public function __construct(string $Content)
    {
        parent::__construct('default');

        $this->Content = $Content;
    }

    public function render(string $ViewName = ''): string
    {
        return $this->Content;
    }
}

/**
 * Application for testing purposes.
 */
class TestCommonApplication extends \Mezon\CommonApplication\CommonApplication
{

    /**
     * Constructor.
     */
    function __construct()
    {
        parent::__construct(new \Mezon\HtmlTemplate\HtmlTemplate(__DIR__, 'index'));
    }

    function actionArrayResult()
    {
        return [
            'title' => 'Array result',
            'main' => 'Route main'
        ];
    }

    function actionViewResult()
    {
        return [
            'title' => 'View result',
            'main' => new TestView('Test view result')
        ];
    }
}

class CommonApplicationUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Running with complex router result
     */
    public function testComplexRouteResult()
    {
        $Application = new TestCommonApplication();

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['r'] = '/array-result/';

        ob_start();
        $Application->run();
        $Output = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(strpos($Output, 'Array result') !== false, 'Template compilation failed (1)');
        $this->assertTrue(strpos($Output, 'Route main') !== false, 'Template compilation failed (2)');
    }

    /**
     * Compiling page with functional view
     */
    public function testComplexViewRenderring()
    {
        // setup
        $Application = new TestCommonApplication();

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['r'] = '/view-result/';

        // test body
        ob_start();
        $Application->run();
        $Output = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString('View result', $Output, 'Template compilation failed (3)');
        $this->assertStringContainsString('Test view result', $Output, 'Template compilation failed (4)');
    }

    /**
     * Testing handleException method
     */
    public function testHandleException()
    {
        // setup
        $Application = new TestCommonApplication();
        $Output = '';
        try {
            throw (new Exception('', 0));
        } catch (Exception $e) {
            // test body
            ob_start();
            $Application->handleException($e);
            $Output = ob_get_contents();
            ob_end_clean();
        }

        // assertions
        $Output = json_decode(str_replace('<pre>', '', $Output), true);
        $this->assertContains('message', $Output);
        $this->assertContains('code', $Output);
        $this->assertContains('call_stack', $Output);
        $this->assertContains('host', $Output);
        $this->assertEquals('undefined', $Output['host']);
    }

    /**
     * Testing handle_rest_exception method
     */
    public function testHandleRestException()
    {
        // setup
        $Application = new TestCommonApplication();
        $Output = '';
        try {
            throw (new \Mezon\Service\ServiceRestTransport\RestException('', 0, 200, ''));
        } catch (Exception $e) {
            // test body
            ob_start();
            $Application->handleRestException($e);
            $Output = ob_get_contents();
            ob_end_clean();
        }

        // assertions
        $Output = json_decode(str_replace('<pre>', '', $Output), true);
        $this->assertContains('message', $Output);
        $this->assertContains('code', $Output);
        $this->assertContains('call_stack', $Output);
        $this->assertContains('host', $Output);
        $this->assertContains('http_body', $Output);
    }

    /**
     * Testing handleException method
     */
    public function testHandleExceptionWithHost()
    {
        // setup
        $Application = new TestCommonApplication();
        $Output = '';
        $_SERVER['HTTP_HOST'] = 'some host';
        $_SERVER['REQUEST_URI'] = 'some uri';
        try {
            throw (new Exception('', 0));
        } catch (Exception $e) {
            // test body
            ob_start();
            $Application->handleException($e);
            $Output = ob_get_contents();
            ob_end_clean();
        }

        // assertions
        $Output = json_decode(str_replace('<pre>', '', $Output), true);
        $this->assertEquals('some hostsome uri', $Output['host']);
    }
}
