<?php

	require_once( dirname( __FILE__ ).'/../template-engine/template-engine.php' );

	/**
	*	Template engine class.
	*/
	class			BasicTemplate extends TemplateEngine
	{
		/**
		*	Loaded template content.
		*/
		private	$Template = false;

		/**
        *   Template ñonstructor.
        */
        function			__construct()
        {
            $this->Template = file_get_contents( dirname( __FILE__ ).'/res/template/index.tpl' );
        }

		/**
        *   Convert to string.
        */
		function			__toString()
		{
			$this->compile_page_vars( $this->Template );

			return( $this->Template );
		}
	}

?>