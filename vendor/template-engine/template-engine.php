<?php

    require_once( dirname( dirname( dirname( __FILE__ ) ) ).'/conf/conf.php' );
    require_once( $MEZON_PATH.'/vendor/singleton/singleton.php' );

	/**
	*	Template engine class.
	*/
    class           TemplateEngine extends Singleton
    {
        /**
        *   Page variables.
        */
        private $PageVars = array();

        /**
        *   Setting page variables.
        */
        public function         set_page_var( $Var , $Value )
        {
            $this->PageVars[ $Var ] = $Value;
        }

        /**
        *   Compiling the page with it's variables.
        */
        public function         compile_page_vars( &$Content )
        {
            foreach( $this->PageVars as $Key => $Value )
            {
                $Content = str_replace( '{'.$Key.'}' , $Value , $Content );
            }
        }
    }

?>