<?php

	require_once( dirname( __FILE__ ).'/../template-engine/template-engine.php' );

	/**
	*	Template engine class.
	*/
	class			SimpleOutputTemplate extends TemplateEngine
	{
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
        *   Compile template.
        */
        function            compile()
        {
			$this->compile_page_vars( $this->Template );

            return( $this->Template );
        }
	}

?>