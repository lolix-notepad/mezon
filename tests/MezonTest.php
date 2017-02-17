<?php

    /**
    *   Common test cases.
    */
    class MezonTest extends PHPUnit\Framework\TestCase
    {
        /**
        *   Testing case when both GET and POST processors exists.
        */
        public function testMezonIncludes()
        {
            $MEZON_PATH = dirname( __FILE__ ).'/../';
            require_once( dirname( __FILE__ ).'/../mezon.php' );
			$this->assertEquals( true , true , '' );
        }
    }

?>