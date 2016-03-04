<?php

    class           TemplateEngine
    {
        private $PageVars = array();

        public function         set_page_var( $Var , $Value )
        {
            $this->PageVars[ $Var ] = $Value;
        }

        public function         set_default_page_vars()
        {
        }

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
                    $Subtitution = Application::$Instance->parse_route( $Route );
                    $Content = str_replace( '{'.$Matches[ 1 ][ $i ].'}' , $Subtitution , $Content );
                }
            }
            while( $Count );
        }

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