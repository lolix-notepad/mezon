<?php

    require_once( dirname( dirname( __FILE__ ) ).'/conf/conf.php' );
    require_once( $MEZON_PATH.'/vendor/application/application.php' );

    /**
    *   Application for testing purposes.
    */
    class           TestApplication extends Application
    {
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
    }

?>