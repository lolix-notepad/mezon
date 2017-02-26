<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/pdo-crud/pdo-crud.php' );

    class ProCrudTest extends PHPUnit\Framework\TestCase
    {
        /**
        * Call protected/private method of a class.
        *
        * @param object &$object    Instantiated object that we will run method on.
        * @param string $methodName Method name to call
        * @param array  $parameters Array of parameters to pass into method.
        *
        * @return mixed Method return.
        */
        public function invokeMethod( &$Object , $MethodName , array $Parameters = array() )
        {
            $reflection = new \ReflectionClass( get_class( $Object ) );
            $method = $reflection->getMethod( $MethodName );
            $method->setAccessible( true );

            return( $method->invokeArgs( $Object , $Parameters ) );
        }

        /**
        *   Testing select query.
        */
        public function testSelectQuery()
        {
            $Object = new PdoCrud();

            $Result = $this->invokeMethod( $Object , 'select_query' , array( 'field1,field2' , 'table' , 'id=1' , 1 , 10 ) );

            $this->assertEquals( 'SELECT field1,field2 FROM table WHERE id=1 LIMIT 1 , 10' , $Result , 'Invalid query' );
        }

		/**
        *   Testing update query.
        */
        public function testUpdateQuery()
        {
            $Object = new PdoCrud();

            $Result = $this->invokeMethod( $Object , 'update_query' , array( 'table' , array( 'f1' => 'value' , 'f2' => 1 ) , 'id=1' ) );

            $this->assertEquals( 'UPDATE table SET f1 = "value" , f2 = 1 WHERE id=1' , $Result , 'Invalid query' );
        }

		/**
        *   Testing delete query.
        */
        public function testDeleteQuery()
        {
            $Object = new PdoCrud();

            $Result = $this->invokeMethod( $Object , 'delete_query' , array( 'table' , 'id=1' , 10 ) );

            $this->assertEquals( 'DELETE FROM table WHERE id=1 LIMIT 10' , $Result , 'Invalid query' );
        }

		/**
        *   Testing lock query.
        */
        public function testLockQuery()
        {
            $Object = new PdoCrud();

            $Result = $this->invokeMethod( $Object , 'lock_query' , array( array( 'table1' , 'table2' ) , array( 'READ' , 'WRITE' ) ) );

            $this->assertEquals( 'LOCK TABLES table1 READ , table2 WRITE' , $Result , 'Invalid query' );
        }
    }

?>