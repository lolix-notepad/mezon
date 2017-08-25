<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/simple-output-template/simple-output-template.php' );

    class SimpleOutputTemplateTest extends PHPUnit\Framework\TestCase
    {
        /**
        *   Testing default behavior.
        */
        public function testDefaultBehaviour()
        {
            $Template = new SimpleOutputTemplate( '{data}' );

            $Headers = $Template->get_headers();

            $this->assertFalse( $Headers , 'Invalid default value' );
        }

		/**
		*	Test checks add header method.
		*/
		public function testAddHeaders()
		{
			$Template = new SimpleOutputTemplate( '{data}' );
			$Template->add_header( 'h1' , 'v1' );
			$Template->add_header( 'h2' , 'v2' );

			$Headers = $Template->get_headers();

            $this->assertEquals( count( $Headers ) , 2 , 'Invalid headers' );
		}
    }

?>