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

        /**
        *   Testing setup of the existing key with complex route. It's value must be overwritten.
        */
        public function testSetComplexExistingKey()
        {
            $Value = get_config_value( array( 'res' , 'images' , 'favicon' ) );

            $this->assertEquals( 'http:////res/images/favicon.ico' , $Value , 'Invalid unexisting-key processing' );

            set_config_value( 'res/images/favicon' , 'set-value' );

            $Value = get_config_value( array( 'res' , 'images' , 'favicon' ) );

            $this->assertEquals( 'set-value' , $Value , 'Invalid res/images/favicon value' );
        }

        /**
        *   Testing setup of the unexisting key with complex route. It's value must be overwritten.
        */
        public function testSetComplexUnExistingKey()
        {
            $Value = get_config_value( array( 'res' , 'images' , 'unexisting-key' ) );

            $this->assertEquals( false , $Value , 'Invalid res/images/unexisting-key processing' );

            set_config_value( 'res/images/unexisting-key' , 'set-value' );

            $Value = get_config_value( array( 'res' , 'images' , 'unexisting-key' ) );

            $this->assertEquals( 'set-value' , $Value , 'Invalid res/images/unexisting-key value' );
        }

        /**
        *   Testing setup of the existing array.
        */
        public function testAddComplexExistingArray()
        {
            $Value = get_config_value( array( 'res' , 'css' ) );

            $this->assertContains( 'http:////res/css/application.css' , $Value , 'Invalid css files list' );

            add_config_value( 'res/css' , 'set-value' );

            $Value = get_config_value( array( 'res' , 'css' ) );

            $this->assertContains( 'set-value' , $Value , 'Invalid css files list' );
        }

        /**
        *   Testing setup of the unexisting array.
        */
        public function testAddComplexUnExistingArray()
        {
            $Value = get_config_value( array( 'unexisting-key' ) );

            $this->assertEquals( false , $Value , 'Invalid unexisting-key processing' );

            add_config_value( 'unexisting-key' , 'set-value' );

            $Value = get_config_value( array( 'unexisting-key' ) );

            $this->assertContains( 'set-value' , $Value , 'Invalid unexisting-key value' );
        }

        /**
        *   Testing setup of the unexisting array with simple route.
        */
        public function testAddUnExistingArray()
        {
            $Value = get_config_value( array( 'unexisting-key' ) );

            $this->assertEquals( false , $Value , 'Invalid unexisting-key processing' );

            add_config_value( 'unexisting-key' , 'set-value' );

            $Value = get_config_value( array( 'unexisting-key' ) );

            $this->assertContains( 'set-value' , $Value , 'Invalid unexisting-key value' );
        }

        /**
        *   Testing setup of the existing array with simple route.
        */
        public function testAddExistingArray()
        {
            add_config_value( 'unexisting-key' , 'set-value-1' );
            add_config_value( 'unexisting-key' , 'set-value-2' );

            $Value = get_config_value( array( 'unexisting-key' ) );

            $this->assertContains( 'set-value-2' , $Value , 'Invalid unexisting-key value' );
        }

        /**
        *   Testing setup of the existing array with simple route.
        */
        public function testComplexStringRoutes()
        {
            set_config_value( 'f1/f2/unexisting-key' , 'set-value-1' );

            $Value = get_config_value( 'f1/f2/unexisting-key' );

            $this->assertEquals( 'set-value-1' , $Value , 'Invalid unexisting-key value' );
        }

        //TODO: create documentation for config manipulation routines
    }

?>