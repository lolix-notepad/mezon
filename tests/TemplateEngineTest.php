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
    }

?>