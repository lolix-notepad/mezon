<?php
require_once (__DIR__ . '/../../conf/conf.php');
require_once (__DIR__ . '/../router.php');

/**
 * Mockup router class.
 */
class MockRouter extends \Mezon\Router
{

    public $ErrorVar = 0;

    /**
     * Mock error handler.
     */
    public function set_error_var()
    {
        $this->ErrorVar = 7;
    }
}

class RouterTest extends PHPUnit\Framework\TestCase
{

    /**
     * Function simply returns string.
     */
    public function hello_world_output()
    {
        return ('Hello world!');
    }

    /**
     * Method for checking id list.
     */
    public function il_test($Route, $Params)
    {
        return ($Params['ids']);
    }

    /**
     * Function simply returns string.
     */
    static public function static_hello_world_output()
    {
        return ('Hello static world!');
    }

    /**
     * Testing action #1.
     */
    public function action_a1()
    {
        return ('action #1');
    }

    /**
     * Testing action #2.
     */
    public function action_a2()
    {
        return ('action #2');
    }

    /**
     * Testing one component router.
     */
    public function testOneComponentRouterClassMethod()
    {
        $Router = new \Mezon\Router();

        $Router->add_route('/index/', array(
            $this,
            'hello_world_output'
        ));

        $Content = $Router->call_route('/index/');

        $this->assertEquals('Hello world!', $Content, 'Invalid index route');
    }

    /**
     * Testing one component router.
     */
    public function testOneComponentRouterLambda()
    {
        $Router = new \Mezon\Router();

        $Router->add_route('/index/', function () {
            return ('Hello world!');
        });

        $Content = $Router->call_route('/index/');

        $this->assertEquals('Hello world!', $Content, 'Invalid index route');
    }

    /**
     * Testing one component router.
     */
    public function testOneComponentRouterStatic()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('/index/', 'RouterTest::static_hello_world_output');

        $Content = $Router->call_route('/index/');

