<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/gui/gui.php' );

    class GUITest extends PHPUnit\Framework\TestCase
    {
        /**
        *   Testing selecting second element.
        */
        public function testSelectSecond()
        {
			$Records = array(
				array( 'id' => 1 , 'title' => 'title 1' ) , 
				array( 'id' => 2 , 'title' => 'title 2' )
			);
            $Str1 = GUI::select_control( 'test' , $Records , 'id' , 'title' , 2 );

			$Str2 = '<select class="form-control" name="test"><option  value="1">title 1</option>'.
						'<option selected value="2">title 2</option></select>';

            $this->assertEquals( $Str1 , $Str2 , 'Invalid HTML' );
        }

		/**
        *   Testing selecting second element.
        */
        public function testSelectFirst()
        {
			$Records = array(
				array( 'id' => 1 , 'title' => 'title 1' ) , 
				array( 'id' => 2 , 'title' => 'title 2' )
			);
            $Str1 = GUI::select_control( 'test' , $Records , 'id' , 'title' , 1 );

			$Str2 = '<select class="form-control" name="test"><option selected value="1">title 1</option>'.
						'<option  value="2">title 2</option></select>';

            $this->assertEquals( $Str1 , $Str2 , 'Invalid HTML' );
        }
    }

?>