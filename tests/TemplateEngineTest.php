<?php

    global          $MEZON_PATH;
    require_once( $MEZON_PATH.'/vendor/template-engine/template-engine.php' );

    class TemplateEngineUtility extends TemplateEngine
    {
        public static function  get_possible_block_positions_test( &$Positions )
        {
            return( self::get_possible_block_positions( $Positions ) );
        }

        public static function  get_all_block_positions_test( $Str , $BlockStart , $BlockEnd )
        {
            return( self::get_all_block_positions( $Str , $BlockStart , $BlockEnd ) );
        }

        public static function  get_block_positions_test( $Str , $BlockStart , $BlockEnd )
        {
            return( self::get_block_positions( $Str , $BlockStart , $BlockEnd ) );
        }
    }

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

        /**
        *   Testing block's positions determination.
        */
        public function testGetBlockPositionsSimple()
        {
            $Positions = array( 1 => 's' , 3 => 'e' );

            list( $Start , $End ) = TemplateEngineUtility::get_possible_block_positions_test( $Positions );

            $this->assertEquals( $Start , 1 , 'Invalid positions parsing' );
            $this->assertEquals( $End , 3 , 'Invalid positions parsing' );
        }

        /**
        *   Testing block's positions determination.
        */
        public function testGetBlockPositionsNested()
        {
            $Positions = array( 0 => 's' , 1 => 's' , 3 => 'e' , 4 => 'e' );

            list( $Start , $End ) = TemplateEngineUtility::get_possible_block_positions_test( $Positions );

            $this->assertEquals( $Start , 0 , 'Invalid positions parsing' );
            $this->assertEquals( $End , 4 , 'Invalid positions parsing' );
        }

        /**
        *   Testing block's positions determination.
        */
        public function testGetBlockPositionsOneByOne()
        {
            $Positions = array( 0 => 's' , 1 => 'e' , 3 => 's' , 4 => 'e' );

            list( $Start , $End ) = TemplateEngineUtility::get_possible_block_positions_test( $Positions );

            $this->assertEquals( $Start , 0 , 'Invalid positions parsing' );
            $this->assertEquals( $End , 1 , 'Invalid positions parsing' );
        }

        /**
        *   Testing block's fetching starts and ends.
        */
        public function testFetchBlockStarts()
        {
            $Str = '{block:param1} {var:2} {~block} {block:param1} {~block}';

            $Result = TemplateEngineUtility::get_all_block_positions_test( $Str , 'block:param1' , '~block' );

            $Result = array_keys( $Result );
            
            $this->assertEquals( $Result[ 0 ] , 0 , 'Invalid blocks parsing' );
            $this->assertEquals( $Result[ 1 ] , 23 , 'Invalid blocks parsing' );
            $this->assertEquals( $Result[ 2 ] , 32 , 'Invalid blocks parsing' );
            $this->assertEquals( $Result[ 3 ] , 47 , 'Invalid blocks parsing' );
        }

        /**
        *   Testing block's fetching start and end.
        */
        public function testFetchBlockPosition()
        {
            $Str = '{block:param1} {var:2} {~block} {block:param2} {~block}';

            list( $Start , $End ) = TemplateEngineUtility::get_block_positions_test( $Str , 'block:param2' , '~block' );

            $this->assertEquals( $Start , 32 , 'Invalid blocks parsing' );
            $this->assertEquals( $End , 47 , 'Invalid blocks parsing' );
        }

        /**
        *   Fetching block contents.
        */
        public function testFetchingBlockDatat()
        {
            $Str = '{block:param1} {var:2} {~block} {block:param2}content{~block}';

            $Data = TemplateEngineUtility::get_block_data( $Str , 'block:param2' , '~block' );

            $this->assertEquals( $Data , 'content' , 'Invalid blocks parsing' );
        }

        /**
        *   Method fetches macro parameters.
        */
        public function testGetMacroParameters()
        {
            $Str = ' {foreach:field}{content}{~foreach}';

            $Data = TemplateEngineUtility::get_macro_parameters( $Str , 'foreach' );

            $this->assertEquals( $Data , 'field' , 'Invalid blocks parsing' );
        }

        /**
        *   Method tests foreach macro.
        */
        public function testForeach()
        {
            $Str = '{foreach:field}{content}{~foreach}';
            $Data = array(
                'field' => array(
                    array( 'content' => '1' ) , 
                    array( 'content' => '2' ) , 
                )
            );

            $Data = TemplateEngineUtility::compile_foreach( $Str , $Data );

            $this->assertEquals( $Data , '12' , 'Invalid blocks parsing' );
        }

        /**
        *   Method tests foreach macro.
        */
        public function testPrintRecord()
        {
            $Str = '{foreach:field}{content}{~foreach}';
            $Data = array(
                'field' => array(
                    array( 'content' => '1' ) , 
                    array( 'content' => '2' ) , 
                )
            );

            $Data = TemplateEngineUtility::print_record( $Str , $Data );

            $this->assertEquals( $Data , '12' , 'Invalid blocks parsing' );
        }
    }

?>