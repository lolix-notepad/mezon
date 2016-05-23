<?php

	require_once( dirname( __FILE__ ).'/../template-engine/template-engine.php' );
	require_once( dirname( __FILE__ ).'/../template-resources/template-resources.php' );

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
        *   Loaded resources.
        */
        private $Resources =  false;

		/**
        *   Template ñonstructor.
        */
        function			__construct()
        {
            $this->Template = file_get_contents( dirname( __FILE__ ).'/res/template/index.tpl' );

            $this->Resources = new TemplateResources();
        }

        /**
        *   Method returns compiled page resources.
        */
        private function    get_resources()
        {
            $CSSFiles = $this->Resources->get_css_files();

            $Content = '';

            foreach( $CSSFiles as $i => $CSSFile )
            {
                $Content .= '
        <link href="'.$CSSFile.'" rel="stylesheet" type="text/css">';
            }

            return( $Content );
        }

        /**
        *   Compile template.
        */
        function            compile()
        {
            $this->set_page_var( 'resources' , $this->get_resources() );

			$this->compile_page_vars( $this->Template );

            return( $this->Template );
        }
	}

?>