<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/singleton/singleton.php' );

    class SingletonFoo extends Singleton
    {
        public $tmp = 'Default foo value';
    }

    class SingletonBar extends Singleton
    {
        public $tmp = 'Default bar value';
    }

    class SingletonParams extends Singleton
    {
        public $tmp = 0;

        public function __construct( $Param )
        {
            $this->tmp = $Param;
        }
    }

    function hack()
    {
        return( SingletonParams::get_instance( 1 ) );
    }

    class SingletonTest extends PHPUnit_Framework_TestCase
    {
        /**
        *   This test checks common singleton's functionality
        */
        public function testCommonWork()
        {
            $Object = new SingletonFoo();

            $this->assertEquals( 'Default foo value' , $Object->tmp , 'Invalid object returned' );

            $Object->destroy();
        }

        /**
        *   Test checks that second object can't be created.
        */
        public function testDirectSreationTest()
        {
            $Object = new SingletonBar();

            try
            {
                $Object = new SingletonBar();
            }
            catch( Exception $e )
            {
                $Object->destroy();
                return;
            }

            $this->assertFalse( false , 'Invalid object creation' );
        }

        /**
        *   Test checks new ClassName() directives work.
        */
        public function testTwoObjects()
        {
            $Object1 = new SingletonFoo();
            $Object2 = new SingletonBar();

            $this->assertEquals( 'Default foo value' , $Object1->tmp , 'Invalid object returned' );
            $this->assertEquals( 'Default bar value' , $Object2->tmp , 'Invalid object returned' );

            $Object1->destroy();
            $Object2->destroy();
        }

        /**
        *   Test checks new ClassName() directives work.
        */
        public function testCloneObject()
        {
            $Object1 = new SingletonFoo();

            try
            {
                $Object2 = clone $Object1;
            }
            catch( Exception $e )
            {
                $Object1->destroy();
                return;
            }

            $this->assertFalse( false , 'Invalid object cloning' );
        }

        /**
        *   Validating params passing through constructor.
        */
        public function testArgsPassing()
        {
            $Object = hack();

            $this->assertEquals( 1 , $Object->tmp , 'Params were not passed' );

            $Object->destroy();
        }
    }

?>