<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/view/view.php' );

    class ViewTest extends PHPUnit_Framework_TestCase
    {
        /**
        *   Testing additing CSS file.
        */
        public function testBasicRenderring()
        {
            $View = new View( 'test' );

            $this->assertEquals( $View->render() , 'test' , 'Invalid output' );
        }
    }

?>