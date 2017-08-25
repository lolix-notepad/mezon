<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/rest-server/rest-server.php' );

	class MockRESTServer extends RESTServer
	{
		public function mock_get_session_id_from_headers( $Headers )
		{
			return( $this->get_session_id_from_headers( $Headers ) );
		}
	}
	
    class RESTServerTest extends PHPUnit\Framework\TestCase
    {
        /**
        *   Testing common auth header.
        */
        public function testCommonHeader()
        {
            $Server = new MockRESTServer();
			$Token = $Server->mock_get_session_id_from_headers( 
				array( 'Authorization' => 'Basic 12345' )
			);

            $this->assertEquals( $Token , '12345' , 'Invalid token from the common auth header' );
        }

		/**
        *   Testing custom auth header.
        */
        public function testCustomHeader()
        {
            $Server = new MockRESTServer();
			$Token = $Server->mock_get_session_id_from_headers( 
				array( 'Cgi-Authorization' => 'Basic 12345' )
			);

            $this->assertEquals( $Token , '12345' , 'Invalid token from the common auth header' );
        }

		/**
        *   Testing header not found.
        */
        public function testTokenNotFoundException()
        {
            $Server = new MockRESTServer();
			try
			{
				$Token = $Server->mock_get_session_id_from_headers( 
					array( 'some header' => 'Basic 12345' )
				);
				$Flag = 'no exception';
			}
			catch( Exception $e )
			{
				$Flag = 'exception';
			}

            $this->assertEquals( $Flag , 'exception' , 'Invalid error behaviour' );
        }
    }

?>