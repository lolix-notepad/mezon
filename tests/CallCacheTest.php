<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/call-cache/call-cache.php' );

    class CallCacheTest extends PHPUnit\Framework\TestCase
    {
        /**
        *   Testing successfull cache put.
        */
        public function testCacheSet()
        {
            CallCache::put( 'test' , 'test value' , '1' , '2' , '3' );

            $Value = CallCache::get( 'test' , '1' , '2' , '3' );

            $this->assertEquals( $Value , 'test value' , 'Data was not put' );
        }

        /**
        *   Testing cache miss.
        */
        public function testCacheMiss1()
        {
            CallCache::put( 'test' , 'test value' , '1' , '2' , '3' );

            $Value = CallCache::get( 'test2' , '1' , '2' , '3' );

            $this->assertEquals( $Value , false , 'Data was not put' );
        }

        /**
        *   Testing cache miss.
        */
        public function testCacheMiss2()
        {
            CallCache::put( 'test' , 'test value' , '1' , '2' , '3' );

            $Value = CallCache::get( 'test' , 'xxx' , '2' , '3' );

            $this->assertEquals( $Value , false , 'Data was not put' );
        }
    }

?>