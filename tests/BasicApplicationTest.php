<?php

    require_once( dirname( dirname( __FILE__ ) ).'/conf/conf.php' );
    require_once( MEZON_PATH.'/vendor/basic-application/basic-application.php' );

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

        function            action_array_result()
        {
            return(
                array( 
                    'title' => 'Route title' , 
                    'main' => 'Route main'
                )
            );
        }

        function            action_view_result()
        {
            return(
                array( 
                    'title' => 'Route title' , 
                    'main' => new View( 'Test view result' )
                )
            );
        }
    }

    class BasicApplicationTest extends PHPUnit\Framework\TestCase
    {
        /**
        *   Running with incorrect router.
        */
        public function testInCorrectRoute()
        {
            $Application = new TestBasicApplication();

			$_SERVER[ 'REQUEST_METHOD' ] = 'GET';
            $_GET[ 'r' ] = '/existing/';

            ob_start();
            $Application->run();
            $Output = ob_get_contents();
            ob_end_clean();

            $this->assertTrue( strpos( $Output , 'OK!!!' ) !== false , 'Template compilation failed (1)' );
            $this->assertTrue( strpos( $Output , '<html>' ) !== false , 'Template compilation failed (2)' );
            $this->assertTrue( strpos( $Output , '<head>' ) !== false , 'Template compilation failed (3)' );
            $this->assertTrue( strpos( $Output , '<body>' ) !== false , 'Template compilation failed (4)' );
            $this->assertTrue( strpos( $Output , '<title>' ) !== false , 'Template compilation failed (5)' );
        }

        /**
        *   Running with complex router result.
        */
        public function testComplexRouteResult()
        {
            $Application = new TestBasicApplication();

			$_SERVER[ 'REQUEST_METHOD' ] = 'GET';
            $_GET[ 'r' ] = '/array-result/';

            ob_start();
            $Application->run();
            $Output = ob_get_contents();
            ob_end_clean();

            $this->assertTrue( strpos( $Output , 'Route title' ) !== false , 'Template compilation failed (6)' );
            $this->assertTrue( strpos( $Output , 'Route main' ) !== false , 'Template compilation failed (7)' );
        }

        /**
        *   Compiling page with functional view.
        */
        public function testComplexViewRenderring()
        {
            $Application = new TestBasicApplication();

			$_SERVER[ 'REQUEST_METHOD' ] = 'GET';
            $_GET[ 'r' ] = '/view-result/';

            ob_start();
            $Application->run();
            $Output = ob_get_contents();
            ob_end_clean();

            $this->assertTrue( strpos( $Output , 'Route title' ) !== false , 'Template compilation failed (8)' );
            $this->assertTrue( strpos( $Output , 'Test view result' ) !== false , 'Template compilation failed (9)' );
        }
    }

?>