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
        public function invokeMethod( &$Object , $MethodName , array $Parameters = array())
        {
            $reflection = new \ReflectionClass( get_class( $Object ) );
            $method = $reflection->getMethod( $MethodName );
            $method->setAccessible( true );

            return( $method->invokeArgs( $Object , $Parameters ) );
        }

        /**
        *   Testing one component router.
        */
        public function testOneComponentRouterClassMethod()
        {
            $Object = new PdoCrud();

            $Result = $this->invokeMethod( $Object , 'select_query' , array( 'field1,field2' , 'table' , 'id=1' , 1 , 10 ) );

            $this->assertEquals( 'SELECT field1,field2 FROM table WHERE id=1 LIMIT 1 , 10' , $Result , 'Invalid query' );
        }
    }

?>