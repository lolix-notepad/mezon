<?php

    class RouterTest extends PHPUnit_Framework_TestCase
    {
        /**
        *   Testing simple default 'index' one component route.
        */
        public function testIndexRouter()
        {
            $URL = 'http://gdzone.ru/mezon-mvc';

            $Content = file_get_contents( $URL.'/doc/examples/hello-world/' );

            $this->expectOutputString( 'Hello world!' , 'Invalid index route' );

            print( $Content );
        }

        /**
        *   Testing simple custom one component route.
        */
        public function testCustomRoute()
        {
            $URL = 'http://gdzone.ru/mezon-mvc';

            $Content = file_get_contents( $URL.'/doc/examples/simple-site/contacts/' );

            $this->expectOutputString( 'This is the "Contacts" page' , 'Invalid contacts route' );

            print( $Content );
        }

        /**
        *   Testing unexisting route behaviour.
        */
        public function testUnexistingRoute()
        {
            $URL = 'http://gdzone.ru/mezon-mvc';

            $Content = file_get_contents( $URL.'/doc/examples/simple-site/unexisting-route/' );

            $this->assertNotFalse( strpos( $Content , "exception 'Exception' with message 'Illegal route :" ) , 'Exception expected' );
        }
    }

?>