<?php

	require_once( dirname( __FILE__ ).'/../template-engine/template-engine.php' );

	/**
	*	Template engine class.
	*/
	class			SimpleOutputTemplate extends TemplateEngine
	{
		/**
		*	HTTP headers.
		*/
		private $Headers = false;

		/**
		*	Loaded template content.
		*/
		private	$Template = false;

		/**
        *   Template ñonstructor.
        */
        function			__construct( $PlaceHolder )
        {
            $this->Template = $PlaceHolder;
        }

		/**
		*	Method adds header.
		*/
		function			add_header( $Header , $Value )
		{
			if( $this->Headers === false )
			{
				$this->Headers = array();
			}

			$this->Headers[ $Header ] = $Value;
		}

		/**
		*	Method returns all template's headers.
		*/
		function			get_headers()
		{
			return( $this->Headers );
		}

		/**
		*	Method outputs all template's headers.
		*/
		private function	output_headers()
		{
			if( $this->Headers !== false )
			{
				foreach( $this->Headers as $Header => $Value )
				{
					header( "$Header: $Value" );
				}
			}
		}

        /**
        *   Compile template.
        */
        function            compile()
        {
			$this->output_headers();

			$this->compile_page_vars( $this->Template );

            return( $this->Template );
        }
	}

?>