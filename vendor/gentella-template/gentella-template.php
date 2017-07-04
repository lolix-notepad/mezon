<?php

	require_once( dirname( __FILE__ ).'/../template-engine/template-engine.php' );
	require_once( dirname( __FILE__ ).'/../template-resources/template-resources.php' );

	/**
	*	Template engine class.
	*/
	class			GentellaTemplate extends TemplateEngine
	{
		/**
		*	Loaded template content.
		*/
		private	$Template = false;

        /**
        *   Loaded resources.
        */
        private $Resources =  false;

		/**
        *   Template сonstructor.
        */
        function				__construct()
        {
            $this->Template = file_get_contents( dirname( __FILE__ ).'/gentella-template/index.html' );

            $this->Resources = new TemplateResources();
        }

        /**
        *   Method returns compiled page resources.
        */
        private function    	get_resources()
        {
            $Content = '';

            $CSSFiles = $this->Resources->get_css_files();
            foreach( $CSSFiles as $i => $CSSFile )
            {
                $Content .= '
        <link href="'.$CSSFile.'" rel="stylesheet" type="text/css">';
            }

            $JSFiles = $this->Resources->get_js_files();
            foreach( $JSFiles as $i => $JSFile )
            {
                $Content .= '
        <script src="'.$JSFile.'"></script>';
            }

            return( $Content );
        }

        /**
        *   Compile template.
        */
        function            	compile()
        {
            $this->set_page_var( 'resources' , $this->get_resources() );
            $this->set_page_var( 'mezon-http-path' , get_config_value( '@mezon-http-path' ) );

			$this->compile_page_vars( $this->Template );

            return( $this->Template );
        }

		/**
		*	Method compiles success message content.
		*/
		public static function	success_message_content( $Message )
		{
			return(
				'<div class="x_content" style="margin: 0; padding: 0;">'.
				'<div class="alert alert-success alert-dismissible fade in" role="alert">'.
				'<button type="button" class="close" data-dismiss="alert" aria-label="Close">'.
				'<span aria-hidden="true">×</span></button>'.$Message.'</div></div>'
			);
		}

		/**
		*	Method compiles info message content.
		*/
		public static function	info_message_content( $Message )
		{
			return(
				'<div class="x_content" style="margin: 0; padding: 0;">'.
				'<div class="alert alert-info alert-dismissible fade in" role="alert">'.
				'<button type="button" class="close" data-dismiss="alert" aria-label="Close">'.
				'<span aria-hidden="true">×</span></button>'.$Message.'</div></div>'
			);
		}

		/**
		*	Method compiles warning message content.
		*/
		public static function	warning_message_content( $Message )
		{
			return(
				'<div class="x_content" style="margin: 0; padding: 0;">'.
				'<div class="alert alert-warning alert-dismissible fade in" role="alert">'.
				'<button type="button" class="close" data-dismiss="alert" aria-label="Close">'.
				'<span aria-hidden="true">×</span></button>'.$Message.'</div></div>'
			);
		}

		/**
		*	Method compiles danger message content.
		*/
		public static function	danger_message_content( $Message )
		{
			return(
				'<div class="x_content" style="margin: 0; padding: 0;">'.
				'<div class="alert alert-danger alert-dismissible fade in" role="alert">'.
				'<button type="button" class="close" data-dismiss="alert" aria-label="Close">'.
				'<span aria-hidden="true">×</span></button>'.$Message.'</div></div>'
			);
		}
	}

?>