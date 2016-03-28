<?php

    /**
    *   Router class.
    */
    class           Router
    {
        /**
        *   Single instance of the class.
        */
        private static           $Instance = false;

        /**
        *   Mapping of routes to their execution functions.
        */
        private                  $Routes;

        /**
        *   Singleton ñonstructor.
        */
        function __construct()
        {
            if( self::$Instance === false )
            {
                self::$Instance = $this;
            }

            $this->Routes = array();
        }

        /**
        *   Transfoming the path of the route in the class name.
        */
        private function        get_class_name( $RoutePart )
        {
            $ClassName = strtoupper( substr( $RoutePart , 0 , 1 ) ).substr( $RoutePart , 1 );

            while( strpos( $ClassName , '-' ) !== false )
            {
                $Position = strpos( $ClassName , '-' );
                $ClassName = substr( $ClassName , 0 , $Position ).
                         strtoupper( substr( $ClassName , $Position + 1 , 1 ) ).
                         substr( $ClassName , $Position + 2 );
            }

            return( $ClassName );
        }

        /**
        *   Processing one component routes.
        *
        *   @example http://example.com/action-name/
        */
        public function        process_one_component_route( $Object , $Route )
        {
            if( method_exists( $Object , 'action_'.$Route[ 0 ] ) )
            {
                $MethodName = 'action_'.$Route[ 0 ];
                return( $Object->$MethodName() );
            }
            else
            {
                throw( new Exception( 'Illegal route : '.$_GET[ 'r' ] ) );
            }
        }

        /**
        *   Processing one component routes.
        *
        *   @example http://example.com/class-name/action-name/
        */
        public function        process_two_component_route( $Route )
        {
            if( file_exists( $MEZON_PATH.'/vendor/'.$Route[ 0 ].'.php' ) )
            {
                require_once( $MEZON_PATH.'/vendor/'.$Route[ 0 ].'.php' );
            }
            else
            {
                throw( new Exception( 'File : '.$Route[ 0 ].' not found' ) );
            }

            $ClassName = $this->get_class_name( $Route[ 0 ] );
            $Object = new $ClassName;

            if( method_exists( $Object , 'action_'.$Route[ 1 ] ) )
            {
                $MethodName = 'action_'.$Route[ 1 ];
                return( $Object->$MethodName() );
            }
            else
            {
                throw( new Exception( 'Method : '.$MethodName.' was not found in the class '.$ClassName ) );
            }
        }

        /**
        *   Method fetches actions from the objects and creates routes for them.
        */
        public function         fetch_actions( $Object )
        {
            $Methods = get_class_methods( $Object );

            foreach( $Methods as $i => $Method )
            {
                if( strpos( $Method , 'action_' ) === 0 )
                {
                    $Route = str_replace( array( 'action_' , '_' ) , array( '' , '-' ) , $Method );
                    $this->Routes[ "/$Route/" ] = array( $Object , $Method );
                }
            }
        }

        /**
        *   Method adds route and it's handler.
        */
        public function         add_route( $Route , $Callback )
        {
            $Route = '/'.trim( $Route , '/' ).'/';

            $this->Routes[ $Route ] = $Callback;
        }

        /**
        *   Processing specified router.
        */
        public function        call_route( $Route )
        {
            if( is_array( $Route ) )
            {
                $Route = implode( '/' , $Route );
            }

            $Route = '/'.trim( $Route , '/' ).'/';

            foreach( $this->Routes as $i => $Processor )
            {
                if( $i == $Route )
                {
                    return( call_user_func( $Processor ) );
                }
            }

            throw( new Exception( 'The processor was not found for the route '.$Route ) );
        }
    }

?>