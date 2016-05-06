<?php

    global          $MEZON_PATH;
    $MEZON_PATH = dirname( dirname( __FILE__ ) );

    require_once( $MEZON_PATH.'/vendor/basic-application/basic-application.php' );

    /**
    *   Application for testing purposes.
    */
    class           TestBasicApplication extends BasicApplication
    {
        function            action_existing()
        {
            /* existing action */

            return( 'OK!!!' );
        }
    }

    class BasicApplicationTest extends PHPUnit_Framework_TestCase
    {
        /**
        *   Running with incorrect router.
        */
        public function testInCorrectRoute()
        {
            $Application = new TestBasicApplication();

            $_GET[ 'r' ] = '/existing/';

            $this->expectOutputRegex( '/OK!!!/' );
            $this->expectOutputRegex( '/head/' );
            $this->expectOutputRegex( '/html/' );
            $this->expectOutputRegex( '/title/' );
            $this->expectOutputRegex( '/body/' );

            $Application->run();
        }
    }

?>