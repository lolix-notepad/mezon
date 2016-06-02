<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/router/router.php' );

    class RouterTest extends PHPUnit_Framework_TestCase
    {
        /**
        *   Function simply returns string.
        */
        public function hello_world_output()
        {
            return( 'Hello world!' );
        }

        /**
        *   Function simply returns string.
        */
        static public function static_hello_world_output()
        {
            return( 'Hello static world!' );
        }

        /**
        *   Testing action #1.
        */
        public function action_a1()
        {
            return( 'action #1' );
        }

        /**
        *   Testing action #2.
        */
        public function action_a2()
        {
            return( 'action #2' );
        }

        /**
        *   Testing one component router.
        */
        public function testOneComponentRouterClassMethod()
        {
            $Router = new Router();
            $Router->add_route( '/index/' , array( $this , 'hello_world_output' ) );

            $Content = $Router->call_route( '/index/' );

            $this->assertEquals( 'Hello world!' , $Content , 'Invalid index route' );
        }

        /**
        *   Testing one component router.
        */
        public function testOneComponentRouterLambda()
        {
            $Router = new Router();
            $Router->add_route( '/index/' , function(){return( 'Hello world!' );} );

            $Content = $Router->call_route( '/index/' );

            $this->assertEquals( 'Hello world!' , $Content , 'Invalid index route' );
        }

        /**
        *   Testing one component router.
        */
        public function testOneComponentRouterStatic()
        {
            $Router = new Router();
            $Router->add_route( '/index/' , 'RouterTest::static_hello_world_output' );

            $Content = $Router->call_route( '/index/' );

            $this->assertEquals( 'Hello static world!' , $Content , 'Invalid index route' );
        }

        /**
        *   Testing unexisting route behaviour.
        */
        public function testUnexistingRoute()
        {
            $Exception = '';
            $Router = new Router();
            $Router->add_route( '/index/' , array( $this , 'hello_world_output' ) );

            try
            {
                $Router->call_route( '/unexisting-route/' );
            }
            catch( Exception $e )
            {
                $Exception = $e->getMessage();
            }

            $Msg = "The processor was not found for the route";

            $this->assertNotFalse( strpos( $Exception , $Msg ) , 'Valid error handling expected' );
        }

        /**
        *   Testing action fetching method.
        */
        public function testClassActions()
        {
            $Router = new Router();
            $Router->fetch_actions( $this );

            $Content = $Router->call_route( '/a1/' );
            $this->assertEquals( 'action #1' , $Content , 'Invalid a1 route' );

            $Content = $Router->call_route( '/a2/' );
            $this->assertEquals( 'action #2' , $Content , 'Invalid a2 route' );
        }

        /**
        *   Testing one processor for all routes.
        */
        public function testSingleAllProcessor()
        {
            $Router = new Router();
            $Router->add_route( '*' , array( $this , 'hello_world_output' ) );

            $Content = $Router->call_route( '/some-route/' );

            $this->assertEquals( 'Hello world!' , $Content , 'Invalid index route' );
        }

        /**
        *   Testing one processor for all routes overlap.
        */
        public function testSingleAllProcessorOverlapUnexisting()
        {
            $Router = new Router();
            $Router->add_route( '*' , array( $this , 'hello_world_output' ) );
            $Router->add_route( '/index/' , 'RouterTest::static_hello_world_output' );

            $Content = $Router->call_route( '/some-route/' );

            $this->assertEquals( 'Hello world!' , $Content , 'Invalid index route' );
        }

        /**
        *   Testing one processor for all routes overlap.
        */
        public function testSingleAllProcessorOverlapExisting()
        {
            $Router = new Router();
            $Router->add_route( '*' , array( $this , 'hello_world_output' ) );
            $Router->add_route( '/index/' , 'RouterTest::static_hello_world_output' );

            $Content = $Router->call_route( '/index/' );

            $this->assertEquals( 'Hello world!' , $Content , 'Invalid index route' );
        }

        /**
        *   Testing one processor for all routes overlap.
        */
        public function testSingleAllProcessorExisting()
        {
            $Router = new Router();
            $Router->add_route( '/index/' , 'RouterTest::static_hello_world_output' );
            $Router->add_route( '*' , array( $this , 'hello_world_output' ) );

            $Content = $Router->call_route( '/index/' );

            $this->assertEquals( 'Hello static world!' , $Content , 'Invalid index route' );
        }

        /**
        *   Testing one processor for all routes overlap.
        */
        public function testSingleAllProcessorUnexisting()
        {
            $Router = new Router();
            $Router->add_route( '/index/' , 'RouterTest::static_hello_world_output' );
            $Router->add_route( '*' , array( $this , 'hello_world_output' ) );

            $Content = $Router->call_route( '/some-route/' );

            $this->assertEquals( 'Hello world!' , $Content , 'Invalid index route' );
        }

        /**
        *   Testing invalid data types behaviour.
        */
        public function testInvalidType()
        {
            $Exception = '';
            $Router = new Router();
            $Router->add_route( '/catalog/[unexisting-type:i]/item/' , array( $this , 'hello_world_output' ) );

            try
            {
                $Router->call_route( '/catalog/1024/item/' );
            }
            catch( Exception $e )
            {
                $Exception = $e->getMessage();
            }

            $Msg = "Illegal parameter type : unexisting-type";

            $this->assertNotFalse( strpos( $Exception , $Msg ) , 'Valid error handling expected' );
        }

        /**
        *   Testing invalid data types behaviour.
        */
        public function testValidInvalidTypes()
        {
            $Exception = '';
            $Router = new Router();
            $Router->add_route( 
                '/catalog/[i:cat_id]/item/[unexisting-type-trace:item_id]/' , array( $this , 'hello_world_output' )
            );

            try
            {
                $Router->call_route( '/catalog/1024/item/2048/' );
            }
            catch( Exception $e )
            {
                $Exception = $e->getMessage();
            }

            $Msg = "Illegal parameter type : unexisting-type";

            $this->assertNotFalse( strpos( $Exception , $Msg ) , 'Valid error handling expected'." /e:$Exception/" );
        }

        /**
        *   Testing valid data types behaviour.
        */
        public function testValidTypes()
        {
            $Exception = '';
            $Router = new Router();
            $Router->add_route( 
                '/catalog/[i:cat_id]/item/[i:item_id]/' , array( $this , 'hello_world_output' )
            );

            try
            {
                $Router->call_route( '/catalog/1024/item/2048/' );
            }
            catch( Exception $e )
            {
                $Exception = $e->getMessage();
            }

            $Msg = "Illegal parameter type";

            $this->assertFalse( strpos( $Exception , $Msg ) , 'Valid type expected' );
        }

        /**
        *   Testing valid integer data types behaviour.
        */
        public function testValidIntegerParams()
        {
            $Exception = '';
            $Router = new Router();
            $Router->add_route( 
                '/catalog/[i:cat_id]/' , array( $this , 'hello_world_output' )
            );

            try
            {
                $Router->call_route( '/catalog/1024/' );
            }
            catch( Exception $e )
            {
                $Exception = $e->getMessage();
            }

            $Msg = "Illegal parameter type";

            $this->assertFalse( strpos( $Exception , $Msg ) , 'Valid type expected' );
        }

        /**
        *   Testing valid alnum data types behaviour.
        */
        public function testValidAlnumParams()
        {
            $Exception = '';
            $Router = new Router();
            $Router->add_route( 
                '/catalog/[a:cat_id]/' , array( $this , 'hello_world_output' )
            );

            try
            {
                $Router->call_route( '/catalog/foo/' );
            }
            catch( Exception $e )
            {
                $Exception = $e->getMessage();
            }

            $Msg = "Illegal parameter type";

            $this->assertFalse( strpos( $Exception , $Msg ) , 'Valid type expected' );
        }

        /**
        *   Testing invalid integer data types behaviour.
        */
        public function testInValidIntegerParams()
        {
            $Exception = '';
            $Router = new Router();
            $Router->add_route( 
                '/catalog/[i:cat_id]/' , array( $this , 'hello_world_output' )
            );

            try
            {
                $Router->call_route( '/catalog/a1024/' );
            }
            catch( Exception $e )
            {
                $Exception = $e->getMessage();
            }

            $Msg = "The processor was not found for the route /catalog/a1024/";

            $this->assertNotFalse( strpos( $Exception , $Msg ) , 'Invalid error response' );
        }

        /**
        *   Testing invalid alnum data types behaviour.
        */
        public function testInValidAlnumParams()
        {
            $Exception = '';
            $Router = new Router();
            $Router->add_route( 
                '/catalog/[a:cat_id]/' , array( $this , 'hello_world_output' )
            );

            try
            {
                $Router->call_route( '/catalog/~foo/' );
            }
            catch( Exception $e )
            {
                $Exception = $e->getMessage();
            }

            $Msg = "The processor was not found for the route /catalog/~foo/";

            $this->assertNotFalse( strpos( $Exception , $Msg ) , 'Invalid error response' );
        }

        /**
        *   Testing parameter extractor.
        */
        public function testValidExtractedParameter()
        {
            $Router = new Router();
            $Router->add_route( 
                '/catalog/[a:cat_id]/' , function( $Route , $Parameters ){return($Parameters[ 'cat_id' ]);}
            );

            $Result = $Router->call_route( '/catalog/foo/' );

            $this->assertEquals( $Result , 'foo' , 'Invalid extracted parameter' );
        }

        /**
        *   Testing parameter extractor.
        */
        public function testValidExtractedParameters()
        {
            $Router = new Router();
            $Router->add_route( 
                '/catalog/[a:cat_id]/[i:item_id]' , 
                function( $Route , $Parameters ){return($Parameters[ 'cat_id' ].$Parameters[ 'item_id' ]);}
            );

            $Result = $Router->call_route( '/catalog/foo/1024/' );

            $this->assertEquals( $Result , 'foo1024' , 'Invalid extracted parameter' );
        }

        /**
        *   Testing parameter extractor.
        */
        public function testValidRouteParameter()
        {
            $Router = new Router();
            $Router->add_route( 
                '/catalog/' , 
                function( $Route , $Parameters ){return($Route);}
            );
            $Router->add_route( 
                '/catalog/[i:cat_id]' , 
                function( $Route , $Parameters ){return($Route);}
            );

            $Result = $Router->call_route( '/catalog/' );

            $this->assertEquals( $Result , '/catalog/' , 'Invalid extracted route' );

            $Result = $Router->call_route( '/catalog/1024/' );

            $this->assertEquals( $Result , '/catalog/1024/' , 'Invalid extracted route' );
        }

        /**
        *   Testing static routes for POST requests.
        */
        public function testPostRequestForExistingStaticRoute()
        {
            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'POST';

            $Router = new Router();
            $Router->add_route( 
                '/catalog/' , 
                function( $Route , $Parameters ){return($Route);} , 
                'POST'
            );

            $Result = $Router->call_route( '/catalog/' );

            $this->assertEquals( $Result , '/catalog/' , 'Invalid extracted route' );
        }

        /**
        *   Testing dynamic routes for POST requests.
        */
        public function testPostRequestForExistingDynamicRoute()
        {
            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'POST';

            $Router = new Router();
            $Router->add_route( 
                '/catalog/[i:cat_id]' , 
                function( $Route , $Parameters ){return($Route);} , 
                'POST'
            );

            $Result = $Router->call_route( '/catalog/1024/' );

            $this->assertEquals( $Result , '/catalog/1024/' , 'Invalid extracted route' );
        }

        /**
        *   Testing static routes for POST requests.
        */
        public function testPostRequestForUnExistingStaticRoute()
        {
            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'POST';

            $Exception = '';
            $Router = new Router();
            $Router->add_route( 
                '/catalog/' , array( $this , 'hello_world_output' )
            );

            try
            {
                $Router->call_route( '/catalog/' );
            }
            catch( Exception $e )
            {
                $Exception = $e->getMessage();
            }

            $Msg = "The processor was not found for the route /catalog/";

            $this->assertNotFalse( strpos( $Exception , $Msg ) , 'Invalid error response' );
        }

        /**
        *   Testing dynamic routes for POST requests.
        */
        public function testPostRequestForUnExistingDynamicRoute()
        {
            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'POST';

            $Exception = '';
            $Router = new Router();
            $Router->add_route( 
                '/catalog/[i:cat_id]' , array( $this , 'hello_world_output' )
            );

            try
            {
                $Router->call_route( '/catalog/1024/' );
            }
            catch( Exception $e )
            {
                $Exception = $e->getMessage();
            }

            $Msg = "The processor was not found for the route /catalog/1024/";

            $this->assertNotFalse( strpos( $Exception , $Msg ) , 'Invalid error response' );
        }

        /**
        *   Testing static routes for PUT requests.
        */
        public function testPutRequestForExistingStaticRoute()
        {
            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'PUT';

            $Router = new Router();
            $Router->add_route( 
                '/catalog/' , 
                function( $Route , $Parameters ){return($Route);} , 
                'PUT'
            );

            $Result = $Router->call_route( '/catalog/' );

            $this->assertEquals( $Result , '/catalog/' , 'Invalid extracted route' );
        }

        /**
        *   Testing dynamic routes for PUT requests.
        */
        public function testPutRequestForExistingDynamicRoute()
        {
            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'PUT';

            $Router = new Router();
            $Router->add_route( 
                '/catalog/[i:cat_id]' , 
                function( $Route , $Parameters ){return($Route);} , 
                'PUT'
            );

            $Result = $Router->call_route( '/catalog/1024/' );

            $this->assertEquals( $Result , '/catalog/1024/' , 'Invalid extracted route' );
        }

        /**
        *   Testing static routes for PUT requests.
        */
        public function testPutRequestForUnExistingStaticRoute()
        {
            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'PUT';

            $Exception = '';
            $Router = new Router();
            $Router->add_route( 
                '/catalog/' , array( $this , 'hello_world_output' )
            );

            try
            {
                $Router->call_route( '/catalog/' );
            }
            catch( Exception $e )
            {
                $Exception = $e->getMessage();
            }

            $Msg = "The processor was not found for the route /catalog/";

            $this->assertNotFalse( strpos( $Exception , $Msg ) , 'Invalid error response' );
        }

        /**
        *   Testing dynamic routes for PUT requests.
        */
        public function testPutRequestForUnExistingDynamicRoute()
        {
            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'PUT';

            $Exception = '';
            $Router = new Router();
            $Router->add_route( 
                '/catalog/[i:cat_id]' , array( $this , 'hello_world_output' )
            );

            try
            {
                $Router->call_route( '/catalog/1024/' );
            }
            catch( Exception $e )
            {
                $Exception = $e->getMessage();
            }

            $Msg = "The processor was not found for the route /catalog/1024/";

            $this->assertNotFalse( strpos( $Exception , $Msg ) , 'Invalid error response' );
        }

        /**
        *   Testing static routes for DELETE requests.
        */
        public function testDeleteRequestForExistingStaticRoute()
        {
            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'DELETE';

            $Router = new Router();
            $Router->add_route( 
                '/catalog/' , 
                function( $Route , $Parameters ){return($Route);} , 
                'DELETE'
            );

            $Result = $Router->call_route( '/catalog/' );

            $this->assertEquals( $Result , '/catalog/' , 'Invalid extracted route' );
        }

        /**
        *   Testing dynamic routes for DELETE requests.
        */
        public function testDeleteRequestForExistingDynamicRoute()
        {
            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'DELETE';

            $Router = new Router();
            $Router->add_route( 
                '/catalog/[i:cat_id]' , 
                function( $Route , $Parameters ){return($Route);} , 
                'DELETE'
            );

            $Result = $Router->call_route( '/catalog/1024/' );

            $this->assertEquals( $Result , '/catalog/1024/' , 'Invalid extracted route' );
        }

        /**
        *   Testing static routes for DELETE requests.
        */
        public function testDeleteRequestForUnExistingStaticRoute()
        {
            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'DELETE';

            $Exception = '';
            $Router = new Router();
            $Router->add_route( 
                '/catalog/' , array( $this , 'hello_world_output' )
            );

            try
            {
                $Router->call_route( '/catalog/' );
            }
            catch( Exception $e )
            {
                $Exception = $e->getMessage();
            }

            $Msg = "The processor was not found for the route /catalog/";

            $this->assertNotFalse( strpos( $Exception , $Msg ) , 'Invalid error response' );
        }

        /**
        *   Testing dynamic routes for DELETE requests.
        */
        public function testDeleteRequestForUnExistingDynamicRoute()
        {
            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'DELETE';

            $Exception = '';
            $Router = new Router();
            $Router->add_route( 
                '/catalog/[i:cat_id]' , array( $this , 'hello_world_output' )
            );

            try
            {
                $Router->call_route( '/catalog/1024/' );
            }
            catch( Exception $e )
            {
                $Exception = $e->getMessage();
            }

            $Msg = "The processor was not found for the route /catalog/1024/";

            $this->assertNotFalse( strpos( $Exception , $Msg ) , 'Invalid error response' );
        }

        /**
        *   Testing case when both GET and POST processors exists.
        *   
        */
        public function testGetPostPostDeleteRouteConcurrency()
        {
            $Router = new Router();
            $Router->add_route( 
                '/catalog/' , function( $Route , $Parameters ){return('POST');} , 'POST'
            );
            $Router->add_route( 
                '/catalog/' , function( $Route , $Parameters ){return('GET');} , 'GET'
            );
            $Router->add_route( 
                '/catalog/' , function( $Route , $Parameters ){return('PUT');} , 'PUT'
            );
            $Router->add_route( 
                '/catalog/' , function( $Route , $Parameters ){return('DELETE');} , 'DELETE'
            );

            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'POST';

            $Result = $Router->call_route( '/catalog/' );

            $this->assertEquals( $Result , 'POST' , 'Invalid selected route' );

            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'GET';

            $Result = $Router->call_route( '/catalog/' );

            $this->assertEquals( $Result , 'GET' , 'Invalid selected route' );

            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'PUT';

            $Result = $Router->call_route( '/catalog/' );

            $this->assertEquals( $Result , 'PUT' , 'Invalid selected route' );

            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'DELETE';

            $Result = $Router->call_route( '/catalog/' );

            $this->assertEquals( $Result , 'DELETE' , 'Invalid selected route' );
        }
    }

?>