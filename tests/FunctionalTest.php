<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/functional/functional.php' );

    /**
    *   Transformation function multiplies 'foo' field.
    */
    function  transform2x( $Object )
    {
        $Object->foo *= 2;

        return( $Object );
    }

    class FunctionalTest extends PHPUnit\Framework\TestCase
    {
		/**
		*	Testing getting field function.
		*/
		public function testGetFieldArray()
		{
			$Arr = array( 'foo' => 'bar' );
			
			$Result = Functional::get_field( $Arr , 'foo' );

            $this->assertEquals( $Result , 'bar' , 'Invalid value' );
		}

		/**
		*	Testing getting field function.
		*/
		public function testGetField2Array()
		{
			$Arr = array( 'foo' => 'bar', 'foo2' => 'bar2' );
			
			$Result = Functional::get_field( $Arr , 'foo2' );

            $this->assertEquals( $Result , 'bar2' , 'Invalid value' );
		}
		
		/**
		*	Testing getting field function.
		*/
		public function testGetFieldObject()
		{
			$obj = new stdClass();
            $obj->foo = 'bar';
			
			$Result = Functional::get_field( $obj , 'foo' );

            $this->assertEquals( $Result , 'bar' , 'Invalid value' );
		}

		/**
		*	Testing getting field function.
		*/
		public function testGetField2Object()
		{
			$obj = new stdClass();
            $obj->foo = 'bar';
            $obj->foo2 = 'bar2';
			
			$Result = Functional::get_field( $obj , 'foo2' );

            $this->assertEquals( $Result , 'bar2' , 'Invalid value' );
		}

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

        /**
        *   Method will test transformation function.
        */
        public function testTransform()
        {
            $obj1 = new stdClass();
            $obj1->foo = 1;

            $obj2 = new stdClass();
            $obj2->foo = 2;

            $obj3 = new stdClass();
            $obj3->foo = 3;

            $Data = array( $obj1 , $obj2 , $obj3 );

            Functional::transform( $Data , 'transform2x' );

            $this->assertEquals( $Data[ 0 ]->foo , 2 , 'Invalid value' );
            $this->assertEquals( $Data[ 1 ]->foo , 4 , 'Invalid value' );
            $this->assertEquals( $Data[ 2 ]->foo , 6 , 'Invalid value' );
        }

        /**
        *   Testing recursive fields summation.
        */
        public function testRecursiveSum()
        {
            $obj1 = new stdClass();
            $obj1->foo = 1;

            $obj2 = new stdClass();
            $obj2->foo = 2;

            $obj3 = new stdClass();
            $obj3->foo = 3;

            $Data = array( $obj1 , array( $obj2 , $obj3 ) );

            $this->assertEquals( Functional::sum_fields( $Data , 'foo' ) , 6 , 'Invalid sum' );
        }

        /**
        *   This method is testing filtration function.
        */
        public function testFilterSimple()
        {
            $obj1 = new stdClass();
            $obj1->foo = 1;

            $obj2 = new stdClass();
            $obj2->foo = 2;

            $obj3 = new stdClass();
            $obj3->foo = 1;

            $Data = array( $obj1 , $obj2 , $obj3 );

            $this->assertEquals(
				count( Functional::filter( $Data , 'foo' , '==' , 1 ) ) , 2 , 'Invalid filtration'
			);
        }

        /**
        *   This method is testing filtration function in a recursive mode.
        */
        public function testFilterRecursive()
        {
            $obj1 = new stdClass();
            $obj1->foo = 1;

            $obj2 = new stdClass();
            $obj2->foo = 2;

            $obj3 = new stdClass();
            $obj3->foo = 1;

            $Data = array( $obj1 , array( $obj2 , $obj3 ) );

            $this->assertEquals( 
				count( Functional::filter( $Data , 'foo' , '==' , 1 ) ) , 2 , 'Invalid filtration'
			);
        }

		/**
        *   This method is testing filtration function in a recursive mode.
        */
        public function testGetFieldRecursive()
        {
            $obj1 = new stdClass();
            $obj1->foo = 1;

            $obj2 = new stdClass();
            $obj2->bar = 2;
			$obj1->obj2 = $obj2;

            $obj3 = new stdClass();
            $obj3->eak = 3;
			$obj1->obj3 = $obj3;

            $this->assertEquals( Functional::get_field( $obj1 , 'eak' ) , 3 , 'Invalid getting' );
        }
    }

?>