        $this->assertEquals('Hello static world!', $Content, 'Invalid index route');
    }

    /**
     * Testing unexisting route behaviour.
     */
    public function testUnexistingRoute()
    {
        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->add_route('/index/', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/unexisting-route/');
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
        $Router->fetch_actions($this);

        $Content = $Router->call_route('/a1/');
        $this->assertEquals('action #1', $Content, 'Invalid a1 route');

        $Content = $Router->call_route('/a2/');
        $this->assertEquals('action #2', $Content, 'Invalid a2 route');
    }

    /**
     * Method tests POST actions
     */
    public function testPostClassAction()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        // set_global('server', 'REQUEST_METHOD', 'POST');

        $Router = new \Mezon\Router();
        $Router->fetch_actions($this);
        $Content = $Router->call_route('/a1/');
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
        $Router->add_route('*', array(
            $this,
            'hello_world_output'
        ));

        $Content = $Router->call_route('/some-route/');

        $this->assertEquals('Hello world!', $Content, 'Invalid index route');
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorOverlapUnexisting()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('*', array(
            $this,
            'hello_world_output'
        ));
        $Router->add_route('/index/', 'RouterTest::static_hello_world_output');

        $Content = $Router->call_route('/some-route/');

        $this->assertEquals('Hello world!', $Content, 'Invalid index route');
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorOverlapExisting()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('*', array(
            $this,
            'hello_world_output'
        ));
        $Router->add_route('/index/', 'RouterTest::static_hello_world_output');

        $Content = $Router->call_route('/index/');

        $this->assertEquals('Hello world!', $Content, 'Invalid index route');
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorExisting()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('/index/', 'RouterTest::static_hello_world_output');
        $Router->add_route('*', array(
            $this,
            'hello_world_output'
        ));

        $Content = $Router->call_route('/index/');

        $this->assertEquals('Hello static world!', $Content, 'Invalid index route');
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorUnexisting()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('/index/', 'RouterTest::static_hello_world_output');
        $Router->add_route('*', array(
            $this,
            'hello_world_output'
        ));

        $Content = $Router->call_route('/some-route/');

        $this->assertEquals('Hello world!', $Content, 'Invalid index route');
    }

    /**
     * Testing invalid data types behaviour.
     */
    public function testInvalidType()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[unexisting-type:i]/item/', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/catalog/1024/item/');
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
        $Router->add_route('/catalog/[i:cat_id]/item/[unexisting-type-trace:item_id]/', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/catalog/1024/item/2048/');
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
        $Router->add_route('/catalog/[i:cat_id]/item/[i:item_id]/', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/catalog/1024/item/2048/');
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
        $Router->add_route('/catalog/[i:cat_id]/', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/catalog/1024/');
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
        $Router->add_route('/catalog/[a:cat_id]/', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/catalog/foo/');
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
        $Router->add_route('/catalog/[i:cat_id]/', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/catalog/a1024/');
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
        $Router->add_route('/catalog/[a:cat_id]/', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/catalog/~foo/');
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
        $Router->add_route('/catalog/[a:cat_id]/', function ($Route, $Parameters) {
            return ($Parameters['cat_id']);
        });

        $Result = $Router->call_route('/catalog/foo/');

        $this->assertEquals($Result, 'foo', 'Invalid extracted parameter');
    }

    /**
     * Testing parameter extractor.
     */
    public function testValidExtractedParameters()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[a:cat_id]/[i:item_id]', function ($Route, $Parameters) {
            return ($Parameters['cat_id'] . $Parameters['item_id']);
        });

        $Result = $Router->call_route('/catalog/foo/1024/');

        $this->assertEquals($Result, 'foo1024', 'Invalid extracted parameter');
    }

    /**
     * Testing parameter extractor.
     */
    public function testValidRouteParameter()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/', function ($Route, $Parameters) {
            return ($Route);
        });
        $Router->add_route('/catalog/[i:cat_id]', function ($Route, $Parameters) {
            return ($Route);
        });

        $Result = $Router->call_route('/catalog/');

        $this->assertEquals($Result, '/catalog/', 'Invalid extracted route');

        $Result = $Router->call_route('/catalog/1024/');

        $this->assertEquals($Result, '/catalog/1024/', 'Invalid extracted route');
    }

    /**
     * Testing static routes for POST requests.
     */
    public function testPostRequestForExistingStaticRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/', function ($Route, $Parameters) {
            return ($Route);
        }, 'POST');

        $Result = $Router->call_route('/catalog/');

        $this->assertEquals($Result, '/catalog/', 'Invalid extracted route');
    }

    /**
     * Testing dynamic routes for POST requests.
     */
    public function testPostRequestForExistingDynamicRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[i:cat_id]', function ($Route, $Parameters) {
            return ($Route);
        }, 'POST');

        $Result = $Router->call_route('/catalog/1024/');

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
        $Router->add_route('/catalog/', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/catalog/');
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
        $Router->add_route('/catalog/[i:cat_id]', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/catalog/1024/');
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
        $Router->add_route('/catalog/', function ($Route, $Parameters) {
            return ($Route);
        }, 'PUT');

        $Result = $Router->call_route('/catalog/');

        $this->assertEquals($Result, '/catalog/', 'Invalid extracted route');
    }

    /**
     * Testing dynamic routes for PUT requests.
     */
    public function testPutRequestForExistingDynamicRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[i:cat_id]', function ($Route, $Parameters) {
            return ($Route);
        }, 'PUT');

        $Result = $Router->call_route('/catalog/1024/');

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
        $Router->add_route('/catalog/', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/catalog/');
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
        $Router->add_route('/catalog/[i:cat_id]', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/catalog/1024/');
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
        $Router->add_route('/catalog/', function ($Route, $Parameters) {
            return ($Route);
        }, 'DELETE');

        $Result = $Router->call_route('/catalog/');

        $this->assertEquals($Result, '/catalog/', 'Invalid extracted route');
    }

    /**
     * Testing dynamic routes for DELETE requests.
     */
    public function testDeleteRequestForExistingDynamicRoute()
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[i:cat_id]', function ($Route, $Parameters) {
            return ($Route);
        }, 'DELETE');

        $Result = $Router->call_route('/catalog/1024/');

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
        $Router->add_route('/catalog/', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/catalog/');
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
        $Router->add_route('/catalog/[i:cat_id]', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/catalog/1024/');
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
        $Router->add_route('/catalog/', function ($Route, $Parameters) {
            return ('POST');
        }, 'POST');
        $Router->add_route('/catalog/', function ($Route, $Parameters) {
            return ('GET');
        }, 'GET');
        $Router->add_route('/catalog/', function ($Route, $Parameters) {
            return ('PUT');
        }, 'PUT');
        $Router->add_route('/catalog/', function ($Route, $Parameters) {
            return ('DELETE');
        }, 'DELETE');

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $Result = $Router->call_route('/catalog/');
        $this->assertEquals($Result, 'POST', 'Invalid selected route');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Result = $Router->call_route('/catalog/');
        $this->assertEquals($Result, 'GET', 'Invalid selected route');

        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $Result = $Router->call_route('/catalog/');
        $this->assertEquals($Result, 'PUT', 'Invalid selected route');

        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $Result = $Router->call_route('/catalog/');
        $this->assertEquals($Result, 'DELETE', 'Invalid selected route');
    }

    /**
     * Testing 'clear' method.
     */
    public function testClearMethod()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/', function ($Route, $Parameters) {
            return ('POST');
        }, 'POST');
        $Router->add_route('/catalog/', function ($Route, $Parameters) {
            return ('GET');
        }, 'GET');
        $Router->add_route('/catalog/', function ($Route, $Parameters) {
            return ('PUT');
        }, 'PUT');
        $Router->add_route('/catalog/', function ($Route, $Parameters) {
            return ('DELETE');
        }, 'DELETE');
        $Router->clear();

        try {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $Router->call_route('/catalog/');
            $Flag = 'not cleared';
        } catch (Exception $e) {
            $Flag = 'cleared';
        }
        $this->assertEquals($Flag, 'cleared', 'Data was not cleared');

        try {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $Router->call_route('/catalog/');
            $Flag = 'not cleared';
        } catch (Exception $e) {
            $Flag = 'cleared';
        }
        $this->assertEquals($Flag, 'cleared', 'Data was not cleared');

        try {
            $_SERVER['REQUEST_METHOD'] = 'PUT';
            $Router->call_route('/catalog/');
            $Flag = 'not cleared';
        } catch (Exception $e) {
            $Flag = 'cleared';
        }
        $this->assertEquals($Flag, 'cleared', 'Data was not cleared');

        try {
            $_SERVER['REQUEST_METHOD'] = 'DELETE';
            $Router->call_route('/catalog/');
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
        $Current = $Router->set_no_processor_found_error_handler(array(
            $Router,
            'set_error_var'
        ));

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $Router->call_route('/unexisting/');

        $Router->set_no_processor_found_error_handler($Current);

        $this->assertEquals($Router->ErrorVar, 7, 'Handler was not set');
    }

    /**
     * Testing command special chars.
     */
    public function testCommandSpecialChars()
    {
        $Router = new \Mezon\Router();

        $Router->add_route('/[a:url]/', function ($Route, $Parameters) {
            return ('GET');
        }, 'GET');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Result = $Router->call_route('/.-@/');
        $this->assertEquals($Result, 'GET', 'Invalid selected route');
    }

    /**
     * Testing strings.
     */
    public function testStringSpecialChars()
    {
        $Router = new \Mezon\Router();

        $Router->add_route('/[s:url]/', function ($Route, $Parameters) {
            return ('GET');
        }, 'GET');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Result = $Router->call_route('/, ;:/');
        $this->assertEquals($Result, 'GET', 'Invalid selected route');
    }

    /**
     * Testing invalid id list data types behaviour.
     */
    public function testInValidIdListParams()
    {
        $Exception = '';
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[il:cat_id]/', array(
            $this,
            'hello_world_output'
        ));

        try {
            $Router->call_route('/catalog/12345./');
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
        $Router->add_route('/catalog/[il:ids]/', array(
            $this,
            'il_test'
        ));

        $Result = $Router->call_route('/catalog/123,456,789/');

        $this->assertEquals($Result, '123,456,789', 'Invalid router response');
    }

    /**
     * Testing valid id list data types behaviour.
     */
    public function testStringParamSecurity()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[s:foo]/', function ($Route, $Parameters) {
            return ($Parameters['foo']);
        });

        $Result = $Router->call_route('/catalog/123&456/');

        $this->assertEquals($Result, '123&amp;456', 'Security data violation');
    }

    /**
     * Testing float value.
     */
    public function testFloatI()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[i:foo]/', function ($Route, $Parameters) {
            return ($Parameters['foo']);
        });

        $Result = $Router->call_route('/catalog/1.1/');

        $this->assertEquals($Result, '1.1', 'Float data violation');
    }

    /**
     * Testing negative float value.
     */
    public function testNegativeFloatI()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[i:foo]/', function ($Route, $Parameters) {
            return ($Parameters['foo']);
        });

        $Result = $Router->call_route('/catalog/-1.1/');

        $this->assertEquals($Result, '-1.1', 'Float data violation');
    }

    /**
     * Testing positive float value.
     */
    public function testPositiveFloatI()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[i:foo]/', function ($Route, $Parameters) {
            return ($Parameters['foo']);
        });

        $Result = $Router->call_route('/catalog/+1.1/');

        $this->assertEquals($Result, '+1.1', 'Float data violation');
    }

    /**
     * Testing negative integer value
     */
    public function test_negative_integer_i()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[i:foo]/', function ($Route, $Parameters) {
            return ($Parameters['foo']);
        });

        $Result = $Router->call_route('/catalog/-1/');

        $this->assertEquals('-1', $Result, 'Float data violation');
    }

    /**
     * Testing positive integer value
     */
    public function test_positive_integer_i()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[i:foo]/', function ($Route, $Parameters) {
            return ($Parameters['foo']);
        });

        $Result = $Router->call_route('/catalog/1/');

        $this->assertEquals('1', $Result, 'Float data violation');
    }

    /**
     * Testing array routes.
     */
    public function test_array_routes()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/item/', function ($Route, $Parameters) {
            return ($Route);
        }, 'GET');

        $Result = $Router->call_route([
            'catalog',
            'item'
        ]);

        $this->assertEquals($Result, '/catalog/item/', 'Invalid extracted route');
    }

    /**
     * Testing empty array routes.
     */
    public function test_empty_array_routes()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/catalog/item/';

        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/item/', function ($Route, $Parameters) {
            return ($Route);
        }, 'GET');

        $Result = $Router->call_route([
            0 => ''
        ]);

        $this->assertEquals($Result, '/catalog/item/', 'Invalid extracted route');
    }

    /**
     * Testing empty array routes.
     */
    public function test_index_route()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';

        $Router = new \Mezon\Router();
        $Router->add_route('/index/', function ($Route, $Parameters) {
            return ($Route);
        }, 'GET');

        $Result = $Router->call_route([
            0 => ''
        ]);

        $this->assertEquals($Result, '/index/', 'Invalid extracted route');
    }

    /**
     * Testing saving of the route parameters
     */
    public function test_saving_parameters()
    {
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[i:foo]/', function ($Route, $Parameters) {
            return ($Parameters['foo']);
        });

        $Router->call_route('/catalog/-1/');

        $this->assertEquals($Router->get_param('foo'), '-1', 'Float data violation');
    }

    /**
     * Testing empty array routes
     */
    public function test_multiple_request_types()
    {
        // setup
        $_SERVER['REQUEST_URI'] = '/';

        $Router = new \Mezon\Router();
        $Router->add_route('/index/', function ($Route, $Parameters) {
            return ($Route);
        }, [
            'GET',
            'POST'
        ]);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Result = $Router->call_route([
            0 => ''
        ]);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $Result = $Router->call_route([
            0 => ''
        ]);

        $this->assertEquals($Result, '/index/', 'Invalid extracted route');
    }

    /**
     * Testing get_param for unexisting param
     */
    public function test_getting_unexisting_parameter()
    {
        // setup
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[i:foo]/', function () {});

        $Router->call_route('/catalog/1/');

        // test body and assertions
        try{
            $Router->get_param('unexisting');
            $this->fail('Exception was not thrown');
        }
        catch(Exception $e){
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Testing get_param for existing param
     */
    public function test_getting_existing_parameter()
    {
        // setup
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[i:foo]/', function () {});

        $Router->call_route('/catalog/1/');

        // test body and assertions
        try{
            $Router->get_param('foo');
            $this->addToAssertionCount(1);
        }
        catch(Exception $e){
            $this->fail('Exception was thrown for existing parameter');
        }
    }

    /**
     * Testing has_param method
     */
    public function test_validating_parameter()
    {
        // setup
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $Router = new \Mezon\Router();
        $Router->add_route('/catalog/[i:foo]/', function () {});

        $Router->call_route('/catalog/1/');

        // test body and assertions
        $this->assertTrue($Router->has_param('foo'));
        $this->assertFalse($Router->has_param('unexisting'));
    }
}

?>