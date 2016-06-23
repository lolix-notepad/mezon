<?php

    global          $MEZON_PATH;
    require_once( $MEZON_PATH.'/vendor/template-engine/template-engine.php' );

    class TemplateEngineTest extends PHPUnit_Framework_TestCase
    {
        /**
        *   Testing single var substitution.
        */
        public function testSingleVar()
        {
            $TemplateEngine = new TemplateEngine();

			$TemplateEngine->set_page_var( 'var1' , 'Value 1' );

			$Content = ' {var1} ';

			$TemplateEngine->compile_page_vars( $Content );

			$this->assertEquals( 1 , strpos( $Content , 'Value 1' ) , 'Substitution was not performed' );

            $TemplateEngine->destroy();
        }

		/**
        *   Testing two vars substitution.
        */
		public function testTwoVars()
        {
            $TemplateEngine = new TemplateEngine();

			$TemplateEngine->set_page_var( 'var1' , 'Value 1' );
			$TemplateEngine->set_page_var( 'var2' , 'Value 2' );

			$Content = ' {var1} {var2}';

			$TemplateEngine->compile_page_vars( $Content );

			$this->assertEquals( 1 , strpos( $Content , 'Value 1' ) , 'Substitution 1 was not performed' );
			$this->assertEquals( 9 , strpos( $Content , 'Value 2' ) , 'Substitution 2 was not performed' );

            $TemplateEngine->destroy();
        }

        /**
        *   This test validates singleton behavior.
        */
        public function testSingletonConstructorBehaviour()
        {
            $TemplateEngine = new TemplateEngine();

            $Msg = '';

            try
            {
                // Second instance.
                new TemplateEngine();
            }
            catch( Exception $e )
            {
                $Msg = $e->getMessage();
            }

            $this->assertEquals(
                'You can not create more than one copy of a singleton '.
                'of type TemplateEngine' , $Msg , 'Invalid behavior'
            );

            $TemplateEngine->destroy();

            try
            {
                new TemplateEngine();
            }
            catch( Exception $e )
            {
                $this->assertFalse( true , 'Invalid behavior' );
            }

            $TemplateEngine->destroy();
        }

        /**
        *   Simple vars.
        */
        public function testSimpleSubstitutionsArray()
        {
            $Data = array( 'var1' => 'v1' , 'var2' => 'v2' );
            $String = '{var1} {var2}';

            $String = TemplateEngine::print_record( $String , $Data );

            $this->assertEquals( $String , 'v1 v2' , 'Invalid string processing' );
        }
        
        /**
        *   Simple vars.
        */
        public function testSimpleSubstitutionsObject()
        {
            $Data = new stdClass();
            $Data->var1 = 'v1';
            $Data->var2 = 'v2';
            $String = '{var1} {var2}';

            $String = TemplateEngine::print_record( $String , $Data );

            $this->assertEquals( $String , 'v1 v2' , 'Invalid string processing' );
        }

        /**
        *   Nested objects.
        */
        public function testSimpleSubstitutionsNestedArray()
        {
            $Data = array( 'var1' => 'v1' , 'var2' => 'v2' , 'field' => array( 'var3' => 'v3' ) );
            $String = '{var1} {var2} {var3}';

            $String = TemplateEngine::print_record( $String , $Data );

            $this->assertEquals( $String , 'v1 v2 v3' , 'Invalid string processing' );
        }
        
        /**
        *   Invalid objects.
        */
        public function testSimpleSubstitutionsInvalidObjects()
        {
            try
            {
                $String = '';
                $String = TemplateEngine::print_record( $String , false );
            }
            catch( Exception $e )
            {
                $Msg = $e->getMessage();
            }

            $this->assertEquals( 'Invalid record was passed' , $Msg , 'Invalid behavior' );
            
            try
            {
                $String = '';
                $String = TemplateEngine::print_record( $String , null );
            }
            catch( Exception $e )
            {
                $Msg = $e->getMessage();
            }

            $this->assertEquals( 'Invalid record was passed' , $Msg , 'Invalid behavior' );
            
            try
            {
                $String = '';
                $String = TemplateEngine::print_record( $String , 1234 );
            }
            catch( Exception $e )
            {
                $Msg = $e->getMessage();
            }

            $this->assertEquals( 'Invalid record was passed' , $Msg , 'Invalid behavior' );

            try
            {
                $String = '';
                $String = TemplateEngine::print_record( $String , 'string' );
            }
            catch( Exception $e )
            {
                $Msg = $e->getMessage();
            }

            $this->assertEquals( 'Invalid record was passed' , $Msg , 'Invalid behavior' );
        }

        // передаётся шит в виде null или false
    }

?>