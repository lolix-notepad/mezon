<?php
require_once (__DIR__ . '/../../../autoloader.php');

/**
 * Mockup router class.
 */
class MockRouter extends \Mezon\Router
{

    public $ErrorVar = 0;

    /**
     * Mock error handler.
     */
    public function setErrorVar()
    {
        $this->ErrorVar = 7;
    }
}

class RouterUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Function simply returns string.
     */
    public function helloWorldOutput()
    {
        return ('Hello world!');
    }

    /**
     * Method for checking id list.
     */
    public function ilTest($Route, $Params)
    {
        return ($Params['ids']);
    }

    /**
     * Function simply returns string.
     */
    static public function staticHelloWorldOutput()
    {
        return ('Hello static world!');
    }

    /**
     * Testing action #1.
     */
    public function actionA1()
    {
        return ('action #1');
    }

    /**
     * Testing action #2.
     */
    public function actionA2()
    {
        return ('action #2');
    }

    public function actionDoubleWord()
    {
        return ('action double word');
    }

    /**
     * Testing one component router.
     */
    public function testOneComponentRouterClassMethod()
    {
        $Router = new \Mezon\Router();

        $Router->addRoute('/index/', [
            $this,
            'helloWorldOutput'
        ]);

        $Content = $Router->callRoute('/index/');

        $this->assertEquals('Hello world!', $Content, 'Invalid index route');
    }

    /**
     * Testing one component router.
     */
    public function testOneComponentRouterLambda()
    {
        $Router = new \Mezon\Router();

        $Router->addRoute('/index/', function () {
            return ('Hello world!');
        });

        $Content = $Router->callRoute('/index/');

        $this->assertEquals('Hello world!', $Content, 'Invalid index route');
    }

    /**
     * Testing one component router.
     */
    public function testOneComponentRouterStatic()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/index/', 'RouterUnitTest::staticHelloWorldOutput');

        $Content = $Router->callRoute('/index/');

        $this->assertEquals('Hello static world!', $Content, 'Invalid index route');
    }

    /**
     * Testing unexisting route behaviour.
     */
    public function testUnexistingRoute()
    {
        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->addRoute('/index/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $Router->callRoute('/unexisting-route/');
        } catch (Exception $e) {
            $Exception = $e->getMessage();
        }

        $Msg = "The processor was not found for the route";

        $this->assertNotFalse(strpos($Exception, $Msg), 'Valid error handling expected');
    }

    /**
     * Testing action fetching method.
     */
    public function testClassActions()
    {
        $Router = new \Mezon\Router();
        $Router->fetchActions($this);

        $Content = $Router->callRoute('/a1/');
        $this->assertEquals('action #1', $Content, 'Invalid a1 route');

        $Content = $Router->callRoute('/a2/');
        $this->assertEquals('action #2', $Content, 'Invalid a2 route');

        $Content = $Router->callRoute('/double-word/');
        $this->assertEquals('action double word', $Content, 'Invalid a2 route');
    }

    /**
     * Method tests POST actions
     */
    public function testPostClassAction()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        // set_global('server', 'REQUEST_METHOD', 'POST');

        $Router = new \Mezon\Router();
        $Router->fetchActions($this);
        $Content = $Router->callRoute('/a1/');
        $this->assertEquals('action #1', $Content, 'Invalid a1 route');

        // set_global('server', 'REQUEST_METHOD', 'GET');
    }

    /**
     * Testing one processor for all routes.
     */
    public function testSingleAllProcessor()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $Router = new \Mezon\Router();
        $Router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);

        $Content = $Router->callRoute('/some-route/');

        $this->assertEquals('Hello world!', $Content, 'Invalid index route');
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorOverlapUnexisting()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);
        $Router->addRoute('/index/', 'RouterUnitTest::staticHelloWorldOutput');

        $Content = $Router->callRoute('/some-route/');

        $this->assertEquals('Hello world!', $Content, 'Invalid index route');
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorOverlapExisting()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);
        $Router->addRoute('/index/', 'RouterUnitTest::staticHelloWorldOutput');

        $Content = $Router->callRoute('/index/');

        $this->assertEquals('Hello world!', $Content, 'Invalid index route');
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorExisting()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/index/', 'RouterUnitTest::staticHelloWorldOutput');
        $Router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);

        $Content = $Router->callRoute('/index/');

        $this->assertEquals('Hello static world!', $Content, 'Invalid index route');
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorUnexisting()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/index/', 'RouterUnitTest::staticHelloWorldOutput');
        $Router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);

        $Content = $Router->callRoute('/some-route/');

        $this->assertEquals('Hello world!', $Content, 'Invalid index route');
    }

    /**
     * Testing invalid data types behaviour.
     */
    public function testInvalidType()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[unexisting-type:i]/item/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $Router->callRoute('/catalog/1024/item/');
            $this->assertFalse(true, 'Exception expected');
        } catch (Exception $e) {
            $this->assertFalse(false, '');
        }
    }

    /**
     * Testing invalid data types behaviour.
     */
    public function testValidInvalidTypes()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute(
            '/catalog/[i:cat_id]/item/[unexisting-type-trace:item_id]/',
            [
                $this,
                'helloWorldOutput'
            ]);

        try {
            $Router->callRoute('/catalog/1024/item/2048/');
            $this->assertFalse(true, 'Exception expected');
        } catch (Exception $e) {
            $this->assertFalse(false, '');
        }
    }

    /**
     * Testing valid data types behaviour.
     */
    public function testValidTypes()
    {
        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:cat_id]/item/[i:item_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $Router->callRoute('/catalog/1024/item/2048/');
        } catch (Exception $e) {
            $Exception = $e->getMessage();
        }

        $Msg = "Illegal parameter type";

        $this->assertFalse(strpos($Exception, $Msg), 'Valid type expected');
    }

    /**
     * Testing valid integer data types behaviour.
     */
    public function testValidIntegerParams()
    {
        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:cat_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $Router->callRoute('/catalog/1024/');
        } catch (Exception $e) {
            $Exception = $e->getMessage();
        }

        $Msg = "Illegal parameter type";

        $this->assertFalse(strpos($Exception, $Msg), 'Valid type expected');
    }

    /**
     * Testing valid alnum data types behaviour.
     */
    public function testValidAlnumParams()
    {
        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[a:cat_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $Router->callRoute('/catalog/foo/');
        } catch (Exception $e) {
            $Exception = $e->getMessage();
        }

        $Msg = "Illegal parameter type";

        $this->assertFalse(strpos($Exception, $Msg), 'Valid type expected');
    }

    /**
     * Testing invalid integer data types behaviour.
     */
    public function testInValidIntegerParams()
    {
        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:cat_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $Router->callRoute('/catalog/a1024/');
        } catch (Exception $e) {
            $Exception = $e->getMessage();
        }

        $Msg = "The processor was not found for the route /catalog/a1024/";

        $this->assertNotFalse(strpos($Exception, $Msg), 'Invalid error response');
    }

    /**
     * Testing invalid alnum data types behaviour.
     */
    public function testInValidAlnumParams()
    {
        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[a:cat_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $Router->callRoute('/catalog/~foo/');
        } catch (Exception $e) {
            $Exception = $e->getMessage();
        }

        $Msg = "The processor was not found for the route /catalog/~foo/";

        $this->assertNotFalse(strpos($Exception, $Msg), 'Invalid error response');
    }

    /**
     * Testing parameter extractor.
     */
    public function testValidExtractedParameter()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[a:cat_id]/', function ($Route, $Parameters) {
            return ($Parameters['cat_id']);
        });

        $Result = $Router->callRoute('/catalog/foo/');

        $this->assertEquals($Result, 'foo', 'Invalid extracted parameter');
    }

    /**
     * Testing parameter extractor.
     */
    public function testValidExtractedParameters()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute(
            '/catalog/[a:cat_id]/[i:item_id]',
            function ($Route, $Parameters) {
                return ($Parameters['cat_id'] . $Parameters['item_id']);
            });

        $Result = $Router->callRoute('/catalog/foo/1024/');

        $this->assertEquals($Result, 'foo1024', 'Invalid extracted parameter');
    }

    /**
     * Testing parameter extractor.
     */
    public function testValidRouteParameter()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/', function ($Route, $Parameters) {
            return ($Route);
        });
        $Router->addRoute('/catalog/[i:cat_id]', function ($Route, $Parameters) {
            return ($Route);
        });

        $Result = $Router->callRoute('/catalog/');

        $this->assertEquals($Result, '/catalog/', 'Invalid extracted route');

        $Result = $Router->callRoute('/catalog/1024/');

        $this->assertEquals($Result, '/catalog/1024/', 'Invalid extracted route');
    }

    /**
     * Testing static routes for POST requests.
     */
    public function testPostRequestForExistingStaticRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/', function ($Route, $Parameters) {
            return ($Route);
        }, 'POST');

        $Result = $Router->callRoute('/catalog/');

        $this->assertEquals($Result, '/catalog/', 'Invalid extracted route');
    }

    /**
     * Testing dynamic routes for POST requests.
     */
    public function testPostRequestForExistingDynamicRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:cat_id]', function ($Route, $Parameters) {
            return ($Route);
        }, 'POST');

        $Result = $Router->callRoute('/catalog/1024/');

        $this->assertEquals($Result, '/catalog/1024/', 'Invalid extracted route');
    }

    /**
     * Testing static routes for POST requests.
     */
    public function testPostRequestForUnExistingStaticRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $Router->callRoute('/catalog/');
        } catch (Exception $e) {
            $Exception = $e->getMessage();
        }

        $Msg = "The processor was not found for the route /catalog/";

        $this->assertNotFalse(strpos($Exception, $Msg), 'Invalid error response');
    }

    /**
     * Testing dynamic routes for POST requests.
     */
    public function testPostRequestForUnExistingDynamicRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:cat_id]', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $Router->callRoute('/catalog/1024/');
        } catch (Exception $e) {
            $Exception = $e->getMessage();
        }

        $Msg = "The processor was not found for the route /catalog/1024/";

        $this->assertNotFalse(strpos($Exception, $Msg), 'Invalid error response');
    }

    /**
     * Testing static routes for PUT requests.
     */
    public function testPutRequestForExistingStaticRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/', function ($Route, $Parameters) {
            return ($Route);
        }, 'PUT');

        $Result = $Router->callRoute('/catalog/');

        $this->assertEquals($Result, '/catalog/', 'Invalid extracted route');
    }

    /**
     * Testing dynamic routes for PUT requests.
     */
    public function testPutRequestForExistingDynamicRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:cat_id]', function ($Route, $Parameters) {
            return ($Route);
        }, 'PUT');

        $Result = $Router->callRoute('/catalog/1024/');

        $this->assertEquals($Result, '/catalog/1024/', 'Invalid extracted route');
    }

    /**
     * Testing static routes for PUT requests.
     */
    public function testPutRequestForUnExistingStaticRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $Router->callRoute('/catalog/');
        } catch (Exception $e) {
            $Exception = $e->getMessage();
        }

        $Msg = "The processor was not found for the route /catalog/";

        $this->assertNotFalse(strpos($Exception, $Msg), 'Invalid error response');
    }

    /**
     * Testing dynamic routes for PUT requests.
     */
    public function testPutRequestForUnExistingDynamicRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:cat_id]', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $Router->callRoute('/catalog/1024/');
        } catch (Exception $e) {
            $Exception = $e->getMessage();
        }

        $Msg = "The processor was not found for the route /catalog/1024/";

        $this->assertNotFalse(strpos($Exception, $Msg), 'Invalid error response');
    }

    /**
     * Testing static routes for DELETE requests.
     */
    public function testDeleteRequestForExistingStaticRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/', function ($Route, $Parameters) {
            return ($Route);
        }, 'DELETE');

        $Result = $Router->callRoute('/catalog/');

        $this->assertEquals($Result, '/catalog/', 'Invalid extracted route');
    }

    /**
     * Testing dynamic routes for DELETE requests.
     */
    public function testDeleteRequestForExistingDynamicRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:cat_id]', function ($Route, $Parameters) {
            return ($Route);
        }, 'DELETE');

        $Result = $Router->callRoute('/catalog/1024/');

        $this->assertEquals($Result, '/catalog/1024/', 'Invalid extracted route');
    }

    /**
     * Testing static routes for DELETE requests.
     */
    public function testDeleteRequestForUnExistingStaticRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $Router->callRoute('/catalog/');
        } catch (Exception $e) {
            $Exception = $e->getMessage();
        }

        $Msg = "The processor was not found for the route /catalog/";

        $this->assertNotFalse(strpos($Exception, $Msg), 'Invalid error response');
    }

    /**
     * Testing dynamic routes for DELETE requests.
     */
    public function testDeleteRequestForUnExistingDynamicRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:cat_id]', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $Router->callRoute('/catalog/1024/');
        } catch (Exception $e) {
            $Exception = $e->getMessage();
        }

        $Msg = "The processor was not found for the route /catalog/1024/";

        $this->assertNotFalse(strpos($Exception, $Msg), 'Invalid error response');
    }

    /**
     * Testing case when both GET and POST processors exists.
     */
    public function testGetPostPostDeleteRouteConcurrency()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/', function ($Route, $Parameters) {
            return ('POST');
        }, 'POST');
        $Router->addRoute('/catalog/', function ($Route, $Parameters) {
            return ('GET');
        }, 'GET');
        $Router->addRoute('/catalog/', function ($Route, $Parameters) {
            return ('PUT');
        }, 'PUT');
        $Router->addRoute('/catalog/', function ($Route, $Parameters) {
            return ('DELETE');
        }, 'DELETE');

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $Result = $Router->callRoute('/catalog/');
        $this->assertEquals($Result, 'POST', 'Invalid selected route');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Result = $Router->callRoute('/catalog/');
        $this->assertEquals($Result, 'GET', 'Invalid selected route');

        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $Result = $Router->callRoute('/catalog/');
        $this->assertEquals($Result, 'PUT', 'Invalid selected route');

        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $Result = $Router->callRoute('/catalog/');
        $this->assertEquals($Result, 'DELETE', 'Invalid selected route');
    }

    /**
     * Testing 'clear' method.
     */
    public function testClearMethod()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/', function ($Route, $Parameters) {
            return ('POST');
        }, 'POST');
        $Router->addRoute('/catalog/', function ($Route, $Parameters) {
            return ('GET');
        }, 'GET');
        $Router->addRoute('/catalog/', function ($Route, $Parameters) {
            return ('PUT');
        }, 'PUT');
        $Router->addRoute('/catalog/', function ($Route, $Parameters) {
            return ('DELETE');
        }, 'DELETE');
        $Router->clear();

        try {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $Router->callRoute('/catalog/');
            $Flag = 'not cleared';
        } catch (Exception $e) {
            $Flag = 'cleared';
        }
        $this->assertEquals($Flag, 'cleared', 'Data was not cleared');

        try {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $Router->callRoute('/catalog/');
            $Flag = 'not cleared';
        } catch (Exception $e) {
            $Flag = 'cleared';
        }
        $this->assertEquals($Flag, 'cleared', 'Data was not cleared');

        try {
            $_SERVER['REQUEST_METHOD'] = 'PUT';
            $Router->callRoute('/catalog/');
            $Flag = 'not cleared';
        } catch (Exception $e) {
            $Flag = 'cleared';
        }
        $this->assertEquals($Flag, 'cleared', 'Data was not cleared');

        try {
            $_SERVER['REQUEST_METHOD'] = 'DELETE';
            $Router->callRoute('/catalog/');
            $Flag = 'not cleared';
        } catch (Exception $e) {
            $Flag = 'cleared';
        }
        $this->assertEquals($Flag, 'cleared', 'Data was not cleared');
    }

    /**
     * Test validate custom error handlers.
     */
    public function testSetErrorHandler()
    {
        $Router = new MockRouter();
        $Current = $Router->setNoProcessorFoundErrorHandler([
            $Router,
            'setErrorVar'
        ]);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $Router->callRoute('/unexisting/');

        $Router->setNoProcessorFoundErrorHandler($Current);

        $this->assertEquals($Router->ErrorVar, 7, 'Handler was not set');
    }

    /**
     * Testing command special chars.
     */
    public function testCommandSpecialChars()
    {
        $Router = new \Mezon\Router();

        $Router->addRoute('/[a:url]/', function ($Route, $Parameters) {
            return ('GET');
        }, 'GET');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Result = $Router->callRoute('/.-@/');
        $this->assertEquals($Result, 'GET', 'Invalid selected route');
    }

    /**
     * Testing strings.
     */
    public function testStringSpecialChars()
    {
        $Router = new \Mezon\Router();

        $Router->addRoute('/[s:url]/', function ($Route, $Parameters) {
            return ('GET');
        }, 'GET');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Result = $Router->callRoute('/, ;:/');
        $this->assertEquals($Result, 'GET', 'Invalid selected route');
    }

    /**
     * Testing invalid id list data types behaviour.
     */
    public function testInValidIdListParams()
    {
        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[il:cat_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $Router->callRoute('/catalog/12345./');
        } catch (Exception $e) {
            $Exception = $e->getMessage();
        }

        $Msg = "The processor was not found for the route /catalog/12345./";

        $this->assertNotFalse(strpos($Exception, $Msg), 'Invalid error response');
    }

    /**
     * Testing valid id list data types behaviour.
     */
    public function testValidIdListParams()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[il:ids]/', [
            $this,
            'ilTest'
        ]);

        $Result = $Router->callRoute('/catalog/123,456,789/');

        $this->assertEquals($Result, '123,456,789', 'Invalid router response');
    }

    /**
     * Testing valid id list data types behaviour.
     */
    public function testStringParamSecurity()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[s:foo]/', function ($Route, $Parameters) {
            return ($Parameters['foo']);
        });

        $Result = $Router->callRoute('/catalog/123&456/');

        $this->assertEquals($Result, '123&amp;456', 'Security data violation');
    }

    /**
     * Testing float value.
     */
    public function testFloatI()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:foo]/', function ($Route, $Parameters) {
            return ($Parameters['foo']);
        });

        $Result = $Router->callRoute('/catalog/1.1/');

        $this->assertEquals($Result, '1.1', 'Float data violation');
    }

    /**
     * Testing negative float value.
     */
    public function testNegativeFloatI()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:foo]/', function ($Route, $Parameters) {
            return ($Parameters['foo']);
        });

        $Result = $Router->callRoute('/catalog/-1.1/');

        $this->assertEquals($Result, '-1.1', 'Float data violation');
    }

    /**
     * Testing positive float value.
     */
    public function testPositiveFloatI()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:foo]/', function ($Route, $Parameters) {
            return ($Parameters['foo']);
        });

        $Result = $Router->callRoute('/catalog/+1.1/');

        $this->assertEquals($Result, '+1.1', 'Float data violation');
    }

    /**
     * Testing negative integer value
     */
    public function testNegativeIntegerI()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:foo]/', function ($Route, $Parameters) {
            return ($Parameters['foo']);
        });

        $Result = $Router->callRoute('/catalog/-1/');

        $this->assertEquals('-1', $Result, 'Float data violation');
    }

    /**
     * Testing positive integer value
     */
    public function testPositiveIntegerI()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:foo]/', function ($Route, $Parameters) {
            return ($Parameters['foo']);
        });

        $Result = $Router->callRoute('/catalog/1/');

        $this->assertEquals('1', $Result, 'Float data violation');
    }

    /**
     * Testing array routes.
     */
    public function testArrayRoutes()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/item/', function ($Route, $Parameters) {
            return ($Route);
        }, 'GET');

        $Result = $Router->callRoute([
            'catalog',
            'item'
        ]);

        $this->assertEquals($Result, '/catalog/item/', 'Invalid extracted route');
    }

    /**
     * Testing empty array routes.
     */
    public function testEmptyArrayRoutes()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/catalog/item/';

        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/item/', function ($Route, $Parameters) {
            return ($Route);
        }, 'GET');

        $Result = $Router->callRoute([
            0 => ''
        ]);

        $this->assertEquals($Result, '/catalog/item/', 'Invalid extracted route');
    }

    /**
     * Testing empty array routes.
     */
    public function testIndexRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';

        $Router = new \Mezon\Router();
        $Router->addRoute('/index/', function ($Route, $Parameters) {
            return ($Route);
        }, 'GET');

        $Result = $Router->callRoute([
            0 => ''
        ]);

        $this->assertEquals($Result, '/index/', 'Invalid extracted route');
    }

    /**
     * Testing saving of the route parameters
     */
    public function testSavingParameters()
    {
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:foo]/', function ($Route, $Parameters) {
            return ($Parameters['foo']);
        });

        $Router->callRoute('/catalog/-1/');

        $this->assertEquals($Router->getParam('foo'), '-1', 'Float data violation');
    }

    /**
     * Testing empty array routes
     */
    public function testMultipleRequestTypes()
    {
        // setup
        $_SERVER['REQUEST_URI'] = '/';

        $Router = new \Mezon\Router();
        $Router->addRoute('/index/', function ($Route, $Parameters) {
            return ($Route);
        }, [
            'GET',
            'POST'
        ]);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Result = $Router->callRoute([
            0 => ''
        ]);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $Result = $Router->callRoute([
            0 => ''
        ]);

        $this->assertEquals($Result, '/index/', 'Invalid extracted route');
    }

    /**
     * Testing getParam for unexisting param
     */
    public function testGettingUnexistingParameter()
    {
        // setup
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:foo]/', function () {});

        $Router->callRoute('/catalog/1/');

        $this->expectException(Exception::class);

        // test body and assertions
        $Router->getParam('unexisting');
    }

    /**
     * Testing getParam for existing param
     */
    public function testGettingExistingParameter()
    {
        // setup
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:foo]/', function () {});

        $Router->callRoute('/catalog/1/');

        // test body
        $Foo = $Router->getParam('foo');

        // assertions
        $this->assertEquals(1,$Foo);
    }

    /**
     * Testing hasParam method
     */
    public function testValidatingParameter()
    {
        // setup
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Router = new \Mezon\Router();
        $Router->addRoute('/catalog/[i:foo]/', function () {});

        $Router->callRoute('/catalog/1/');

        // test body and assertions
        $this->assertTrue($Router->hasParam('foo'));
        $this->assertFalse($Router->hasParam('unexisting'));
    }
}
