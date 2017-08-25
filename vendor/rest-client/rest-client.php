<?php

    /**
    *   REST client class.
    */
    class           RESTClient
    {
        /**
		*	Server host.
		*/
		protected			$URL = false;

		/**
		*	Session id.
		*/
		protected			$SessionId = false;

		/**
		*	Constructor.
		*/
		function __construct( $URL )
		{
			$this->URL = $URL;
		}

		/**
		*	Method sends POST request to REST server.
		*/
		function post_request( $Endpoint , $Data )
		{
			$URL = $this->URL.$Endpoint;

			$Options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST' , 
					'content' => http_build_query( $Data )
				)
			);
			$Context = stream_context_create( $Options );
			return( json_decode( file_get_contents( $URL , false , $Context ) ) );
		}

		/**
		*	Method sends GET request to REST server.
		*/
		function get_request( $Endpoint )
		{
			$URL = $this->URL.$Endpoint;

			$Options = array(
				'http' => array(
					'header'  => "Cgi-Authorization: Basic ".$this->SessionId , 
					'method'  => 'GET'
				)
			);

			$Context = stream_context_create( $Options );
			return( json_decode( file_get_contents( $URL , false , $Context ) ) );
		}
    }

?>