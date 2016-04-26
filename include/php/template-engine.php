<?php

    //TODO: move to vendor
    class           TemplateEngine
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
        *   Setting default page variables.
        */
        public function         set_default_page_vars()
        {
            $this->PageVars[ 'title' ] = 'Page title';
        }

        /**
        *   Place variables into the page.
        */
        private function        process_substitutions( &$Content )
        {
            do
            {
                $Count = 0;
                $Matches = array();
                $Count = preg_match_all( "/\{([a-zA-Z_\-\/@]+)\}/" , $Content , $Matches );

                for( $i = 0 ; $i < count( $Matches[ 1 ] ) ; $i++ )
                {
                    $Route = explode( '/' , $Matches[ 1 ][ $i ] );
                    $Instance = new Application();
                    $Subtitution = $Instance->parse_route( $Route );
                    $Content = str_replace( '{'.$Matches[ 1 ][ $i ].'}' , $Subtitution , $Content );
                }
            }
            while( $Count );
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

            $this->process_substitutions( $Content );
        }
    }

?>