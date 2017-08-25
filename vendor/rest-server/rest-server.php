<?php

	require_once( dirname( __FILE__ ).'/../common-application/common-application.php' );
	require_once( dirname( __FILE__ ).'/../rest-template/rest-template.php' );

	/**
	*	REST Server class.
	*/
	class			RESTServer extends CommonApplication
	{
		/**
        *   Constructor.
        */
        function			__construct()
        {
			parent::__construct( new RESTTemplate() );
        }

		/**
		*	Fetching auth token from headers.
		*/
		protected function get_session_id_from_headers( $Headers )
		{
			if( isset( $Headers[ 'Authorization' ] ) )
			{
				$Token = str_replace( 'Basic ' , '' , $Headers[ 'Authorization' ] );

				return( $Token );
			}
			elseif( isset( $Headers[ 'Cgi-Authorization' ] ) )
			{
				$Token = str_replace( 'Basic ' , '' , $Headers[ 'Cgi-Authorization' ] );

				return( $Token );
			}

			throw( new Exception( 'No token' ) );
		}

		/**
		*	Method returns session id from HTTP header.
		*/
		protected function get_session_id()
		{
			$Headers = getallheaders();

			return( $this->get_session_id_from_headers( $Headers ) );
		}

		/**
		*	Method compiles responce.
		*/
		protected function response( $ResponceData )
		{
			return(
				array(
					'response' => json_encode(
						$ResponceData
					)
				)
			);
		}
	}

?>