<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/view/view.php' );

    class ViewTest extends PHPUnit\Framework\TestCase
    {
        /**
        *   Testing additing CSS file.
        */
        public function testBasicRenderring()
        {
            $View = new View( 'test' );

            $this->assertEquals( $View->render( array() ) , 'test' , 'Invalid output' );
        }
    }

?>