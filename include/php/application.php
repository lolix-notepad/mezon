<?php

    global          $MEZON_PATH;
    require_once( $MEZON_PATH.'/include/php/template-engine.php' );

    /**
    *   Base class of the application.
    */
    class           Application
    {
        public static           $Instance = false;

        // TODO: make a template engine object with singleton
        public static           $Engine = false;

        /**
        *   Singleton сonstructor.
        */
        function __construct()
        {
            if( self::$Instance === false )
            {
                self::$Instance = $this;

                self::$Engine = new TemplateEngine();
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
        *   Processing controller.
        */
        private function        process_controller_call( $RoutePart , $Method = 'run' )
        {
            global          $MEZON_PATH;
            require_once( $MEZON_PATH.'/include/php/'.$RoutePart.'-controller.php' );
            $ControllerName = $this->get_class_name( $RoutePart , 'Controller' );
            $ControllerObject = new $ControllerName();
            $ControllerObject->run();
        }

        /**
        *   Processing view.
        */
        private function        process_view_call( $RoutePart , $Method = 'run' )
        {
            global          $MEZON_PATH;
            require_once( $MEZON_PATH.'/include/php/'.$RoutePart.'-view.php' );
            $ViewName = $this->get_class_name( $RoutePart , 'View' );
            $ViewObject = new $ViewName();

            if( method_exists( $ViewObject , $Method ) )
            {
                return( $ViewObject->$Method() );
            }
            else
            {
                return( $ViewObject->virtual( $Method ) );
            }
        }

        /**
        *   Обработка четырёхкомпонентных маршрутов. bundle-name - название папки в /include/php/
        *
        *   @example domain.com/bundle-name/[view|controller|route]/class-name/class-action
        */
        public function         parse_exact_route( $Route )
        {
            // TODO: реализовать
        }

        /**
        *   Обработка трёхкомпонентных маршрутов. bundle-name - название папки в /include/php/
        *
        *   @example domain.com/[view|controller|bundle-name|route]/class-name/class-action
        */
        public function         parse_complex_route( $Route )
        {
            $Content = '';

            if( $Route[ 0 ] == 'controller' )
            {
                $this->process_controller_call( $Route[ 1 ] , $Route[ 2 ] );
            }

            if( $Route[ 0 ] == 'view' )
            {
                $Content = $this->process_view_call( $Route[ 1 ] , $Route[ 2 ] );
            }

            // TODO: реализовать bundle-name/class-name/class-action

            return( $Content );
        }

        /**
        *   Обработка двухкомпонентных маршрутов.
        *
        *   @example domain.com/[view|controller|bundle-name|route]/class-name
        */
        public function         parse_common_route( $Route )
        {
            $Route [] = 'run';

            // TODO: domain.com/class-name/action-name/

            return( $this->parse_complex_route( $Route ) );
        }

        /**
        *   Обработка однокомпонентных маршрутов.
        *
        *   @example domain.com/class-name/[will be defaulted to "run"]
        */
        public function         parse_simple_route( $Route )
        {
            $this->process_controller_call( $Route[ 0 ] , 'run' );

            return( $this->process_view_call( $Route[ 0 ] , 'run' ) );
        }

        /**
        *   Обработка видов и контроллеров.
        */
        private function        parse_controller_view_route( $Route )
        {
            switch( count( $Route ) )
            {
                case( 1 ): 
                    if( method_exists( $this , $Route[ 0 ] ) )
                    {
                        $MethodName = $Route[ 0 ];
                        return( $this->$MethodName() );
                    }
                    else
                    {
                        return( $this->parse_simple_route( $Route ) );
                    }
                break;
                case( 2 ): return( $this->parse_common_route( $Route ) ); break;
                case( 3 ): return( $this->parse_complex_route( $Route ) ); break;
                case( 4 ): return( $this->parse_exact_route( $Route ) ); break;
                default: throw( new Exception( 'Illegal route : '.$_GET[ 'r' ] ) ); break;
            }
        }

        /**
        *   Обрабатываем подготовленый роут.
        */
        public function         parse_route( $Route )
        {
            if( count( $Route ) && $Route[ 0 ] == 'conf' )
            {
                return( get_compiled_config_value( array_slice( $Route , 1 ) ) );
            }

            return( $this->parse_controller_view_route( $Route ) );
        }

        /**
        *   Главная страница.
        */
        public function         index()
        {
            return( $this->parse_simple_route( array( 'index' ) ) );
        }

        /**
        *   Обрабатываем роут.
        */
        public function         run()
        {
            $Route = explode( '/' , trim( @$_GET[ 'r' ] , '/' ) );

            $Content = $this->parse_route( $Route );

            self::$Engine->compile_page_vars( $Content );

            print( $Content );
        }
    }

?>