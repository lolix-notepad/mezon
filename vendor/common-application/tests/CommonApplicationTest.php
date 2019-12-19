<?php
require_once (__DIR__ . '/../../conf/conf.php');
require_once (__DIR__ . '/../../html-template/html-template.php');
require_once (__DIR__ . '/../common-application.php');

/**
 * View class
 *
 * @author Dodonov A.A.
 */
class TestView extends \Mezon\View
{

    public function __construct(string $Content)
    {
        parent::__construct('default');

        $this->Content = $Content;
    }

    public function render(string $ViewName = ''): string
    {
        return ($this->Content);
    }
}

/**
 * Application for testing purposes.
 */
class TestCommonApplication extends \Mezon\CommonApplication
{

    /**
     * Constructor.
     */
    function __construct()
    {
        parent::__construct(new \Mezon\HTMLTemplate(__DIR__, 'index'));
    }

    function action_array_result()
    {
        return ([
            'title' => 'Array result',
            'main' => 'Route main'
        ]);
    }

    function action_view_result()
    {
        return ([
            'title' => 'View result',
            'main' => new TestView('Test view result')
        ]);
    }
}

class CommonApplicationTest extends PHPUnit\Framework\TestCase
{

    /**
     * Running with complex router result
     */
    public function test_complex_route_result()
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
    public function test_complex_view_renderring()
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
        $this->assertContains('View result', $Output, 'Template compilation failed (3)');
        $this->assertContains('Test view result', $Output, 'Template compilation failed (4)');
    }

    /**
     * Testing handle_exception method
     */
    public function test_handle_exception()
    {
        // setup
        $Application = new TestCommonApplication();
        $Output = '';
        try {
            throw (new Exception('', 0));
        } catch (Exception $e) {
            // test body
            ob_start();
            $Application->handle_exception($e);
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
    public function test_handle_rest_exception()
    {
        // setup
        $Application = new TestCommonApplication();
        $Output = '';
        try {
            throw (new \Mezon\Service\ServiceRESTTransport\RESTException('', 0, 200, ''));
        } catch (Exception $e) {
            // test body
            ob_start();
            $Application->handle_rest_exception($e);
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
     * Testing handle_exception method
     */
    public function test_handle_exception_with_host()
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
            $Application->handle_exception($e);
            $Output = ob_get_contents();
            ob_end_clean();
        }

        // assertions
        $Output = json_decode(str_replace('<pre>', '', $Output), true);
        $this->assertEquals('some hostsome uri', $Output['host']);
    }
}

?>