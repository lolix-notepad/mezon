<?php

    require_once( dirname( dirname( __FILE__ ) ).'/conf/conf.php' );
    require_once( MEZON_PATH.'/vendor/basic-template/basic-template.php' );
    require_once( MEZON_PATH.'/vendor/common-application/common-application.php' );

    /**
    *   Application for testing purposes.
    */
    class           TestCommonApplication extends CommonApplication
    {
		/**
		*	Constructor.
		*/
		function			__construct()
		{
			parent::__construct( new BasicTemplate() );
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

    class CommonApplicationTest extends PHPUnit\Framework\TestCase
    {
        /**
        *   Running with complex router result.
        */
        public function testComplexRouteResult()
        {
            $Application = new TestCommonApplication();

			$_SERVER[ 'REQUEST_METHOD' ] = 'GET';
            $_GET[ 'r' ] = '/array-result/';

            ob_start();
            $Application->run();
            $Output = ob_get_contents();
            ob_end_clean();

            $this->assertTrue( strpos( $Output , 'Route title' ) !== false , 'Template compilation failed (1)' );
            $this->assertTrue( strpos( $Output , 'Route main' ) !== false , 'Template compilation failed (2)' );
        }

        /**
        *   Compiling page with functional view.
        */
        public function testComplexViewRenderring()
        {
            $Application = new TestCommonApplication();

			$_SERVER[ 'REQUEST_METHOD' ] = 'GET';
            $_GET[ 'r' ] = '/view-result/';

            ob_start();
            $Application->run();
            $Output = ob_get_contents();
            ob_end_clean();

            $this->assertTrue( strpos( $Output , 'Route title' ) !== false , 'Template compilation failed (3)' );
            $this->assertTrue( strpos( $Output , 'Test view result' ) !== false , 'Template compilation failed (4)' );
        }
    }

?>