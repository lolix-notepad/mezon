<?php

    require_once( dirname( __FILE__ ).'/../include/php/router.php' );

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
            $Exception = '';
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
            $Exception = '';
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
            $Exception = '';
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
    }

?>