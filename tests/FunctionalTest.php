<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/functional/functional.php' );

    class FunctionalTest extends PHPUnit_Framework_TestCase
    {
        /**
        *   Testing fields fetching.
        */
        public function testFieldsFetching()
        {
            $obj1 = new stdClass();
            $obj1->foo = 1;

            $obj2 = new stdClass();
            $obj2->foo = 2;

            $obj3 = new stdClass();
            $obj3->foo = 3;

            $Data = array( $obj1 , $obj2 , $obj3 );

            $Result = Functional::get_fields( $Data , 'foo' );

            $this->assertEquals( count( $Result ) , 3 , 'Invalid count' );

            $this->assertEquals( $Result[ 0 ] , 1 , 'Invalid value' );
            $this->assertEquals( $Result[ 1 ] , 2 , 'Invalid value' );
            $this->assertEquals( $Result[ 2 ] , 3 , 'Invalid value' );
        }

        /**
        *   Testing fields setting.
        */
        public function testFieldsSetting()
        {
            $Values = array( 1 , 2 , 3 );
            $obj1 = new stdClass();
            $obj2 = new stdClass();

            $Data = array( $obj1 , $obj2 );

            Functional::set_fields_in_objects( $Data , 'foo' , $Values );

            $this->assertEquals( count( $Data ) , 3 , 'Invalid count' );

            $this->assertEquals( $Data[ 0 ]->foo , 1 , 'Invalid value' );
            $this->assertEquals( $Data[ 1 ]->foo , 2 , 'Invalid value' );
            $this->assertEquals( $Data[ 2 ]->foo , 3 , 'Invalid value' );
        }

        /**
        *   Testing fields summation.
        */
        public function testFieldsSum()
        {
            $obj1 = new stdClass();
            $obj1->foo = 1;

            $obj2 = new stdClass();
            $obj2->foo = 2;

            $obj3 = new stdClass();
            $obj3->foo = 3;

            $Data = array( $obj1 , $obj2 , $obj3 );

            $this->assertEquals( Functional::sum_fields( $Data , 'foo' ) , 6 , 'Invalid sum' );
        }
    }

?>