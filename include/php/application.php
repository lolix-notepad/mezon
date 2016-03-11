<?php

    global          $MEZON_PATH;
    require_once( $MEZON_PATH.'/include/php/template-engine.php' );

    //TODO: implement /class_name/action routes and class lookup with name 'class_name' in %mezon-path%/vendor/ 
    //TODO: implement class lookup with name 'class_name' in %mezon-path%/vendor/bundle-name for routes /bundle/class/action/
    //TODO: illegal routes must return 404 code but not output exception description

    /**
    *   Base class of the application.
    */
    class           Application
    {
        /**
        *   Single instance of the class.
        */
        private static           $Instance = false;

        /**
        *   Singleton сonstructor.
        */
        function __construct()
        {
            if( self::$Instance === false )
            {
                self::$Instance = $this;
            }
        }

        /**
        *   Transfoming route in a class name.
        */
        private function        get_class_name( $RoutePart , $Suffix )
        {
            $Class = strtoupper( substr( $RoutePart , 0 , 1 ) ).substr( $RoutePart , 1 ).$Suffix;

            while( strpos( $Class , '-' ) !== false )
            {
                $Position = strpos( $Class , '-' );
                $Class = substr( $Class , 0 , $Position ).
                         strtoupper( substr( $Class , $Position + 1 , 1 ) ).
                         substr( $Class , $Position + 2 );
            }

            return( $Class );
        }

        /**
        *   Processing views and controllers.
        */
        private function        call_route( $Route )
        {
            switch( count( $Route ) )
            {
                case( 1 ): 
                    if( method_exists( $this , 'action_'.$Route[ 0 ] ) )
                    {
                        $MethodName = 'action_'.$Route[ 0 ];
                        return( $this->$MethodName() );
                    }
                    else
                    {
                        throw( new Exception( 'Illegal route : '.$_GET[ 'r' ] ) );
                    }
                break;
                default: throw( new Exception( 'Illegal route : '.$_GET[ 'r' ] ) ); break;
            }
        }

        /**
        *   Processing prepared route.
        */
        public function         parse_route( $Route )
        {
            if( count( $Route ) && $Route[ 0 ] == 'conf' )
            {
                return( get_compiled_config_value( array_slice( $Route , 1 ) ) );
            }

            return( $this->call_route( $Route ) );
        }

        /**
        *   Main page.
        */
        public function         action_index()
        {
            return( $this->parse_simple_route( array( 'index' ) ) );
        }

        /**
        *   Processing route.
        */
        public function         run()
        {
            try
            {
                $Route = explode( '/' , trim( @$_GET[ 'r' ] , '/' ) );

                $Content = $this->parse_route( $Route );

                $Engine = new TemplateEngine();
                $Engine->compile_page_vars( $Content );

                print( $Content );
            }
            catch( Exception $e )
            {
                print( '<pre>'.$e );
            }
        }
    }

?>