<?php

    require_once( dirname( __FILE__ ).'/../conf/conf.php' );

    class ConfTest extends PHPUnit_Framework_TestCase
    {
        /**
        *   Testing setup of the existing key. It's value must be overwritten.
        */
        public function testSetExistingKey()
        {
            $Value = get_config_value( array( '@app-http-path' ) );

            $this->assertEquals( 'http:///' , $Value , 'Invalid @app-http-path value' );

            set_config_value( '@app-http-path' , 'set-value' );

            $Value = get_config_value( array( '@app-http-path' ) );

            $this->assertEquals( 'set-value' , $Value , 'Invalid @app-http-path value' );
        }

        /**
        *   Testing setup of the unexisting key. It's value must be overwritten.
        */
        public function testSetUnExistingKey()
        {
            $Value = get_config_value( array( 'unexisting-key' ) );

            $this->assertEquals( false , $Value , 'Invalid unexisting-key processing' );

            set_config_value( 'unexisting-key' , 'set-value' );

            $Value = get_config_value( array( 'unexisting-key' ) );

            $this->assertEquals( 'set-value' , $Value , 'Invalid unexisting-key value' );
        }

        //todo: add existing
        //todo: add unexisting
        //todo: set complex route existing
        //todo: set complex route unexisting
        //todo: add complex route existing
        //todo: add complex route unexisting
    }

?>