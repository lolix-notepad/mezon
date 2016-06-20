<?php

    require_once( dirname( dirname( __FILE__ ) ).'/conf/conf.php' );
    require_once( $MEZON_PATH.'/vendor/application/application.php' );

    /**
    *   Application for testing purposes.
    */
    class           TestApplication extends Application
    {
        function __construct()
        {
            if( is_object( $this->Router ) )
            {
                $this->Router->clear();
            }

            parent::__construct();
        }

        function            action_existing()
        {
            /* existing action */

            return( 'OK!' );
        }
    }

    class ApplicationTest extends PHPUnit_Framework_TestCase
    {
        /**
        *   Running with correct router.
        */
        public function testCorrectRoute()
        {
            $Application = new TestApplication();

            $_GET[ 'r' ] = '/existing/';

            $this->expectOutputString( 'OK!' );

            $Application->run();
        }

        /**
        *   Running with incorrect router.
        */
        public function testInCorrectRoute()
        {
            $Application = new TestApplication();

            $_GET[ 'r' ] = '/unexisting/';

            $this->expectOutputRegex( '/The processor was not found for the route/' );

            $Application->run();
        }

        /**
        *   Test config structure validators.
        */
        public function testConfigValidatorsRoute()
        {
            $Application = new TestApplication();

            $Msg = '';

            try
            {
                $Application->load_routes_from_config( dirname( dirname( __FILE__ ) ).'/tests/test-invalid-routes-1.php' );
            }
            catch( Exception $e )
            {
                $Msg = $e->getMessage();
            }

            $this->assertEquals( 'Field "route" must be set' , $Msg , 'Invalid behavior' );
        }
        
        /**
        *   Test config structure validators.
        */
        public function testConfigValidatorsCallback()
        {
            $Application = new TestApplication();

            $Msg = '';

            try
            {
                $Application->load_routes_from_config( dirname( dirname( __FILE__ ) ).'/tests/test-invalid-routes-2.php' );
            }
            catch( Exception $e )
            {
                $Msg = $e->getMessage();
            }

            $this->assertEquals( 'Field "callback" must be set' , $Msg , 'Invalid behavior' );
        }

        /**
        *   Testing loading routes from config file.
        */
        public function testRoutesConfig()
        {
            $Application = new TestApplication();

            $Application->load_routes_from_config( dirname( dirname( __FILE__ ) ).'/tests/test-routes.php' );

            $_GET[ 'r' ] = '/get-route/';

            $this->expectOutputString( 'OK!' );

            $Application->run();
        }

        /**
        *   Testing loading POST routes from config file.
        */
        public function testPostRoutesConfig()
        {
            global $_SERVER;
            $_SERVER[ 'REQUEST_METHOD' ] = 'POST';

            $Application = new TestApplication();

            $Application->load_routes_from_config( dirname( dirname( __FILE__ ) ).'/tests/test-routes.php' );

            $_GET[ 'r' ] = '/post-route/';

            $this->expectOutputString( 'OK!' );

            $Application->run();
        }
    }

?>