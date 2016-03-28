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
        public function testOneComponentRouter()
        {
            $Router = new Router();
            $Router->add_route( '/index/' , array( $this , 'hello_world_output' ) );

            $Content = $Router->call_route( '/index/' );

            $this->assertEquals( 'Hello world!' , $Content , 'Invalid index route' );
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
    }

?>